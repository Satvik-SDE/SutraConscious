<?php

namespace App\Services;

use App\Models\Order;
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

    public function createOrder(Order $order): ?string
    {
        if (! $this->api) {
            return null;
        }

        $rzpOrder = $this->api->order->create([
            'amount' => $order->total * 100,
            'currency' => $order->currency,
            'receipt' => $order->number,
            'notes' => [
                'order_number' => $order->number,
                'customer_email' => $order->customer_email,
            ],
        ]);

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
        } catch (\Throwable $e) {
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
        } catch (\Throwable $e) {
            return false;
        }
    }
}
