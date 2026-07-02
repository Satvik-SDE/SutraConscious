@php
    $catImage = $products->first()?->images?->first();
    $catImageUrl = $catImage ? \Illuminate\Support\Facades\Storage::disk('public')->url($catImage->path) : null;
@endphp

@extends('shop.layouts.app', [
    'title' => ($category->seo_title ?: $category->name) . ' — Sutra Conscious',
    'metaDescription' => $category->seo_description ?: ($category->description ?: 'Shop 100% cotton ' . $category->name . ' — conscious clothing crafted in Bharat.'),
    'ogImage' => $catImageUrl,
    'ogImageAlt' => $category->name,
])

@section('content')
    <section class="relative bg-brand-skin/50 border-b border-surface-line overflow-hidden">
        <div class="container-wide py-16 lg:py-24 relative z-10">
            <nav class="text-[0.7rem] uppercase tracking-[0.18em] text-brand-black/60 mb-6 flex items-center gap-2 flex-wrap" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="hover:text-brand-blue">Home</a>
                <span class="text-brand-black/30">/</span>
                <a href="{{ route('shop') }}" class="hover:text-brand-blue">Shop</a>
                @if($category->department)
                    <span class="text-brand-black/30">/</span>
                    <a href="{{ route('department.show', $category->department->slug) }}" class="hover:text-brand-blue">{{ $category->department->name }}</a>
                @endif
                <span class="text-brand-black/30">/</span>
                <span class="text-brand-black">{{ $category->name }}</span>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-end">
                <div class="lg:col-span-7" data-reveal>
                    <p class="eyebrow">Collection</p>
                    <h1 class="mt-3 font-display text-display-lg text-brand-black">{{ $category->name }}</h1>
                </div>
                @if($category->description)
                    <p class="lg:col-span-5 text-brand-black/75 leading-relaxed" data-reveal data-reveal-delay="200">{{ $category->description }}</p>
                @endif
            </div>
        </div>
        <div aria-hidden="true" class="hidden lg:block absolute -bottom-10 -right-6 font-script text-[16rem] text-brand-blue/10 select-none">{{ Str::words($category->name, 1, '') }}</div>
    </section>

    <section class="py-section-sm">
        <div class="container-wide grid grid-cols-1 lg:grid-cols-[240px_1fr] gap-10 lg:gap-16">
            @include('shop.partials.catalog-sidebar', [
                'sidebarNoteTitle' => 'About this collection',
                'sidebarNoteBody' => $category->description ?: '100% cotton, woven for everyday wear.',
            ])

            <div>
                <div class="flex items-center justify-between mb-8">
                    <div class="text-sm text-brand-black/60"><span class="text-brand-black font-medium">{{ $products->count() }}</span> {{ Str::plural('piece', $products->count()) }} in this collection</div>
                </div>

                @if($products->isEmpty())
                    <div class="bg-brand-skin/30 border border-surface-line p-12 text-center">
                        <div class="font-script text-5xl text-brand-blue/40 mb-3">Sutra</div>
                        <p class="text-brand-black/60">This collection is being prepared.</p>
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
