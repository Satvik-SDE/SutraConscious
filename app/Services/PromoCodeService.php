<?php

namespace App\Services;

use App\Models\Order;
use App\Models\PromoCode;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class PromoCodeService
{
    public const SESSION_KEY = 'checkout_promo_code';

    public function normalizeCode(string $code): string
    {
        return strtoupper(trim($code));
    }

    public function getAppliedCode(): ?string
    {
        $code = Session::get(self::SESSION_KEY);

        return is_string($code) && $code !== '' ? $code : null;
    }

    public function applyToSession(string $code): void
    {
        Session::put(self::SESSION_KEY, $this->normalizeCode($code));
    }

    public function clearSession(): void
    {
        Session::forget(self::SESSION_KEY);
    }

    public function findByCode(string $code): ?PromoCode
    {
        return PromoCode::query()
            ->where('code', $this->normalizeCode($code))
            ->first();
    }

    public function validate(PromoCode $promo, int $subtotal): void
    {
        if (! $promo->is_active) {
            throw ValidationException::withMessages([
                'promo_code' => 'This promo code is no longer active.',
            ]);
        }

        if ($promo->starts_at !== null && now()->lt($promo->starts_at)) {
            throw ValidationException::withMessages([
                'promo_code' => 'This promo code is not valid yet.',
            ]);
        }

        if ($promo->expires_at !== null && now()->gt($promo->expires_at)) {
            throw ValidationException::withMessages([
                'promo_code' => 'This promo code has expired.',
            ]);
        }

        if ($promo->max_uses !== null && $promo->used_count >= $promo->max_uses) {
            throw ValidationException::withMessages([
                'promo_code' => 'This promo code has reached its usage limit.',
            ]);
        }

        if ($promo->min_order_amount !== null && $subtotal < $promo->min_order_amount) {
            throw ValidationException::withMessages([
                'promo_code' => 'Minimum order of ₹'.number_format($promo->min_order_amount).' required for this code.',
            ]);
        }
    }

    /** @return array{discount_total: int, shipping_total: int, label: string} */
    public function apply(PromoCode $promo, int $subtotal, int $shippingFee): array
    {
        return match ($promo->type) {
            PromoCode::TYPE_PERCENT => [
                'discount_total' => (int) min($subtotal, (int) round($subtotal * $promo->value / 100)),
                'shipping_total' => $shippingFee,
                'label' => $promo->value.'% off',
            ],
            PromoCode::TYPE_FIXED => [
                'discount_total' => min($promo->value, $subtotal),
                'shipping_total' => $shippingFee,
                'label' => '₹'.number_format($promo->value).' off',
            ],
            PromoCode::TYPE_FREE_SHIPPING => [
                'discount_total' => 0,
                'shipping_total' => 0,
                'label' => 'Free shipping',
            ],
            default => [
                'discount_total' => 0,
                'shipping_total' => $shippingFee,
                'label' => '',
            ],
        };
    }

    public function findAndValidate(string $code, int $subtotal): PromoCode
    {
        $promo = $this->findByCode($code);

        if (! $promo) {
            throw ValidationException::withMessages([
                'promo_code' => 'Invalid promo code.',
            ]);
        }

        $this->validate($promo, $subtotal);

        return $promo;
    }

    public function recordUsage(Order $order): void
    {
        if ($order->promo_code_id === null) {
            return;
        }

        PromoCode::query()
            ->whereKey($order->promo_code_id)
            ->increment('used_count');
    }
}
