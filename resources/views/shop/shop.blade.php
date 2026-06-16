@extends('shop.layouts.app', [
    'title' => 'Shop — Sutra Conscious',
    'metaDescription' => 'Browse conscious cotton clothing for men, women, and kids — crafted in Bharat.',
])

@section('content')
    <section class="relative bg-brand-skin/50 border-b border-surface-line overflow-hidden">
        <div class="container-wide py-16 lg:py-24 relative z-10">
            <p data-reveal class="eyebrow">The Collection</p>
            <h1 data-reveal data-reveal-delay="100" class="mt-4 font-display text-display-lg text-brand-black max-w-3xl">Shop All</h1>
            <p data-reveal data-reveal-delay="200" class="mt-5 max-w-xl text-brand-black/70 text-lg">100% premium cotton for men, women, and kids — crafted in Bharat.</p>
        </div>
        <div aria-hidden="true" class="hidden lg:block absolute -bottom-10 -right-6 font-script text-[16rem] text-brand-blue/10 select-none">Sutra</div>
    </section>

    <section class="py-section-sm">
        <div class="container-wide grid grid-cols-1 lg:grid-cols-[240px_1fr] gap-10 lg:gap-16">
            @include('shop.partials.catalog-sidebar', [
                'showFabricPledge' => true,
            ])

            <div>
                <div class="flex items-center justify-between mb-8">
                    <div class="text-sm text-brand-black/60"><span class="text-brand-black font-medium">{{ $products->count() }}</span> {{ Str::plural('piece', $products->count()) }}</div>
                </div>

                @if($products->isEmpty())
                    <div class="bg-brand-skin/30 border border-surface-line p-12 text-center">
                        <div class="font-script text-5xl text-brand-blue/40 mb-3">Sutra</div>
                        <p class="text-brand-black/60">New arrivals are loading.</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-12">
                        @foreach($products as $idx => $product)
                            <div data-reveal data-reveal-delay="{{ ($idx % 3) * 100 }}">
                                @include('shop.partials.product-card', ['product' => $product])
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection
