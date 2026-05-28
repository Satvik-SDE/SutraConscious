@extends('shop.layouts.app', [
    'title' => ($product->seo_title ?: $product->name) . ' — Sutra Conscious',
    'metaDescription' => $product->seo_description ?: $product->short_description ?: '100% premium cotton kurta from Sutra Conscious.',
    'ogType' => 'product',
])

@push('head')
    @php
        $primary = $product->images->first();
        $productLd = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product->name,
            'description' => $product->short_description ?: ($product->seo_description ?: '100% Premium Cotton Kurta by Sutra Conscious'),
            'sku' => $product->slug,
            'brand' => ['@type' => 'Brand', 'name' => 'Sutra Conscious'],
            'image' => $primary ? \Illuminate\Support\Facades\Storage::disk('public')->url($primary->path) : asset('img/brand/logo.png'),
            'offers' => [
                '@type' => 'Offer',
                'priceCurrency' => $product->currency ?: 'INR',
                'price' => (string) $product->base_price,
                'availability' => $product->variants->sum('stock') > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
                'url' => route('product.show', $product->slug),
            ],
        ];

        $breadcrumbLd = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => array_values(array_filter([
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => route('home')],
                ['@type' => 'ListItem', 'position' => 2, 'name' => 'Shop', 'item' => route('shop')],
                $product->category ? ['@type' => 'ListItem', 'position' => 3, 'name' => $product->category->name, 'item' => route('category.show', $product->category->slug)] : null,
                ['@type' => 'ListItem', 'position' => $product->category ? 4 : 3, 'name' => $product->name, 'item' => route('product.show', $product->slug)],
            ])),
        ];
    @endphp
    <script type="application/ld+json">{!! json_encode($productLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    <script type="application/ld+json">{!! json_encode($breadcrumbLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush

@section('content')
    <section class="container-bleed py-10 lg:py-16">

        {{-- Breadcrumb --}}
        <nav class="text-[0.7rem] uppercase tracking-[0.18em] text-brand-black/60 mb-8 flex items-center gap-2" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="hover:text-brand-blue">Home</a>
            <span class="text-brand-black/30">/</span>
            <a href="{{ route('shop') }}" class="hover:text-brand-blue">Shop</a>
            @if($product->category)
                <span class="text-brand-black/30">/</span>
                <a href="{{ route('category.show', $product->category->slug) }}" class="hover:text-brand-blue">{{ $product->category->name }}</a>
            @endif
            <span class="text-brand-black/30">/</span>
            <span class="text-brand-black truncate">{{ $product->name }}</span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12">

            {{-- ────────── GALLERY ────────── --}}
            @php $hasMultipleImages = $product->images->count() > 1; @endphp
            <div class="lg:col-span-7 xl:col-span-8" x-data="{ active: 0 }" data-reveal>
                <div class="{{ $hasMultipleImages ? 'grid grid-cols-1 lg:grid-cols-[100px_1fr] gap-4 lg:gap-6' : '' }}">
                    {{-- Thumbnails (desktop, vertical) --}}
                    @if($hasMultipleImages)
                        <div class="hidden lg:flex flex-col gap-3 lg:order-1">
                            @foreach($product->images as $img)
                                <button type="button"
                                        @click="active = {{ $loop->index }}"
                                        class="aspect-product overflow-hidden border transition-all duration-300"
                                        :class="active === {{ $loop->index }} ? 'border-brand-blue ring-1 ring-brand-blue/40' : 'border-surface-line hover:border-brand-blue/40'"
                                        aria-label="View image {{ $loop->iteration }}">
                                    <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($img->path) }}" alt="" loading="lazy" class="w-full h-full object-cover no-drag">
                                </button>
                            @endforeach
                        </div>
                    @endif

                    {{-- Main image (zoomable) --}}
                    <div class="{{ $hasMultipleImages ? 'lg:order-2' : '' }} lg:sticky lg:top-28 relative">
                        <div class="absolute top-4 right-4 z-20">
                            @include('shop.partials.wishlist-button', ['product' => $product, 'size' => 'lg'])
                        </div>
                        <div class="aspect-product bg-brand-skin/30 border border-surface-line overflow-hidden cursor-zoom-in" data-zoom>
                            @if($product->images->isNotEmpty())
                                @foreach($product->images as $img)
                                    <img
                                        x-show="active === {{ $loop->index }}"
                                        src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($img->path) }}"
                                        alt="{{ $img->alt ?? $product->name }}"
                                        class="w-full h-full object-cover no-drag transition-transform duration-500 ease-silk"
                                        @if(! $loop->first) x-cloak @endif
                                    >
                                @endforeach
                            @else
                                <div class="w-full h-full flex items-center justify-center text-brand-black/30 text-[0.7rem] uppercase tracking-[0.2em]">No image yet</div>
                            @endif
                        </div>

                        {{-- Thumbnails (mobile, horizontal) --}}
                        @if($hasMultipleImages)
                            <div class="lg:hidden mt-4 grid grid-cols-5 gap-2">
                                @foreach($product->images as $img)
                                    <button type="button"
                                            @click="active = {{ $loop->index }}"
                                            class="aspect-square overflow-hidden border transition-all"
                                            :class="active === {{ $loop->index }} ? 'border-brand-blue' : 'border-surface-line'">
                                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($img->path) }}" alt="" loading="lazy" class="w-full h-full object-cover">
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ────────── DETAILS ────────── --}}
            <div class="lg:col-span-5 xl:col-span-4">
                <div class="lg:pl-4">
                    @if($product->category)
                        <p class="eyebrow">{{ $product->category->name }}</p>
                    @endif

                    <h1 data-reveal class="mt-3 font-display text-display-md text-brand-black">{{ $product->name }}</h1>

                    @if($product->color_label)
                        <div data-reveal data-reveal-delay="100" class="mt-3 text-sm text-brand-black/60 uppercase tracking-[0.2em]">{{ $product->color_label }}</div>
                    @endif

                    <div data-reveal data-reveal-delay="200" class="mt-6 flex items-baseline gap-4">
                        <span class="text-3xl font-display text-brand-blue">₹{{ number_format($product->base_price) }}</span>
                        <span class="text-[0.7rem] uppercase tracking-[0.18em] text-brand-black/40">incl. all taxes</span>
                    </div>

                    @if($product->short_description)
                        <p data-reveal data-reveal-delay="300" class="mt-6 text-brand-black/75 leading-relaxed">{{ $product->short_description }}</p>
                    @endif

                    {{-- Material chips --}}
                    <div data-reveal data-reveal-delay="400" class="mt-6 flex flex-wrap gap-2">
                        <span class="chip"><span class="w-1.5 h-1.5 rounded-full bg-brand-blue"></span>{{ $product->fabric }}</span>
                        @if($product->sleeve)
                            <span class="chip"><span class="w-1.5 h-1.5 rounded-full bg-brand-blue"></span>{{ $product->sleeve }}</span>
                        @endif
                        <span class="chip"><span class="w-1.5 h-1.5 rounded-full bg-brand-blue"></span>Made in Bharat</span>
                    </div>

                    {{-- Variant picker --}}
                    @if($product->variants->isNotEmpty())
                        <form action="{{ route('cart.add') }}" method="POST" class="mt-10" x-data="{ variant: '{{ $product->variants->first()->id }}', qty: 1 }">
                            @csrf
                            <input type="hidden" name="variant_id" :value="variant">
                            <input type="hidden" name="quantity" :value="qty">

                            <div class="flex items-center justify-between mb-3">
                                <div class="field-label mb-0">Size</div>
                                <button type="button" class="text-[0.7rem] uppercase tracking-[0.18em] text-brand-black/60 hover:text-brand-blue underline underline-offset-4">Size guide</button>
                            </div>

                            <div class="flex flex-wrap gap-2 mb-7">
                                @foreach($product->variants as $variant)
                                    <button type="button"
                                            @click="variant = '{{ $variant->id }}'"
                                            class="w-12 h-12 inline-flex items-center justify-center border transition-all duration-300 ease-silk text-sm font-medium relative"
                                            :class="variant === '{{ $variant->id }}' ? 'border-brand-blue bg-brand-blue text-surface-cream' : 'border-surface-line text-brand-black hover:border-brand-blue'"
                                            @if($variant->stock <= 0) disabled @endif
                                            aria-label="Size {{ $variant->size }}">
                                        {{ $variant->size }}
                                        @if($variant->stock <= 0)
                                            <span aria-hidden="true" class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                                <span class="w-12 h-px bg-brand-black/30 rotate-[-30deg]"></span>
                                            </span>
                                        @endif
                                    </button>
                                @endforeach
                            </div>

                            <div class="field-label">Quantity</div>
                            <div class="inline-flex items-center border border-surface-line mb-8 select-none">
                                <button type="button" @click="qty = Math.max(1, qty - 1)" class="w-10 h-11 text-brand-black hover:bg-brand-skin/40 transition-colors" aria-label="Decrease">−</button>
                                <div class="w-12 h-11 border-x border-surface-line flex items-center justify-center text-sm" x-text="qty"></div>
                                <button type="button" @click="qty = Math.min(10, qty + 1)" class="w-10 h-11 text-brand-black hover:bg-brand-skin/40 transition-colors" aria-label="Increase">+</button>
                            </div>

                            @if(session('status'))
                                <div class="mb-4 text-brand-blue text-sm flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                                    {{ session('status') }}
                                </div>
                            @endif
                            @error('cart')
                                <div class="mb-4 text-red-600 text-sm">{{ $message }}</div>
                            @enderror

                            <div class="flex flex-col gap-3">
                                <button type="submit" class="btn-primary w-full justify-center">
                                    Add to Bag
                                    <span class="text-surface-cream/70">·</span>
                                    <span>₹<span x-text="(qty * {{ $product->base_price }}).toLocaleString('en-IN')"></span></span>
                                </button>
                                <a href="{{ route('shop') }}" class="text-center text-[0.78rem] uppercase tracking-[0.2em] text-brand-black/60 hover:text-brand-blue transition-colors">Continue shopping</a>
                            </div>
                        </form>
                    @else
                        <div class="mt-8 bg-brand-skin/40 border border-surface-line p-4 text-sm text-brand-black/70">
                            Sizes coming soon for this product.
                        </div>
                    @endif

                    {{-- Accordions --}}
                    <div class="mt-12 divide-y divide-surface-line border-y border-surface-line" x-data="{ open: 'details' }">
                        @foreach([
                            ['key' => 'details', 'title' => 'The Details', 'body' => $product->description ?: '<p>'. e($product->short_description ?: 'Thoughtfully cut from 100% premium cotton. Breathable, soft, daily-wear ready.') .'</p>'],
                            ['key' => 'fabric',  'title' => 'Fabric &amp; Care', 'body' => '<p><strong>Fabric:</strong> '. e($product->fabric) .'</p><p class="mt-2">Wash cold with similar colours. Line dry in shade. Light iron on the reverse to retain handfeel.</p>'],
                            ['key' => 'shipping','title' => 'Shipping &amp; Returns', 'body' => '<p>Ships across India in 4–7 business days. International shipping coming soon.</p><p class="mt-2">7-day easy exchanges. Read the <a class="text-brand-blue underline underline-offset-4" href="'. route('shipping-returns') .'">full policy</a>.</p>'],
                        ] as $acc)
                            <div>
                                <button type="button" @click="open = open === '{{ $acc['key'] }}' ? '' : '{{ $acc['key'] }}'" class="w-full flex items-center justify-between py-5 text-left">
                                    <span class="text-sm uppercase tracking-[0.2em] font-medium text-brand-black">{{ $acc['title'] }}</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-brand-black transition-transform duration-300" :class="open === '{{ $acc['key'] }}' ? 'rotate-45' : ''" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                                    </svg>
                                </button>
                                <div x-show="open === '{{ $acc['key'] }}'"
                                     x-transition:enter="transition ease-silk duration-400"
                                     x-transition:enter-start="opacity-0 -translate-y-1"
                                     x-transition:enter-end="opacity-100 translate-y-0"
                                     x-cloak>
                                    <div class="pb-5 prose prose-sm max-w-none text-brand-black/75">
                                        {!! $acc['body'] !!}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if($related->isNotEmpty())
        <section class="py-section bg-brand-skin/30 border-t border-surface-line">
            <div class="container-wide">
                <div class="flex items-end justify-between mb-12" data-reveal>
                    <div>
                        <p class="eyebrow">More to explore</p>
                        <h2 class="mt-3 font-display text-display-md text-brand-black">You may also like</h2>
                    </div>
                    <a href="{{ route('shop') }}" class="link-grow hidden sm:inline-flex">View all kurtas</a>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-x-6 gap-y-12">
                    @foreach($related as $idx => $product)
                        <div data-reveal data-reveal-delay="{{ $idx * 80 }}">
                            @include('shop.partials.product-card', ['product' => $product])
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
