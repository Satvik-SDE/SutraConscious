@extends('shop.layouts.app', ['title' => 'Order ' . $order->number . ' — Sutra Conscious'])

@section('content')
    <section class="container-wide py-12 lg:py-20">
        <div class="mb-10" data-reveal>
            <a href="{{ $backUrl }}" class="text-[0.7rem] uppercase tracking-[0.18em] text-brand-black/50 hover:text-brand-blue transition-colors">← {{ $backLabel }}</a>
            <p class="eyebrow mt-6">Order {{ $order->number }}</p>
            <h1 class="mt-3 font-display text-display-md text-brand-black">Order details</h1>
            @if($order->created_at)
                <p class="mt-2 text-sm text-brand-black/60">Placed {{ $order->created_at->format('j F Y, g:i A') }}</p>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-[1fr_380px] gap-12">
            <div class="space-y-10">
                <div class="border border-surface-line bg-surface-cream p-7 lg:p-10" data-reveal>
                    <h2 class="font-display text-2xl text-brand-black mb-6">Status</h2>
                    <div class="flex flex-wrap gap-3 mb-8">
                        <span class="inline-flex text-[0.65rem] uppercase tracking-[0.16em] px-3 py-1.5 border border-brand-blue/30 text-brand-blue">{{ $order->statusLabel() }}</span>
                        <span class="inline-flex text-[0.65rem] uppercase tracking-[0.16em] px-3 py-1.5 bg-brand-black/5 text-brand-black/65">{{ $order->paymentStatusLabel() }}</span>
                    </div>
                    @include('shop.partials.order-timeline', ['order' => $order])
                </div>

                @if($order->hasTracking())
                    <div class="border border-brand-blue/20 bg-brand-blue/5 p-7 lg:p-10" data-reveal>
                        <h2 class="font-display text-2xl text-brand-black mb-2">Package tracking</h2>
                        @if($order->tracking_carrier)
                            <p class="text-sm text-brand-black/60 mb-4">Carrier: {{ $order->tracking_carrier }}</p>
                        @endif
                        @if($order->tracking_number)
                            <p class="font-mono text-sm text-brand-black mb-6">{{ $order->tracking_number }}</p>
                        @endif
                        @if($link = $order->trackingLink())
                            <a href="{{ $link }}" target="_blank" rel="noopener noreferrer" class="btn-primary inline-flex">
                                Track shipment
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5M10.5 13.5 21 3m0 0h-5.25M21 3v5.25"/>
                                </svg>
                            </a>
                        @endif
                    </div>
                @elseif($order->status === \App\Models\Order::STATUS_SHIPPED)
                    <p class="text-sm text-brand-black/55" data-reveal>Your order is on the way. Tracking details will appear here once available.</p>
                @endif

                <div class="border border-surface-line bg-surface-cream p-7 lg:p-10" data-reveal>
                    <h2 class="font-display text-2xl text-brand-black mb-6">Items</h2>
                    <ul class="divide-y divide-surface-line">
                        @foreach($order->items as $item)
                            <li class="py-4 flex justify-between gap-4">
                                <div>
                                    <div class="text-brand-black font-medium">{{ $item->product_name }}</div>
                                    <div class="text-[0.7rem] uppercase tracking-[0.18em] text-brand-black/55 mt-1">{{ $item->variant_label }} · ×{{ $item->quantity }}</div>
                                </div>
                                <div class="text-brand-black whitespace-nowrap">₹{{ number_format($item->line_total) }}</div>
                            </li>
                        @endforeach
                    </ul>
                    <div class="rule my-5"></div>
                    <div class="flex justify-between items-baseline">
                        <span class="eyebrow-dim">Total</span>
                        <span class="font-display text-3xl text-brand-blue">₹{{ number_format($order->total) }}</span>
                    </div>
                </div>
            </div>

            <aside class="lg:sticky lg:top-28 lg:self-start space-y-6" data-reveal="right">
                <div class="border border-surface-line bg-brand-skin/30 p-7">
                    <p class="eyebrow-dim">Ship to</p>
                    <p class="mt-4 text-sm text-brand-black leading-relaxed">
                        {{ $order->customer_name }}<br>
                        {{ $order->shipping_line1 }}<br>
                        @if($order->shipping_line2){{ $order->shipping_line2 }}<br>@endif
                        {{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_postal_code }}<br>
                        {{ $order->shipping_country }}
                    </p>
                    <p class="mt-4 text-xs text-brand-black/55">{{ $order->customer_email }} · {{ $order->customer_phone }}</p>
                </div>

                @if(empty($isGuestView) && $order->payment_status === \App\Models\Order::PAYMENT_UNPAID && $order->razorpay_order_id)
                    <a href="{{ route('checkout.pay', $order) }}" class="btn-primary w-full text-center">Complete payment</a>
                @endif

                @if(empty($isGuestView))
                    <a href="{{ route('shop') }}" class="block text-center text-[0.7rem] uppercase tracking-[0.18em] text-brand-black/50 hover:text-brand-blue">Continue shopping</a>
                @else
                    <p class="text-xs text-brand-black/50 text-center">
                        <a href="{{ route('register') }}" class="text-brand-blue hover:underline">Create an account</a> to save this order to your profile.
                    </p>
                @endif
            </aside>
        </div>
    </section>
@endsection
