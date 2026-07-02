<?php

namespace App\Services;

use App\Models\ShippingZone;

class ShippingService
{
    /** @return array{serviceable: bool, shipping_fee: int, zone_name: ?string, message: string, free_shipping_applied: bool, free_shipping_min: ?int, amount_until_free_shipping: ?int, is_maharashtra: ?bool} */
    public function quote(string $countryCode, string $postalCode, ?string $state, int $subtotal): array
    {
        $countryCode = strtoupper(trim($countryCode));
        $postalCode = $this->normalizePostal($postalCode);
        $state = $state ? trim($state) : null;

        if ($countryCode === '' || $postalCode === '') {
            return $this->unserviceable('Enter your pin / postal code to check delivery.');
        }

        $zones = ShippingZone::query()
            ->where('is_active', true)
            ->where('country_code', $countryCode)
            ->get();

        if ($zones->isEmpty()) {
            return $this->unserviceable('We do not ship to this country yet.');
        }

        $best = null;
        $bestScore = -1;

        foreach ($zones as $zone) {
            $score = $this->matchScore($zone, $postalCode, $state);

            if ($score < 0) {
                continue;
            }

            $score += $zone->priority;

            if ($score > $bestScore) {
                $bestScore = $score;
                $best = $zone;
            }
        }

        if (! $best) {
            return $this->unserviceable('Sorry — we do not deliver to this pin / postal code yet.');
        }

        if (! $best->is_serviceable) {
            return $this->unserviceable('Sorry — this location is not serviceable at the moment.');
        }

        $fee = $best->shipping_fee;
        $freeApplied = false;
        $freeMin = $best->free_shipping_min;
        $isMaharashtra = null;
        $amountUntilFree = null;

        if ($countryCode === 'IN') {
            $isMaharashtra = $this->isMaharashtra($postalCode, $state);
            $fee = $isMaharashtra
                ? (int) config('shipping.india.fee_maharashtra', 59)
                : (int) config('shipping.india.fee_outside_maharashtra', 99);
            $freeMin = (int) config('shipping.india.free_shipping_min', 2000);

            if ($subtotal >= $freeMin) {
                $fee = 0;
                $freeApplied = true;
            } else {
                $amountUntilFree = $freeMin - $subtotal;
            }
        } elseif ($freeMin !== null && $subtotal >= $freeMin) {
            $fee = 0;
            $freeApplied = true;
        }

        $message = $this->deliveryMessage($countryCode, $freeApplied, $freeMin, $isMaharashtra, $amountUntilFree);

        return [
            'serviceable' => true,
            'shipping_fee' => $fee,
            'zone_name' => $best->name,
            'message' => $message,
            'free_shipping_applied' => $freeApplied,
            'free_shipping_min' => $freeMin,
            'amount_until_free_shipping' => $amountUntilFree,
            'is_maharashtra' => $isMaharashtra,
        ];
    }

    public function isMaharashtra(string $postalCode, ?string $state): bool
    {
        $postalCode = $this->normalizePostal($postalCode);

        if (preg_match('/^\d{6}$/', $postalCode)) {
            $prefix3 = (int) substr($postalCode, 0, 3);

            if ($prefix3 === 403) {
                return false;
            }

            return ($prefix3 >= 400 && $prefix3 <= 402)
                || ($prefix3 >= 404 && $prefix3 <= 445);
        }

        if ($state !== null) {
            $normalized = strtolower(trim($state));

            return in_array($normalized, ['maharashtra', 'mh'], true);
        }

        return false;
    }

    protected function deliveryMessage(
        string $countryCode,
        bool $freeApplied,
        ?int $freeMin,
        ?bool $isMaharashtra,
        ?int $amountUntilFree,
    ): string {
        if ($countryCode !== 'IN') {
            return $freeApplied
                ? 'Free shipping applied for your order total.'
                : 'We deliver to your location.';
        }

        if ($freeApplied) {
            return 'Free shipping applied — your order is above ₹'.number_format((int) $freeMin).'.';
        }

        $region = $isMaharashtra ? 'Maharashtra' : 'India';
        $fee = $isMaharashtra
            ? (int) config('shipping.india.fee_maharashtra', 59)
            : (int) config('shipping.india.fee_outside_maharashtra', 99);

        $message = "We deliver within {$region}. Shipping ₹{$fee}.";

        if ($amountUntilFree !== null && $amountUntilFree > 0) {
            $message .= ' Add order for ₹'.number_format($amountUntilFree).' or more to get shipping free.';
        }

        return $message;
    }

    protected function matchScore(ShippingZone $zone, string $postalCode, ?string $state): int
    {
        $value = $zone->match_value ? trim($zone->match_value) : '';

        return match ($zone->match_type) {
            ShippingZone::MATCH_POSTAL_EXACT => strcasecmp($postalCode, $value) === 0 ? 1000 : -1,
            ShippingZone::MATCH_POSTAL_PREFIX => ($value !== '' && str_starts_with($postalCode, $value))
                ? 800 + strlen($value)
                : -1,
            ShippingZone::MATCH_STATE => ($state !== null && strcasecmp($state, $value) === 0) ? 500 : -1,
            ShippingZone::MATCH_COUNTRY => 100,
            default => -1,
        };
    }

    /** @return array{serviceable: bool, shipping_fee: int, zone_name: ?string, message: string, free_shipping_applied: bool, free_shipping_min: ?int, amount_until_free_shipping: ?int, is_maharashtra: ?bool} */
    protected function unserviceable(string $message): array
    {
        return [
            'serviceable' => false,
            'shipping_fee' => 0,
            'zone_name' => null,
            'message' => $message,
            'free_shipping_applied' => false,
            'free_shipping_min' => null,
            'amount_until_free_shipping' => null,
            'is_maharashtra' => null,
        ];
    }

    public function normalizePostal(string $postalCode): string
    {
        return strtoupper(preg_replace('/\s+/', '', trim($postalCode)) ?? '');
    }

    /** @return array<string, string> */
    public function availableCountries(): array
    {
        $codes = ShippingZone::query()
            ->where('is_active', true)
            ->distinct()
            ->orderBy('country_code')
            ->pluck('country_code');

        $labels = [
            'IN' => 'India',
            'AE' => 'United Arab Emirates',
            'US' => 'United States',
            'GB' => 'United Kingdom',
            'SG' => 'Singapore',
            'AU' => 'Australia',
            'CA' => 'Canada',
        ];

        return $codes->mapWithKeys(fn (string $code) => [
            $code => $labels[$code] ?? $code,
        ])->all();
    }
}
