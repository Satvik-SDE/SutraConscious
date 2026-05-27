<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\CartService;
use App\Services\RazorpayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function __construct(
        protected CartService $cart,
        protected RazorpayService $razorpay,
    ) {}

    public function show()
    {
        $cart = $this->cart->current()->load(['items.variant.product']);

        if ($cart->isEmpty()) {
            return redirect()->route('cart.show')->with('status', 'Your cart is empty.');
        }

        return view('shop.checkout', [
            'cart' => $cart,
        ]);
    }

    public function place(Request $request)
    {
        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:32'],
            'shipping_line1' => ['required', 'string', 'max:255'],
            'shipping_line2' => ['nullable', 'string', 'max:255'],
            'shipping_city' => ['required', 'string', 'max:255'],
            'shipping_state' => ['required', 'string', 'max:255'],
            'shipping_country' => ['required', 'string', 'size:2'],
            'shipping_postal_code' => ['required', 'string', 'max:12'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $cart = $this->cart->current()->load(['items.variant.product']);

        if ($cart->isEmpty()) {
            return redirect()->route('cart.show')->with('status', 'Your cart is empty.');
        }

        $order = DB::transaction(function () use ($cart, $data) {
            $subtotal = $cart->subtotal();
            $shipping = 0;
            $total = $subtotal + $shipping;

            $order = Order::create(array_merge($data, [
                'number' => Order::generateNumber(),
                'user_id' => auth()->id(),
                'status' => Order::STATUS_PENDING,
                'payment_status' => Order::PAYMENT_UNPAID,
                'currency' => 'INR',
                'subtotal' => $subtotal,
                'shipping_total' => $shipping,
                'discount_total' => 0,
                'total' => $total,
            ]));

            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_variant_id' => $item->product_variant_id,
                    'product_name' => $item->variant->product->name,
                    'variant_label' => $item->variant->label(),
                    'sku' => $item->variant->sku,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'line_total' => $item->lineTotal(),
                ]);
            }

            $this->cart->clear();

            return $order;
        });

        if ($this->razorpay->isConfigured()) {
            $this->razorpay->createOrder($order);
            return redirect()->route('checkout.pay', $order);
        }

        return redirect()->route('order.confirmation', $order)->with('status', 'Order placed. Razorpay is not yet configured — we will contact you for payment.');
    }

    public function pay(Order $order)
    {
        if ($order->payment_status === Order::PAYMENT_PAID) {
            return redirect()->route('order.confirmation', $order);
        }

        if (! $this->razorpay->isConfigured() || ! $order->razorpay_order_id) {
            return redirect()->route('order.confirmation', $order);
        }

        return view('shop.pay', [
            'order' => $order,
            'razorpayKey' => $this->razorpay->publicKey(),
        ]);
    }

    public function verify(Request $request, Order $order)
    {
        $data = $request->validate([
            'razorpay_order_id' => ['required', 'string'],
            'razorpay_payment_id' => ['required', 'string'],
            'razorpay_signature' => ['required', 'string'],
        ]);

        if ($data['razorpay_order_id'] !== $order->razorpay_order_id) {
            return redirect()->route('checkout.pay', $order)->withErrors(['payment' => 'Order mismatch']);
        }

        $valid = $this->razorpay->verifySignature(
            $data['razorpay_order_id'],
            $data['razorpay_payment_id'],
            $data['razorpay_signature'],
        );

        if (! $valid) {
            $order->update(['payment_status' => Order::PAYMENT_FAILED]);
            return redirect()->route('checkout.pay', $order)->withErrors(['payment' => 'Payment signature could not be verified.']);
        }

        $order->update([
            'razorpay_payment_id' => $data['razorpay_payment_id'],
            'razorpay_signature' => $data['razorpay_signature'],
            'payment_status' => Order::PAYMENT_PAID,
            'status' => Order::STATUS_PROCESSING,
            'paid_at' => now(),
        ]);

        return redirect()->route('order.confirmation', $order);
    }

    public function confirmation(Order $order)
    {
        return view('shop.order-confirmation', [
            'order' => $order->load('items'),
        ]);
    }
}
