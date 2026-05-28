<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\RazorpayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Razorpay\Api\Errors\Error as RazorpayError;

class RazorpayPaymentController extends Controller
{
    public function __construct(protected RazorpayService $razorpay) {}

    public function createOrder(Request $request): JsonResponse
    {
        if (! $this->razorpay->isConfigured()) {
            return response()->json(['message' => 'Razorpay credentials are not configured.'], 401);
        }

        $data = $request->validate([
            'amount' => ['required', 'integer', 'min:100'],
            'currency' => ['sometimes', 'string', 'size:3'],
            'receipt' => ['nullable', 'string', 'max:40'],
        ]);

        try {
            $rzpOrder = $this->razorpay->createPaymentOrder(
                $data['amount'],
                $data['currency'] ?? 'INR',
                $data['receipt'] ?? null,
            );

            return response()->json([
                'order_id' => $rzpOrder['id'],
                'amount' => (int) $rzpOrder['amount'],
                'currency' => $rzpOrder['currency'],
            ]);
        } catch (InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        } catch (RazorpayError $e) {
            $status = $e->getHttpStatusCode() === 401 ? 401 : 500;

            return response()->json(['message' => $e->getMessage()], $status);
        } catch (\Throwable) {
            return response()->json(['message' => 'Unable to create Razorpay order.'], 500);
        }
    }

    public function verifyPayment(Request $request): JsonResponse
    {
        if (! $this->razorpay->isConfigured()) {
            return response()->json(['message' => 'Razorpay credentials are not configured.'], 401);
        }

        $data = $request->validate([
            'razorpay_order_id' => ['required', 'string'],
            'razorpay_payment_id' => ['required', 'string'],
            'razorpay_signature' => ['required', 'string'],
        ]);

        $valid = $this->razorpay->verifySignature(
            $data['razorpay_order_id'],
            $data['razorpay_payment_id'],
            $data['razorpay_signature'],
        );

        if (! $valid) {
            return response()->json([
                'success' => false,
                'message' => 'Payment signature could not be verified.',
            ], 400);
        }

        return response()->json(['success' => true]);
    }
}
