@extends('shop.layouts.app', ['title' => 'Checkout — Sutra Conscious'])

@section('content')
    <section class="container-wide py-12 lg:py-20">
        <div class="mb-10" data-reveal>
            <p class="eyebrow">Checkout</p>
            <h1 class="mt-3 font-display text-display-md text-brand-black">Almost yours.</h1>
            <div class="mt-6 flex items-center gap-3 text-xs uppercase tracking-[0.18em] text-brand-black/60">
                <a href="{{ route('cart.show') }}" class="hover:text-brand-blue">Bag</a>
                <span class="w-6 h-px bg-brand-black/20"></span>
                <span class="text-brand-blue">Details</span>
                <span class="w-6 h-px bg-brand-black/20"></span>
                <span>Payment</span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-[1fr_400px] gap-12">
            <form action="{{ route('checkout.place') }}" method="POST" class="space-y-10" novalidate data-reveal>
                @csrf

                <section>
                    <div class="flex items-baseline justify-between mb-6">
                        <h2 class="font-display text-2xl text-brand-black">Contact</h2>
                        <span class="text-[0.7rem] uppercase tracking-[0.18em] text-brand-black/40">01</span>
                    </div>
                    <div class="space-y-5">
                        <div>
                            <label class="field-label">Full name</label>
                            <input type="text" name="customer_name" value="{{ old('customer_name') }}" required class="field-input">
                            @error('customer_name') <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p> @enderror
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="field-label">Email</label>
                                <input type="email" name="customer_email" value="{{ old('customer_email') }}" required class="field-input">
                                @error('customer_email') <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="field-label">Phone</label>
                                <input type="tel" name="customer_phone" value="{{ old('customer_phone') }}" required class="field-input">
                                @error('customer_phone') <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                </section>

                <div class="rule"></div>

                <section>
                    <div class="flex items-baseline justify-between mb-6">
                        <h2 class="font-display text-2xl text-brand-black">Ships to</h2>
                        <span class="text-[0.7rem] uppercase tracking-[0.18em] text-brand-black/40">02</span>
                    </div>
                    <div class="space-y-5">
                        <div>
                            <label class="field-label">Address line 1</label>
                            <input type="text" name="shipping_line1" value="{{ old('shipping_line1') }}" required class="field-input">
                            @error('shipping_line1') <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="field-label">Address line 2 <span class="text-brand-black/30 normal-case tracking-normal">(optional)</span></label>
                            <input type="text" name="shipping_line2" value="{{ old('shipping_line2') }}" class="field-input">
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="field-label">City</label>
                                <input type="text" name="shipping_city" value="{{ old('shipping_city') }}" required class="field-input">
                                @error('shipping_city') <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="field-label">State</label>
                                <input type="text" name="shipping_state" value="{{ old('shipping_state') }}" required class="field-input">
                                @error('shipping_state') <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="field-label">Postal code</label>
                                <input type="text" name="shipping_postal_code" value="{{ old('shipping_postal_code') }}" required class="field-input">
                                @error('shipping_postal_code') <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="field-label">Country</label>
                                <select name="shipping_country" required class="field-input">
                                    <option value="IN" selected>India</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </section>

                <div class="rule"></div>

                <section>
                    <div class="flex items-baseline justify-between mb-6">
                        <h2 class="font-display text-2xl text-brand-black">A note for us <span class="text-brand-black/30 text-sm">(optional)</span></h2>
                        <span class="text-[0.7rem] uppercase tracking-[0.18em] text-brand-black/40">03</span>
                    </div>
                    <textarea name="notes" rows="3" placeholder="Gift wrap, delivery instructions, anything else?" class="field-input">{{ old('notes') }}</textarea>
                </section>

                <div class="pt-2">
                    <button type="submit" class="btn-primary w-full sm:w-auto">
                        Continue to Payment
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3"/>
                        </svg>
                    </button>
                    <p class="text-[0.7rem] uppercase tracking-[0.18em] text-brand-black/40 mt-4">Secure payment · Razorpay</p>
                </div>
            </form>

            <aside class="lg:sticky lg:top-28 lg:self-start" data-reveal="right">
                <div class="bg-brand-skin/30 border border-surface-line p-7">
                    <p class="eyebrow-dim">Your bag</p>
                    <div class="mt-6 space-y-5 max-h-[440px] overflow-y-auto pr-2 scroll-thin">
                        @foreach($cart->items as $item)
                            @php
                                $img = $item->variant->product->images->first();
                            @endphp
                            <div class="flex gap-4">
                                <div class="w-16 h-20 bg-surface-cream overflow-hidden flex-shrink-0">
                                    @if($img)
                                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($img->path) }}" alt="" loading="lazy" class="w-full h-full object-cover">
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm font-medium text-brand-black truncate">{{ $item->variant->product->name }}</div>
                                    <div class="text-[0.7rem] uppercase tracking-[0.18em] text-brand-black/55 mt-1">{{ $item->variant->label() }} · ×{{ $item->quantity }}</div>
                                </div>
                                <div class="text-sm text-brand-black whitespace-nowrap self-start">₹{{ number_format($item->lineTotal()) }}</div>
                            </div>
                        @endforeach
                    </div>

                    <div class="rule my-5"></div>

                    <dl class="space-y-2.5 text-sm">
                        <div class="flex justify-between"><dt class="text-brand-black/70">Subtotal</dt><dd>₹{{ number_format($cart->subtotal()) }}</dd></div>
                        <div class="flex justify-between"><dt class="text-brand-black/70">Shipping</dt><dd class="text-brand-black/50 text-[0.7rem] uppercase tracking-[0.18em]">Calculated next</dd></div>
                    </dl>

                    <div class="rule my-5"></div>

                    <div class="flex items-baseline justify-between">
                        <span class="eyebrow-dim">Total</span>
                        <span class="font-display text-3xl text-brand-blue">₹{{ number_format($cart->subtotal()) }}</span>
                    </div>
                </div>

                <ul class="mt-6 space-y-2 text-[0.72rem] text-brand-black/60 px-1">
                    <li class="flex items-center gap-2"><span class="w-1 h-1 rounded-full bg-brand-blue"></span>Free returns within 7 days</li>
                    <li class="flex items-center gap-2"><span class="w-1 h-1 rounded-full bg-brand-blue"></span>Ships across India in 4–7 business days</li>
                    <li class="flex items-center gap-2"><span class="w-1 h-1 rounded-full bg-brand-blue"></span>Secure encrypted payment</li>
                </ul>
            </aside>
        </div>
    </section>
@endsection
