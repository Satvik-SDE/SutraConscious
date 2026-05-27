<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\RazorpayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RazorpayWebhookController extends Controller
{
    public function __construct(protected RazorpayService $razorpay) {}

    public function handle(Request $request)
    {
        $body = $request->getContent();
        $signature = $request->header('X-Razorpay-Signature', '');

        if (! $this->razorpay->verifyWebhookSignature($body, $signature)) {
            Log::warning('razorpay.webhook.invalid_signature');
            return response('invalid signature', 401);
        }

        $payload = json_decode($body, true) ?: [];
        $event = $payload['event'] ?? null;
        $paymentEntity = $payload['payload']['payment']['entity'] ?? [];

        $razorpayOrderId = $paymentEntity['order_id'] ?? null;
        $razorpayPaymentId = $paymentEntity['id'] ?? null;

        if (! $razorpayOrderId) {
            return response('no order', 200);
        }

        $order = Order::where('razorpay_order_id', $razorpayOrderId)->first();
        if (! $order) {
            Log::warning('razorpay.webhook.order_not_found', ['razorpay_order_id' => $razorpayOrderId]);
            return response('order not found', 200);
        }

        if ($event === 'payment.captured' && $order->payment_status !== Order::PAYMENT_PAID) {
            $order->update([
                'razorpay_payment_id' => $razorpayPaymentId,
                'payment_status' => Order::PAYMENT_PAID,
                'status' => Order::STATUS_PROCESSING,
                'paid_at' => now(),
            ]);
        }

        if ($event === 'payment.failed' && $order->payment_status === Order::PAYMENT_UNPAID) {
            $order->update(['payment_status' => Order::PAYMENT_FAILED]);
        }

        return response('ok', 200);
    }
}
