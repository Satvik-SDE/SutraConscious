@extends('shop.layouts.app', ['title' => 'Order Confirmed — Sutra Conscious'])

@section('content')
    <section class="container-narrow py-16 lg:py-24 text-center">
        <div data-reveal>
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-brand-blue text-surface-cream mb-8 ring-8 ring-brand-blue/10">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-9 h-9" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                </svg>
            </div>
            <p class="eyebrow">Order received</p>
            <h1 class="mt-4 font-display text-display-md text-brand-black">Thank you, {{ explode(' ', $order->customer_name)[0] }}.</h1>
            <p class="mt-3 text-brand-black/70 text-lg">Your order <span class="text-brand-blue font-medium">{{ $order->number }}</span> has been received.</p>
            <p class="mt-3 text-sm text-brand-black/60 max-w-md mx-auto">A confirmation will be sent to {{ $order->customer_email }}. We'll reach out shortly with shipping details.</p>

            <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
                @auth
                    <a href="{{ route('account.orders.show', $order) }}" class="btn-primary">View order status</a>
                @else
                    <a href="{{ route('orders.track') }}" class="btn-primary">Track this order</a>
                    <a href="{{ route('register') }}" class="text-[0.7rem] uppercase tracking-[0.18em] text-brand-blue hover:underline">Create account to save orders</a>
                @endauth
            </div>
        </div>
    </section>

    <section class="container-narrow pb-section">
        <div class="bg-surface-cream border border-surface-line p-7 lg:p-10" data-reveal data-reveal-delay="200">
            <div class="flex items-end justify-between mb-7">
                <h2 class="font-display text-2xl text-brand-black">What's in your bag</h2>
                <span class="text-[0.7rem] uppercase tracking-[0.18em] text-brand-black/45">{{ $order->items->count() }} {{ Str::plural('item', $order->items->count()) }}</span>
            </div>
            <ul class="divide-y divide-surface-line">
                @foreach($order->items as $item)
                    <li class="py-4 grid grid-cols-[1fr_auto] gap-4 items-center">
                        <div>
                            <div class="text-brand-black font-medium">{{ $item->product_name }}</div>
                            <div class="text-[0.7rem] uppercase tracking-[0.18em] text-brand-black/55 mt-1">{{ $item->variant_label }} · ×{{ $item->quantity }}</div>
                        </div>
                        <div class="text-brand-black whitespace-nowrap">₹{{ number_format($item->line_total) }}</div>
                    </li>
                @endforeach
            </ul>
            <div class="rule my-5"></div>
            <div class="flex items-baseline justify-between">
                <span class="eyebrow-dim">Total</span>
                <span class="font-display text-3xl text-brand-blue">₹{{ number_format($order->total) }}</span>
            </div>
        </div>

        <div class="text-center mt-12" data-reveal data-reveal-delay="300">
            <a href="{{ route('shop') }}" class="btn-primary">Keep Browsing</a>
        </div>
    </section>
@endsection
