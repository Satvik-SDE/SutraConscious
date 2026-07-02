@extends('shop.layouts.app', ['title' => 'Checkout — Sutra Conscious'])

@section('content')
    <section class="container-wide py-12 lg:py-20">
        <div class="mb-10" data-reveal>
            <p class="eyebrow">Checkout</p>
            <h1 class="mt-3 font-display text-display-md text-brand-black">Almost yours.</h1>
            @guest
                <p class="mt-4 text-sm text-brand-black/60">
                    <a href="{{ route('login') }}" class="text-brand-blue hover:underline">Sign in</a>
                    for faster checkout and order history, or
                    <a href="{{ route('orders.track') }}" class="text-brand-blue hover:underline">track a guest order</a>.
                </p>
            @endguest
            <div class="mt-6 flex items-center gap-3 text-xs uppercase tracking-[0.18em] text-brand-black/60">
                <a href="{{ route('cart.show') }}" class="hover:text-brand-blue">Bag</a>
                <span class="w-6 h-px bg-brand-black/20"></span>
                <span class="text-brand-blue">Details</span>
                <span class="w-6 h-px bg-brand-black/20"></span>
                <span>Payment</span>
            </div>
        </div>

        @error('cart')
            <div class="mb-6 text-red-600 text-sm" data-reveal>{{ $message }}</div>
        @enderror

        <div class="grid grid-cols-1 lg:grid-cols-[1fr_400px] gap-12"
             x-data="{
                subtotal: {{ $cart->subtotal() }},
                loading: false,
                promoLoading: false,
                checked: false,
                termsAccepted: @js((bool) old('accept_terms')),
                serviceable: null,
                shippingFee: 0,
                discount: 0,
                promoInput: @js($appliedPromoCode ?? ''),
                promoCode: @js($appliedPromoCode),
                promoLabel: '',
                promoMessage: '',
                promoError: '',
                message: '',
                zoneName: '',
                amountUntilFreeShipping: null,
                freeShippingApplied: false,
                freeShippingMin: {{ (int) config('shipping.india.free_shipping_min', 2000) }},
                get total() {
                    const ship = this.serviceable ? this.shippingFee : 0;
                    return Math.max(0, this.subtotal - this.discount + ship);
                },
                get canSubmit() { return this.serviceable === true && this.termsAccepted; },
                get orderAmountForFreeShipping() {
                    return Math.max(0, this.freeShippingMin - this.subtotal);
                },
                shippingPayload() {
                    return {
                        shipping_postal_code: this.$refs.postal?.value?.trim() || '',
                        shipping_country: this.$refs.country?.value || 'IN',
                        shipping_state: this.$refs.state?.value?.trim() || '',
                    };
                },
                applyPricing(data) {
                    this.serviceable = data.serviceable ?? this.serviceable;
                    this.shippingFee = data.shipping_fee ?? data.shipping_total ?? 0;
                    this.discount = data.discount_total ?? 0;
                    this.promoCode = data.promo_code ?? null;
                    this.promoLabel = data.promo_label ?? '';
                    this.promoError = data.promo_error ?? '';
                    if (data.message && data.serviceable !== undefined) this.message = data.message;
                    if (data.zone_name !== undefined) this.zoneName = data.zone_name ?? '';
                    this.amountUntilFreeShipping = data.amount_until_free_shipping ?? null;
                    this.freeShippingApplied = data.free_shipping_applied ?? false;
                },
                async checkShipping() {
                    const postal = this.$refs.postal?.value?.trim() || '';
                    const country = this.$refs.country?.value || 'IN';
                    const state = this.$refs.state?.value?.trim() || '';
                    if (postal.length < 3) {
                        this.checked = false;
                        this.serviceable = null;
                        this.message = 'Enter your pin / postal code to check delivery.';
                        return;
                    }
                    this.loading = true;
                    try {
                        const res = await fetch(@js(route('checkout.shipping-quote')), {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': @js(csrf_token()),
                            },
                            body: JSON.stringify(this.shippingPayload()),
                        });
                        const data = await res.json();
                        this.checked = true;
                        this.applyPricing(data);
                    } catch (e) {
                        this.checked = true;
                        this.serviceable = false;
                        this.message = 'Could not check delivery. Please try again.';
                    } finally {
                        this.loading = false;
                    }
                },
                async applyPromo() {
                    const code = (this.promoInput || '').trim();
                    if (!code) return;
                    this.promoLoading = true;
                    this.promoError = '';
                    this.promoMessage = '';
                    try {
                        const res = await fetch(@js(route('checkout.promo.apply')), {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': @js(csrf_token()),
                            },
                            body: JSON.stringify({ promo_code: code, ...this.shippingPayload() }),
                        });
                        const data = await res.json();
                        if (!res.ok) {
                            this.promoError = data.message || Object.values(data.errors || {}).flat()[0] || 'Could not apply promo code.';
                            return;
                        }
                        this.applyPricing(data);
                        this.promoInput = data.promo_code || code;
                        this.promoMessage = data.message || 'Promo code applied.';
                    } catch (e) {
                        this.promoError = 'Could not apply promo code. Please try again.';
                    } finally {
                        this.promoLoading = false;
                    }
                },
                async removePromo() {
                    this.promoLoading = true;
                    this.promoError = '';
                    this.promoMessage = '';
                    try {
                        const res = await fetch(@js(route('checkout.promo.remove')), {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': @js(csrf_token()),
                            },
                            body: JSON.stringify(this.shippingPayload()),
                        });
                        const data = await res.json();
                        this.promoCode = null;
                        this.promoLabel = '';
                        this.promoInput = '';
                        this.discount = 0;
                        this.applyPricing(data);
                    } catch (e) {
                        this.promoError = 'Could not remove promo code.';
                    } finally {
                        this.promoLoading = false;
                    }
                }
             }"
             x-init="$nextTick(() => { if (($refs.postal?.value || '').length >= 3) checkShipping(); else if (promoCode) applyPromo(); })">
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
                            <input type="text" name="customer_name" value="{{ old('customer_name', $defaults['customer_name'] ?? '') }}" required class="field-input">
                            @error('customer_name') <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p> @enderror
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="field-label">Email</label>
                                <input type="email" name="customer_email" value="{{ old('customer_email', $defaults['customer_email'] ?? '') }}" required class="field-input">
                                @error('customer_email') <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="field-label">Phone</label>
                                <input type="tel" name="customer_phone" value="{{ old('customer_phone', $defaults['customer_phone'] ?? '') }}" required class="field-input">
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
                            <input type="text" name="shipping_line1" value="{{ old('shipping_line1', $defaults['shipping_line1'] ?? '') }}" required class="field-input">
                            @error('shipping_line1') <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="field-label">Address line 2 <span class="text-brand-black/30 normal-case tracking-normal">(optional)</span></label>
                            <input type="text" name="shipping_line2" value="{{ old('shipping_line2', $defaults['shipping_line2'] ?? '') }}" class="field-input">
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="field-label">City</label>
                                <input type="text" name="shipping_city" value="{{ old('shipping_city', $defaults['shipping_city'] ?? '') }}" required class="field-input">
                                @error('shipping_city') <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="field-label">State</label>
                                <input type="text" name="shipping_state" x-ref="state" value="{{ old('shipping_state', $defaults['shipping_state'] ?? '') }}" required class="field-input" @blur="checkShipping()">
                                @error('shipping_state') <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="field-label">Postal code / Pincode</label>
                                <input type="text" name="shipping_postal_code" x-ref="postal" value="{{ old('shipping_postal_code', $defaults['shipping_postal_code'] ?? '') }}" required class="field-input" @blur="checkShipping()" @input.debounce.500ms="checkShipping()">
                                @error('shipping_postal_code') <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p> @enderror
                                <p class="mt-2 text-xs text-brand-black/55" x-show="loading">Checking delivery…</p>
                                <p class="mt-2 text-xs text-brand-blue" x-show="!loading && serviceable === true" x-text="message"></p>
                                <p class="mt-2 text-xs text-red-600" x-show="!loading && serviceable === false" x-text="message"></p>
                                <p class="mt-2 text-xs text-brand-black/50" x-show="!loading && serviceable === null && !checked">Enter pin / postal code to see if we deliver to you.</p>
                            </div>
                            <div>
                                <label class="field-label">Country</label>
                                <select name="shipping_country" x-ref="country" required class="field-input" @change="checkShipping()">
                                    @foreach($countries as $code => $label)
                                        <option value="{{ $code }}" @selected(old('shipping_country', $defaults['shipping_country'] ?? 'IN') === $code)>{{ $label }}</option>
                                    @endforeach
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

                <div class="rule"></div>

                <section>
                    <div class="flex items-baseline justify-between mb-6">
                        <h2 class="font-display text-2xl text-brand-black">Terms</h2>
                        <span class="text-[0.7rem] uppercase tracking-[0.18em] text-brand-black/40">04</span>
                    </div>
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" name="accept_terms" value="1" class="mt-1 rounded border-surface-line text-brand-blue focus:ring-brand-blue" x-model="termsAccepted" @checked(old('accept_terms'))>
                        <span class="text-sm text-brand-black/75 leading-relaxed">
                            I agree to the
                            <a href="{{ route('shipping-returns') }}" class="text-brand-blue hover:underline" target="_blank">shipping &amp; returns policy</a>
                            and
                            <a href="{{ route('privacy') }}" class="text-brand-blue hover:underline" target="_blank">privacy policy</a>.
                        </span>
                    </label>
                    @error('accept_terms') <p class="text-red-600 text-xs mt-2">{{ $message }}</p> @enderror
                </section>

                <div class="pt-2">
                    <button type="submit" class="btn-primary w-full sm:w-auto" :disabled="!canSubmit" :class="!canSubmit ? 'opacity-50 cursor-not-allowed' : ''">
                        Continue to Payment
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3"/>
                        </svg>
                    </button>
                    <p class="text-[0.7rem] uppercase tracking-[0.18em] text-brand-black/40 mt-4" x-show="!canSubmit && serviceable !== true">Check pin / postal code above to continue</p>
                    <p class="text-[0.7rem] uppercase tracking-[0.18em] text-brand-black/40 mt-4" x-show="serviceable === true && !termsAccepted">Please agree to the terms and policies to continue</p>
                    <p class="text-[0.7rem] uppercase tracking-[0.18em] text-brand-black/40 mt-4" x-show="canSubmit">Secure payment · Razorpay</p>
                </div>
            </form>

            <aside class="lg:sticky lg:top-28 lg:self-start" data-reveal="right">
                <div class="mb-4 bg-brand-blue/10 border border-brand-blue/20 px-4 py-3 text-sm text-brand-black/80" x-show="($refs.country?.value || 'IN') === 'IN'">
                    <span class="font-medium text-brand-blue">Free shipping across India</span>
                    on orders above ₹{{ number_format(config('shipping.india.free_shipping_min', 2000)) }}.
                </div>

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

                    <p class="text-xs text-brand-blue font-medium" x-show="($refs.country?.value || 'IN') === 'IN' && orderAmountForFreeShipping > 0" x-text="'Add order for ₹' + orderAmountForFreeShipping.toLocaleString('en-IN') + ' or more to get shipping free.'"></p>
                    <p class="text-xs text-brand-blue font-medium" x-show="($refs.country?.value || 'IN') === 'IN' && orderAmountForFreeShipping === 0">Your order qualifies for free shipping across India.</p>

                    <div class="space-y-3">
                        <p class="text-[0.7rem] uppercase tracking-[0.18em] text-brand-black/55">Promo code</p>
                        <template x-if="!promoCode">
                            <div class="flex gap-2">
                                <input type="text" x-model="promoInput" placeholder="Enter code" class="field-input flex-1 uppercase" @keydown.enter.prevent="applyPromo()">
                                <button type="button" class="btn-outline shrink-0 px-4" @click="applyPromo()" :disabled="promoLoading || !(promoInput || '').trim()">Apply</button>
                            </div>
                        </template>
                        <template x-if="promoCode">
                            <div class="flex items-center justify-between gap-3 bg-surface-cream border border-surface-line px-4 py-3">
                                <div>
                                    <div class="text-sm font-medium text-brand-black" x-text="promoCode"></div>
                                    <div class="text-xs text-brand-blue mt-0.5" x-show="promoLabel" x-text="promoLabel"></div>
                                </div>
                                <button type="button" class="text-[0.7rem] uppercase tracking-[0.18em] text-brand-black/50 hover:text-brand-blue" @click="removePromo()" :disabled="promoLoading">Remove</button>
                            </div>
                        </template>
                        <p class="text-xs text-brand-blue" x-show="promoMessage" x-text="promoMessage"></p>
                        <p class="text-xs text-red-600" x-show="promoError" x-text="promoError"></p>
                    </div>

                    <div class="rule my-5"></div>

                    <dl class="space-y-2.5 text-sm">
                        <div class="flex justify-between"><dt class="text-brand-black/70">Subtotal</dt><dd>₹{{ number_format($cart->subtotal()) }}</dd></div>
                        <div class="flex justify-between" x-show="discount > 0">
                            <dt class="text-brand-black/70">Discount</dt>
                            <dd class="text-brand-blue" x-text="'−₹' + discount.toLocaleString('en-IN')"></dd>
                        </div>
                        <div class="flex justify-between" x-show="promoCode && discount === 0 && serviceable === true && shippingFee === 0">
                            <dt class="text-brand-black/70">Promo</dt>
                            <dd class="text-brand-blue">Free shipping</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-brand-black/70">Shipping</dt>
                            <dd>
                                <span x-show="serviceable === null" class="text-brand-black/50 text-[0.7rem] uppercase tracking-[0.18em]">Enter pincode</span>
                                <span x-show="serviceable === true && shippingFee === 0" class="text-brand-blue">Free</span>
                                <span x-show="serviceable === true && shippingFee > 0" x-text="'₹' + shippingFee.toLocaleString('en-IN')"></span>
                                <span x-show="serviceable === false" class="text-red-600 text-xs">Not available</span>
                            </dd>
                        </div>
                    </dl>

                    <div class="rule my-5"></div>

                    <div class="flex items-baseline justify-between">
                        <span class="eyebrow-dim">Total</span>
                        <span class="font-display text-3xl text-brand-blue" x-text="'₹' + total.toLocaleString('en-IN')">₹{{ number_format($cart->subtotal()) }}</span>
                    </div>
                </div>

                <ul class="mt-6 space-y-2 text-[0.72rem] text-brand-black/60 px-1">
                    <li class="flex items-center gap-2"><span class="w-1 h-1 rounded-full bg-brand-blue"></span><span class="text-brand-blue font-medium">Free shipping across India on orders above ₹{{ number_format(config('shipping.india.free_shipping_min', 2000)) }}</span></li>
                    <li class="flex items-center gap-2"><span class="w-1 h-1 rounded-full bg-brand-blue"></span>Quality-checked before dispatch</li>
                    <li class="flex items-center gap-2"><span class="w-1 h-1 rounded-full bg-brand-blue"></span>India delivery 3–7 business days</li>
                    <li class="flex items-center gap-2"><span class="w-1 h-1 rounded-full bg-brand-blue"></span>Secure encrypted payment</li>
                </ul>
            </aside>
        </div>
    </section>
@endsection
