<?php

namespace App\Services;

use App\Mail\NewPaidOrderMail;
use App\Models\Order;
use App\Models\ProductVariant;
use App\Services\PromoCodeService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OrderPaymentService
{
    public function __construct(protected PromoCodeService $promoCodes) {}

    public function markPaid(
        Order $order,
        ?string $razorpayPaymentId = null,
        ?string $razorpaySignature = null,
    ): bool {
        if ($order->payment_status === Order::PAYMENT_PAID) {
            return false;
        }

        $marked = false;

        DB::transaction(function () use ($order, $razorpayPaymentId, $razorpaySignature, &$marked) {
            $locked = Order::query()
                ->whereKey($order->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($locked->payment_status === Order::PAYMENT_PAID) {
                return;
            }

            $locked->loadMissing('items');

            foreach ($locked->items as $item) {
                $variant = ProductVariant::query()
                    ->whereKey($item->product_variant_id)
                    ->lockForUpdate()
                    ->first();

                if (! $variant) {
                    continue;
                }

                $deduct = min($variant->stock, $item->quantity);
                if ($deduct > 0) {
                    $variant->decrement('stock', $deduct);
                }

                if ($deduct < $item->quantity) {
                    Log::warning('orders.stock_insufficient_on_payment', [
                        'order_id' => $locked->id,
                        'order_number' => $locked->number,
                        'variant_id' => $variant->id,
                        'requested' => $item->quantity,
                        'deducted' => $deduct,
                    ]);
                }
            }

            $payload = [
                'payment_status' => Order::PAYMENT_PAID,
                'status' => Order::STATUS_PROCESSING,
                'paid_at' => now(),
            ];

            if ($razorpayPaymentId !== null) {
                $payload['razorpay_payment_id'] = $razorpayPaymentId;
            }

            if ($razorpaySignature !== null) {
                $payload['razorpay_signature'] = $razorpaySignature;
            }

            $locked->update($payload);
            $marked = true;
        });

        if ($marked) {
            $this->promoCodes->recordUsage($order->fresh());
            $this->notifyProcessingTeam($order->fresh(['items']));
        }

        return $marked;
    }

    public function notifyProcessingTeam(Order $order): void
    {
        $teamEmail = config('orders.team_email');

        if (! $teamEmail) {
            return;
        }

        if ($order->payment_status !== Order::PAYMENT_PAID || $order->team_notified_at !== null) {
            return;
        }

        DB::transaction(function () use ($order, $teamEmail) {
            $locked = Order::query()
                ->whereKey($order->id)
                ->whereNull('team_notified_at')
                ->where('payment_status', Order::PAYMENT_PAID)
                ->lockForUpdate()
                ->first();

            if (! $locked) {
                return;
            }

            $locked->loadMissing('items');

            try {
                Mail::to($teamEmail)->send(new NewPaidOrderMail($locked));
                $locked->update(['team_notified_at' => now()]);
            } catch (\Throwable $exception) {
                Log::error('orders.team_notification_failed', [
                    'order_id' => $locked->id,
                    'order_number' => $locked->number,
                    'message' => $exception->getMessage(),
                ]);
            }
        });
    }
}
