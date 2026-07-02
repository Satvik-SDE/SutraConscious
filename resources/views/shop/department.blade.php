@php
    $deptImage = $products->first()?->images?->first();
    $deptImageUrl = $department->heroImageUrl()
        ?: ($deptImage ? \Illuminate\Support\Facades\Storage::disk('public')->url($deptImage->path) : null);
@endphp

@extends('shop.layouts.app', [
    'title' => ($department->seo_title ?: $department->name) . ' — Sutra Conscious',
    'metaDescription' => $department->seo_description ?: ($department->description ?: 'Shop conscious 100% cotton clothing at Sutra Conscious.'),
    'ogImage' => $deptImageUrl,
    'ogImageAlt' => $department->name,
])

@section('content')
    <section class="relative bg-brand-skin/50 border-b border-surface-line overflow-hidden">
        <div class="container-wide py-16 lg:py-24 relative z-10">
            <nav class="text-[0.7rem] uppercase tracking-[0.18em] text-brand-black/60 mb-6 flex items-center gap-2 flex-wrap" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="hover:text-brand-blue">Home</a>
                <span class="text-brand-black/30">/</span>
                <a href="{{ route('shop') }}" class="hover:text-brand-blue">Shop</a>
                <span class="text-brand-black/30">/</span>
                <span class="text-brand-black">{{ $department->name }}</span>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-end">
                <div class="lg:col-span-7" data-reveal>
                    <p class="eyebrow">Shop section</p>
                    <h1 class="mt-3 font-display text-display-lg text-brand-black">{{ $department->name }}</h1>
                </div>
                @if($department->description)
                    <p class="lg:col-span-5 text-brand-black/75 leading-relaxed" data-reveal data-reveal-delay="200">{{ $department->description }}</p>
                @endif
            </div>
        </div>
        <div aria-hidden="true" class="hidden lg:block absolute -bottom-10 -right-6 font-script text-[16rem] text-brand-blue/10 select-none">{{ Str::words($department->name, 1, '') }}</div>
    </section>

    <section class="py-section-sm">
        <div class="container-wide grid grid-cols-1 lg:grid-cols-[240px_1fr] gap-10 lg:gap-16">
            @include('shop.partials.catalog-sidebar', [
                'showFabricPledge' => true,
            ])

            <div>
                <div class="flex items-center justify-between mb-8">
                    <div class="text-sm text-brand-black/60"><span class="text-brand-black font-medium">{{ $products->count() }}</span> {{ Str::plural('piece', $products->count()) }} in {{ $department->name }}</div>
                </div>

                @if($products->isEmpty())
                    <div class="bg-brand-skin/30 border border-surface-line p-12 text-center">
                        <div class="font-script text-5xl text-brand-blue/40 mb-3">Sutra</div>
                        <p class="text-brand-black/60">New arrivals for this section are on the way.</p>
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
