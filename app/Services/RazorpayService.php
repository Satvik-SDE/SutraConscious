<?php

namespace App\Services;

use App\Models\Order;
use InvalidArgumentException;
use Razorpay\Api\Api;

class RazorpayService
{
    protected ?Api $api = null;

    public function __construct()
    {
        $key = config('services.razorpay.key');
        $secret = config('services.razorpay.secret');

        if ($key && $secret) {
            $this->api = new Api($key, $secret);
        }
    }

    public function isConfigured(): bool
    {
        return $this->api !== null;
    }

    public function publicKey(): ?string
    {
        return config('services.razorpay.key');
    }

    /**
     * @return array{id: string, amount: int, currency: string}
     */
    public function createPaymentOrder(int $amountPaise, string $currency = 'INR', ?string $receipt = null): array
    {
        if (! $this->api) {
            throw new InvalidArgumentException('Razorpay is not configured.');
        }

        if ($amountPaise < 100) {
            throw new InvalidArgumentException('Amount must be at least 100 paise.');
        }

        $payload = [
            'amount' => $amountPaise,
            'currency' => $currency,
        ];

        if ($receipt !== null && $receipt !== '') {
            $payload['receipt'] = $receipt;
        }

        $created = $this->api->order->create($payload);

        return [
            'id' => $created['id'],
            'amount' => (int) $created['amount'],
            'currency' => $created['currency'],
        ];
    }

    public function createOrder(Order $order): ?string
    {
        if (! $this->api) {
            return null;
        }

        $amountPaise = (int) round($order->total * 100);

        $rzpOrder = $this->createPaymentOrder($amountPaise, $order->currency, $order->number);

        $order->update([
            'payment_provider' => 'razorpay',
            'razorpay_order_id' => $rzpOrder['id'],
        ]);

        return $rzpOrder['id'];
    }

    public function verifySignature(string $razorpayOrderId, string $razorpayPaymentId, string $razorpaySignature): bool
    {
        if (! $this->api) {
            return false;
        }

        try {
            $this->api->utility->verifyPaymentSignature([
                'razorpay_order_id' => $razorpayOrderId,
                'razorpay_payment_id' => $razorpayPaymentId,
                'razorpay_signature' => $razorpaySignature,
            ]);

            return true;
        } catch (\Throwable) {
            return false;
        }
    }

    public function verifyWebhookSignature(string $body, string $signature): bool
    {
        $secret = config('services.razorpay.webhook_secret');
        if (! $secret || ! $this->api) {
            return false;
        }

        try {
            $this->api->utility->verifyWebhookSignature($body, $signature, $secret);

            return true;
        } catch (\Throwable) {
            return false;
        }
    }
}
