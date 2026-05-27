@extends('shop.layouts.app')

@php
    $heroImage = $featured->first()?->images?->first();
    $heroImageUrl = $heroImage ? \Illuminate\Support\Facades\Storage::disk('public')->url($heroImage->path) : null;
@endphp

@section('content')
    {{-- ─────────── HERO ─────────── --}}
    <section class="relative overflow-hidden bg-brand-skin">
        <div class="container-bleed pt-12 lg:pt-20 pb-12 lg:pb-24">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-16 items-center">
                <div class="lg:col-span-6 lg:col-start-1 relative z-10">
                    <p data-reveal class="eyebrow">100% Premium Cotton · Bharat</p>

                    <h1 data-reveal data-reveal-delay="100" class="mt-6 font-display text-display-xl text-brand-black">
                        Conscious cotton,
                        <span class="block font-script text-script-xl text-brand-blue mt-2">made for every day.</span>
                    </h1>

                    <p data-reveal data-reveal-delay="200" class="mt-8 text-lg lg:text-xl text-brand-black/75 max-w-xl leading-relaxed">
                        Thoughtfully cut kurtas in 100% premium cotton — woven in Bharat, breathable in our weather, soft enough to live in for decades.
                    </p>

                    <div data-reveal data-reveal-delay="300" class="mt-10 flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('shop') }}" class="btn-primary">
                            Shop the Collection
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3"/>
                            </svg>
                        </a>
                        <a href="{{ route('about') }}" class="btn-outline">Our Story</a>
                    </div>

                    <div data-reveal data-reveal-delay="400" class="mt-14 flex items-center gap-6">
                        <div class="flex -space-x-1">
                            @foreach($featured->take(3) as $fp)
                                @php $thumb = $fp->images->first(); @endphp
                                @if($thumb)
                                    <div class="w-10 h-10 rounded-full overflow-hidden ring-2 ring-brand-skin">
                                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($thumb->path) }}" alt="" loading="lazy" class="w-full h-full object-cover">
                                    </div>
                                @endif
                            @endforeach
                        </div>
                        <div>
                            <div class="text-sm font-medium text-brand-black">{{ $featured->count() }}+ new arrivals</div>
                            <div class="text-xs text-brand-black/60">in our debut collection</div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-6 relative">
                    <div data-reveal="right" class="relative aspect-hero w-full max-w-xl mx-auto bg-surface-cream border border-surface-line overflow-hidden">
                        @if($heroImageUrl)
                            <img src="{{ $heroImageUrl }}" alt="Sutra Conscious — featured kurta" class="absolute inset-0 w-full h-full object-cover no-drag" loading="eager" fetchpriority="high">
                        @else
                            <div class="absolute inset-0 flex items-center justify-center">
                                <img src="{{ asset('img/brand/logo.png') }}" alt="" class="w-1/2 max-w-xs opacity-80">
                            </div>
                        @endif

                        {{-- Floating tag --}}
                        <div class="absolute bottom-4 left-4 bg-surface-cream/95 backdrop-blur-sm border border-surface-line px-4 py-3 max-w-[60%]">
                            <div class="text-[0.65rem] uppercase tracking-[0.3em] text-brand-blue mb-1">{{ $featured->first()?->category?->name ?? 'First Collection' }}</div>
                            <div class="text-sm font-medium text-brand-black">{{ $featured->first()?->name ?? 'Featured Kurta' }}</div>
                        </div>
                    </div>

                    {{-- Decorative script accent --}}
                    <div aria-hidden="true" class="hidden lg:block absolute -bottom-6 -left-12 font-script text-9xl text-brand-blue/15 select-none">सूत्र</div>
                </div>
            </div>
        </div>

        {{-- Decorative bottom rule with eyebrow --}}
        <div class="absolute inset-x-0 bottom-0 flex items-center gap-4 px-4 lg:px-10 pb-3 text-[0.65rem] uppercase tracking-[0.3em] text-brand-black/40">
            <span>Scroll</span>
            <div class="h-px flex-1 bg-brand-black/15"></div>
            <span>Est. {{ date('Y') }}</span>
        </div>
    </section>

    @include('shop.partials.trust-strip')

    {{-- ─────────── EDITORIAL COLLECTIONS ─────────── --}}
    @foreach($categories as $catIdx => $category)
        @if($category->products->isNotEmpty())
            <section class="py-section">
                <div class="container-wide">
                    {{-- Editorial header --}}
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-end mb-12 lg:mb-16">
                        <div class="lg:col-span-7" data-reveal>
                            <div class="flex items-baseline gap-4">
                                <span class="font-script text-script-lg text-brand-blue/30">{{ str_pad((string)($catIdx + 1), 2, '0', STR_PAD_LEFT) }}</span>
                                <p class="eyebrow">Collection</p>
                            </div>
                            <h2 class="mt-3 font-display text-display-lg text-brand-black">{{ $category->name }}</h2>
                            @if($category->description)
                                <p class="mt-5 text-brand-black/70 leading-relaxed max-w-lg">{{ $category->description }}</p>
                            @endif
                        </div>
                        <div class="lg:col-span-5 lg:text-right" data-reveal data-reveal-delay="200">
                            <a href="{{ route('category.show', $category->slug) }}" class="link-grow">
                                Explore {{ $category->name }}
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3"/>
                                </svg>
                            </a>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-x-6 gap-y-12">
                        @foreach($category->products->take(4) as $idx => $product)
                            <div data-reveal data-reveal-delay="{{ ($idx % 4) * 100 }}">
                                @include('shop.partials.product-card', ['product' => $product])
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            @if(! $loop->last)
                <div class="container-wide"><div class="rule"></div></div>
            @endif
        @endif
    @endforeach

    {{-- ─────────── BRAND STORY / SUTRA ─────────── --}}
    <section class="relative bg-brand-black text-surface-cream py-section overflow-hidden">
        <div aria-hidden="true" class="absolute inset-0 opacity-10 pointer-events-none">
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 font-script text-[20rem] lg:text-[28rem] text-brand-blue select-none whitespace-nowrap">सूत्र</div>
        </div>

        <div class="container-narrow text-center relative z-10">
            <p data-reveal class="eyebrow text-brand-skin">The Sutra</p>
            <h2 data-reveal data-reveal-delay="100" class="mt-6 font-script text-script-xl text-brand-blue">From soil, to skin,<br>to soil.</h2>

            <div data-reveal data-reveal-delay="200" class="mt-10 grid grid-cols-1 sm:grid-cols-3 gap-6 max-w-3xl mx-auto text-left">
                <div class="border-l-2 border-brand-blue pl-4">
                    <div class="font-display text-2xl text-surface-cream">Soil</div>
                    <p class="mt-2 text-sm text-surface-cream/70 leading-relaxed">Cotton sown in Indian soil, woven by Indian hands. Bharat's textile heritage — alive.</p>
                </div>
                <div class="border-l-2 border-brand-blue pl-4">
                    <div class="font-display text-2xl text-surface-cream">Skin</div>
                    <p class="mt-2 text-sm text-surface-cream/70 leading-relaxed">Breathable, soft, daily-wear cotton — no polyester, no plastic on your skin.</p>
                </div>
                <div class="border-l-2 border-brand-blue pl-4">
                    <div class="font-display text-2xl text-surface-cream">Soil</div>
                    <p class="mt-2 text-sm text-surface-cream/70 leading-relaxed">When the time comes, it returns to where it came from. Fully biodegradable.</p>
                </div>
            </div>

            <div data-reveal data-reveal-delay="300" class="mt-12">
                <a href="{{ route('about') }}" class="btn-outline border-surface-cream text-surface-cream hover:bg-surface-cream hover:text-brand-black">Read our story</a>
            </div>
        </div>
    </section>

    {{-- ─────────── INSTAGRAM TEASE ─────────── --}}
    <section class="py-section bg-surface-cream">
        <div class="container-wide">
            <div class="flex items-end justify-between mb-10" data-reveal>
                <div>
                    <p class="eyebrow">@sutraconscious</p>
                    <h2 class="mt-3 font-display text-display-md text-brand-black">Worn in the wild.</h2>
                </div>
                <a href="https://www.instagram.com/sutraconscious/" target="_blank" rel="noopener" class="link-grow hidden sm:inline-flex">Follow on Instagram</a>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-2 md:gap-3">
                @foreach($featured->take(4) as $idx => $product)
                    @php $img = $product->images->first(); @endphp
                    @if($img)
                        <a href="https://www.instagram.com/sutraconscious/" target="_blank" rel="noopener"
                           data-reveal data-reveal-delay="{{ $idx * 80 }}"
                           class="block aspect-square overflow-hidden group relative">
                            <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($img->path) }}"
                                 alt="" loading="lazy"
                                 class="w-full h-full object-cover transition-transform duration-700 ease-silk group-hover:scale-105 no-drag">
                            <div class="absolute inset-0 bg-brand-black/0 group-hover:bg-brand-black/40 flex items-center justify-center transition-colors duration-500">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-6 h-6 fill-surface-cream opacity-0 group-hover:opacity-100 transition-opacity duration-500">
                                    <path d="M12 2.2c3.2 0 3.584.012 4.85.07 1.366.062 2.633.336 3.608 1.311.975.975 1.249 2.242 1.311 3.608.058 1.266.07 1.65.07 4.85s-.012 3.584-.07 4.85c-.062 1.366-.336 2.633-1.311 3.608-.975.975-2.242 1.249-3.608 1.311-1.266.058-1.65.07-4.85.07s-3.584-.012-4.85-.07c-1.366-.062-2.633-.336-3.608-1.311C2.567 19.522 2.293 18.255 2.231 16.889 2.173 15.623 2.161 15.239 2.161 12.039s.012-3.584.07-4.85c.062-1.366.336-2.633 1.311-3.608C4.517 2.606 5.784 2.332 7.15 2.27 8.416 2.212 8.8 2.2 12 2.2zm0 1.8c-3.146 0-3.519.011-4.764.067-1.041.048-1.605.221-1.98.367-.498.193-.853.424-1.227.798-.374.374-.605.729-.798 1.227-.146.375-.319.939-.367 1.98C2.811 9.481 2.8 9.854 2.8 13s.011 3.519.067 4.764c.048 1.041.221 1.605.367 1.98.193.498.424.853.798 1.227.374.374.729.605 1.227.798.375.146.939.319 1.98.367 1.245.056 1.618.067 4.764.067s3.519-.011 4.764-.067c1.041-.048 1.605-.221 1.98-.367.498-.193.853-.424 1.227-.798.374-.374.605-.729.798-1.227.146-.375.319-.939.367-1.98.056-1.245.067-1.618.067-4.764s-.011-3.519-.067-4.764c-.048-1.041-.221-1.605-.367-1.98a3.302 3.302 0 00-.798-1.227 3.302 3.302 0 00-1.227-.798c-.375-.146-.939-.319-1.98-.367C15.519 4.011 15.146 4 12 4zm0 3.062a4.938 4.938 0 110 9.876 4.938 4.938 0 010-9.876zm0 1.8a3.138 3.138 0 100 6.276 3.138 3.138 0 000-6.276zm5.144-1.62a1.152 1.152 0 11-2.304 0 1.152 1.152 0 012.304 0z"/>
                                </svg>
                            </div>
                        </a>
                    @endif
                @endforeach
            </div>
        </div>
    </section>
@endsection
