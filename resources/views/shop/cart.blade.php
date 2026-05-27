@extends('shop.layouts.app', ['title' => 'Your Bag — Sutra Conscious'])

@section('content')
    <section class="container-wide py-12 lg:py-20">
        <div class="flex items-end justify-between mb-10" data-reveal>
            <div>
                <p class="eyebrow">Your Bag</p>
                <h1 class="mt-3 font-display text-display-md text-brand-black">{{ $cart->itemCount() }} {{ Str::plural('piece', $cart->itemCount()) }} in your bag</h1>
            </div>
            <a href="{{ route('shop') }}" class="link-grow hidden sm:inline-flex">Continue shopping</a>
        </div>

        @if($cart->isEmpty())
            <div class="bg-brand-skin/30 border border-surface-line py-20 text-center" data-reveal>
                <div class="font-script text-6xl text-brand-blue/40">Sutra</div>
                <p class="mt-4 text-brand-black/70">Your bag is empty.</p>
                <a href="{{ route('shop') }}" class="btn-primary mt-8 inline-flex">Browse Kurtas</a>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-[1fr_380px] gap-12">
                <div class="border-y border-surface-line divide-y divide-surface-line" data-reveal>
                    @foreach($cart->items as $item)
                        @php
                            $product = $item->variant->product;
                            $img = $product->images->first();
                        @endphp
                        <div class="py-6 grid grid-cols-[100px_1fr_auto] sm:grid-cols-[140px_1fr_auto] gap-5 sm:gap-6 items-start">
                            <a href="{{ route('product.show', $product->slug) }}" class="aspect-product bg-brand-skin/30 overflow-hidden">
                                @if($img)
                                    <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($img->path) }}" alt="{{ $product->name }}" loading="lazy" class="w-full h-full object-cover">
                                @endif
                            </a>

                            <div class="min-w-0">
                                @if($product->category)
                                    <div class="text-[0.65rem] uppercase tracking-[0.3em] text-brand-black/45 mb-1">{{ $product->category->name }}</div>
                                @endif
                                <a href="{{ route('product.show', $product->slug) }}" class="block font-medium text-brand-black hover:text-brand-blue">{{ $product->name }}</a>
                                <div class="mt-1 text-xs uppercase tracking-[0.18em] text-brand-black/60">{{ $item->variant->label() }}</div>

                                <div class="mt-4 flex items-center gap-6">
                                    <div class="inline-flex items-center border border-surface-line select-none">
                                        <form action="{{ route('cart.update', $item) }}" method="POST" class="inline-flex">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="quantity" value="{{ max(0, $item->quantity - 1) }}">
                                            <button type="submit" class="w-9 h-9 text-brand-black hover:bg-brand-skin/40" aria-label="Decrease">−</button>
                                        </form>
                                        <span class="w-10 text-center text-sm">{{ $item->quantity }}</span>
                                        <form action="{{ route('cart.update', $item) }}" method="POST" class="inline-flex">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="quantity" value="{{ min(10, $item->quantity + 1) }}">
                                            <button type="submit" class="w-9 h-9 text-brand-black hover:bg-brand-skin/40" aria-label="Increase">+</button>
                                        </form>
                                    </div>

                                    <form action="{{ route('cart.remove', $item) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-[0.7rem] uppercase tracking-[0.18em] text-brand-black/55 hover:text-red-600 transition-colors">Remove</button>
                                    </form>
                                </div>
                            </div>

                            <div class="text-right">
                                <div class="font-medium text-brand-black">₹{{ number_format($item->lineTotal()) }}</div>
                                @if($item->quantity > 1)
                                    <div class="text-[0.7rem] text-brand-black/50 mt-1">₹{{ number_format($item->variant->price()) }} each</div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <aside class="lg:sticky lg:top-28 lg:self-start" data-reveal="right">
                    <div class="bg-brand-skin/30 border border-surface-line p-7">
                        <p class="eyebrow-dim">Order summary</p>
                        <dl class="mt-6 space-y-3 text-sm">
                            <div class="flex justify-between">
                                <dt class="text-brand-black/70">Subtotal</dt>
                                <dd class="text-brand-black">₹{{ number_format($cart->subtotal()) }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-brand-black/70">Shipping</dt>
                                <dd class="text-brand-black/60 text-xs uppercase tracking-[0.18em]">Calculated next</dd>
                            </div>
                        </dl>
                        <div class="rule my-5"></div>
                        <div class="flex items-baseline justify-between">
                            <span class="eyebrow-dim">Total</span>
                            <span class="font-display text-3xl text-brand-blue">₹{{ number_format($cart->subtotal()) }}</span>
                        </div>

                        <a href="{{ route('checkout.show') }}" class="btn-primary w-full justify-center mt-7">
                            Proceed to Checkout
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3"/>
                            </svg>
                        </a>

                        <div class="mt-5 text-[0.7rem] uppercase tracking-[0.18em] text-brand-black/50 text-center">Secure checkout · Razorpay</div>
                    </div>

                    <div class="mt-6 text-xs text-brand-black/60 leading-relaxed text-center px-4">
                        Need help? Email <a href="mailto:sutra.conscious@gmail.com" class="text-brand-blue link-underline">sutra.conscious@gmail.com</a>
                    </div>
                </aside>
            </div>
        @endif
    </section>
@endsection
