@extends('shop.layouts.app', [
    'title' => 'Shop All Kurtas — Sutra Conscious',
    'metaDescription' => 'Browse 100% premium cotton kurtas crafted in Bharat.',
])

@section('content')
    {{-- Hero band --}}
    <section class="relative bg-brand-skin/50 border-b border-surface-line overflow-hidden">
        <div class="container-wide py-16 lg:py-24 relative z-10">
            <p data-reveal class="eyebrow">The Collection</p>
            <h1 data-reveal data-reveal-delay="100" class="mt-4 font-display text-display-lg text-brand-black max-w-3xl">All Kurtas</h1>
            <p data-reveal data-reveal-delay="200" class="mt-5 max-w-xl text-brand-black/70 text-lg">100% premium cotton. Crafted in Bharat. Built for everyday wear.</p>
        </div>
        <div aria-hidden="true" class="hidden lg:block absolute -bottom-10 -right-6 font-script text-[16rem] text-brand-blue/10 select-none">Sutra</div>
    </section>

    <section class="py-section-sm">
        <div class="container-wide grid grid-cols-1 lg:grid-cols-[240px_1fr] gap-10 lg:gap-16">
            {{-- Sidebar --}}
            <aside data-reveal="left" class="lg:sticky lg:top-28 lg:self-start space-y-8">
                <div>
                    <div class="eyebrow-dim mb-4">Collections</div>
                    <ul class="space-y-3">
                        <li>
                            <a href="{{ route('shop') }}" class="block text-sm tracking-wide {{ $activeCategory === null ? 'text-brand-blue font-medium' : 'text-brand-black hover:text-brand-blue' }}">
                                <span class="inline-flex items-center gap-2">
                                    @if($activeCategory === null)<span class="w-1.5 h-1.5 rounded-full bg-brand-blue"></span>@endif
                                    All Kurtas
                                </span>
                            </a>
                        </li>
                        @foreach($categories as $cat)
                            <li>
                                <a href="{{ route('category.show', $cat->slug) }}" class="block text-sm tracking-wide {{ $activeCategory === $cat->slug ? 'text-brand-blue font-medium' : 'text-brand-black hover:text-brand-blue' }}">
                                    <span class="inline-flex items-center gap-2">
                                        @if($activeCategory === $cat->slug)<span class="w-1.5 h-1.5 rounded-full bg-brand-blue"></span>@endif
                                        {{ $cat->name }}
                                    </span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="rule"></div>

                <div>
                    <div class="eyebrow-dim mb-4">Sizes</div>
                    <div class="flex flex-wrap gap-2">
                        @foreach(['S', 'M', 'L', 'XL'] as $size)
                            <span class="px-3 py-1.5 border border-surface-line text-xs uppercase tracking-[0.18em] text-brand-black/60">{{ $size }}</span>
                        @endforeach
                    </div>
                </div>

                <div class="rule"></div>

                <div class="bg-brand-skin/30 border border-surface-line p-5">
                    <div class="eyebrow-dim mb-2">Fabric Pledge</div>
                    <p class="text-sm text-brand-black/75 leading-relaxed">Every Sutra Conscious piece is 100% premium cotton. No blends. No synthetics. Ever.</p>
                </div>
            </aside>

            {{-- Grid --}}
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
