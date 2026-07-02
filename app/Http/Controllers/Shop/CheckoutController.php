<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Services\CartService;
use App\Services\OrderPaymentService;
use App\Services\PromoCodeService;
use App\Services\RazorpayService;
use App\Services\ShippingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    public function __construct(
        protected CartService $cart,
        protected RazorpayService $razorpay,
        protected OrderPaymentService $orderPayments,
        protected ShippingService $shipping,
        protected PromoCodeService $promoCodes,
    ) {}

    public function show()
    {
        $cart = $this->cart->current()->load(['items.variant.product']);

        if ($cart->isEmpty()) {
            return redirect()->route('cart.show')->with('status', 'Your cart is empty.');
        }

        return view('shop.checkout', [
            'cart' => $cart,
            'defaults' => $this->checkoutDefaults(),
            'countries' => $this->shipping->availableCountries(),
            'appliedPromoCode' => $this->promoCodes->getAppliedCode(),
        ]);
    }

    public function shippingQuote(Request $request)
    {
        $data = $request->validate([
            'shipping_country' => ['required', 'string', 'size:2'],
            'shipping_postal_code' => ['required', 'string', 'max:12'],
            'shipping_state' => ['nullable', 'string', 'max:255'],
        ]);

        $cart = $this->cart->current()->load(['items.variant.product']);

        if ($cart->isEmpty()) {
            return response()->json(['serviceable' => false, 'message' => 'Your cart is empty.'], 422);
        }

        return response()->json($this->pricingSummary(
            $cart,
            $data['shipping_country'],
            $data['shipping_postal_code'],
            $data['shipping_state'] ?? null,
        ));
    }

    public function applyPromo(Request $request)
    {
        $data = $request->validate([
            'promo_code' => ['required', 'string', 'max:64'],
            'shipping_country' => ['nullable', 'string', 'size:2'],
            'shipping_postal_code' => ['nullable', 'string', 'max:12'],
            'shipping_state' => ['nullable', 'string', 'max:255'],
        ]);

        $cart = $this->cart->current()->load(['items.variant.product']);

        if ($cart->isEmpty()) {
            return response()->json(['message' => 'Your cart is empty.'], 422);
        }

        $promo = $this->promoCodes->findAndValidate($data['promo_code'], $cart->subtotal());
        $this->promoCodes->applyToSession($promo->code);

        $summary = $this->pricingSummary(
            $cart,
            $data['shipping_country'] ?? 'IN',
            $data['shipping_postal_code'] ?? '',
            $data['shipping_state'] ?? null,
        );

        return response()->json(array_merge($summary, [
            'message' => 'Promo code applied.',
        ]));
    }

    public function removePromo(Request $request)
    {
        $data = $request->validate([
            'shipping_country' => ['nullable', 'string', 'size:2'],
            'shipping_postal_code' => ['nullable', 'string', 'max:12'],
            'shipping_state' => ['nullable', 'string', 'max:255'],
        ]);

        $this->promoCodes->clearSession();

        $cart = $this->cart->current()->load(['items.variant.product']);

        if ($cart->isEmpty()) {
            return response()->json(['message' => 'Promo code removed.'], 200);
        }

        return response()->json($this->pricingSummary(
            $cart,
            $data['shipping_country'] ?? 'IN',
            $data['shipping_postal_code'] ?? '',
            $data['shipping_state'] ?? null,
        ));
    }

    /** @return array<string, mixed> */
    protected function pricingSummary($cart, string $country, string $postal, ?string $state): array
    {
        $subtotal = $cart->subtotal();
        $quote = $this->shipping->quote($country, $postal, $state, $subtotal);

        $discount = 0;
        $shipping = $quote['serviceable'] ? $quote['shipping_fee'] : 0;
        $promoCode = null;
        $promoLabel = null;
        $promoError = null;

        $appliedCode = $this->promoCodes->getAppliedCode();

        if ($appliedCode) {
            try {
                $promo = $this->promoCodes->findAndValidate($appliedCode, $subtotal);
                $applied = $this->promoCodes->apply($promo, $subtotal, $shipping);
                $discount = $applied['discount_total'];
                $shipping = $applied['shipping_total'];
                $promoCode = $promo->code;
                $promoLabel = $applied['label'];
            } catch (ValidationException $exception) {
                $this->promoCodes->clearSession();
                $promoError = collect($exception->errors())->flatten()->first();
            }
        }

        $total = $quote['serviceable']
            ? max(1, $subtotal - $discount + $shipping)
            : max(0, $subtotal - $discount);

        return array_merge($quote, [
            'subtotal' => $subtotal,
            'discount_total' => $discount,
            'shipping_total' => $shipping,
            'shipping_fee' => $shipping,
            'total' => $total,
            'promo_code' => $promoCode,
            'promo_label' => $promoLabel,
            'promo_error' => $promoError,
        ]);
    }

    /** @return array<string, string|null> */
    protected function checkoutDefaults(): array
    {
        $user = auth()->user();

        if (! $user || $user->is_admin) {
            return ['shipping_country' => 'IN'];
        }

        $latest = $user->orders()->latest()->first();

        if ($latest) {
            return [
                'customer_name' => $latest->customer_name,
                'customer_email' => $latest->customer_email,
                'customer_phone' => $latest->customer_phone,
                'shipping_line1' => $latest->shipping_line1,
                'shipping_line2' => $latest->shipping_line2,
                'shipping_city' => $latest->shipping_city,
                'shipping_state' => $latest->shipping_state,
                'shipping_country' => $latest->shipping_country,
                'shipping_postal_code' => $latest->shipping_postal_code,
            ];
        }

        return [
            'customer_name' => $user->name,
            'customer_email' => $user->email,
            'shipping_country' => 'IN',
        ];
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
            'accept_terms' => ['accepted'],
        ]);

        $cart = $this->cart->current()->load(['items.variant.product']);

        if ($cart->isEmpty()) {
            return redirect()->route('cart.show')->with('status', 'Your cart is empty.');
        }

        $summary = $this->pricingSummary(
            $cart,
            $data['shipping_country'],
            $data['shipping_postal_code'],
            $data['shipping_state'],
        );

        if (! $summary['serviceable']) {
            throw ValidationException::withMessages([
                'shipping_postal_code' => $summary['message'],
            ]);
        }

        $subtotal = $summary['subtotal'];
        $shipping = $summary['shipping_total'];
        $discount = $summary['discount_total'];
        $total = $summary['total'];
        $promo = $summary['promo_code']
            ? $this->promoCodes->findByCode($summary['promo_code'])
            : null;

        $order = DB::transaction(function () use ($cart, $data, $subtotal, $shipping, $discount, $total, $promo) {
            foreach ($cart->items as $item) {
                $variant = ProductVariant::query()
                    ->whereKey($item->product_variant_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($item->quantity > $variant->stock) {
                    $label = $item->variant->product->name.' ('.$item->variant->label().')';

                    throw ValidationException::withMessages([
                        'cart' => $variant->stock === 0
                            ? "{$label} is out of stock."
                            : "Only {$variant->stock} left for {$label}. Please update your bag.",
                    ]);
                }
            }

            if ($promo) {
                $this->promoCodes->validate($promo, $subtotal);
            }

            $order = Order::create(array_merge($data, [
                'number' => Order::generateNumber(),
                'user_id' => auth()->id(),
                'status' => Order::STATUS_PENDING,
                'payment_status' => Order::PAYMENT_UNPAID,
                'currency' => 'INR',
                'subtotal' => $subtotal,
                'shipping_total' => $shipping,
                'discount_total' => $discount,
                'promo_code_id' => $promo?->id,
                'promo_code' => $promo?->code,
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
            $this->promoCodes->clearSession();

            return $order;
        });

        if ($this->razorpay->isConfigured()) {
            try {
                $this->razorpay->createOrder($order);
            } catch (\Throwable) {
                return redirect()
                    ->route('order.confirmation', $order)
                    ->withErrors(['payment' => 'We could not start online payment. Please contact us to complete your order.']);
            }

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

        $this->orderPayments->markPaid(
            $order,
            $data['razorpay_payment_id'],
            $data['razorpay_signature'],
        );

        return redirect()->route('order.confirmation', $order);
    }

    public function confirmation(Order $order)
    {
        if (auth()->check() && $order->user_id === null && strcasecmp($order->customer_email, auth()->user()->email) === 0) {
            $order->update(['user_id' => auth()->id()]);
        }

        return view('shop.order-confirmation', [
            'order' => $order->load('items'),
        ]);
    }
}
