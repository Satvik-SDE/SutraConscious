@extends('shop.layouts.app', ['title' => 'Wishlist — Sutra Conscious'])

@section('content')
    <section class="container-wide py-12 lg:py-20">
        <div class="mb-10" data-reveal>
            <p class="eyebrow">Saved</p>
            <h1 class="mt-3 font-display text-display-md text-brand-black">Your wishlist</h1>
            <p class="mt-3 text-sm text-brand-black/60">Pieces you love — ready when you are.</p>
        </div>

        @if(session('status'))
            <p class="mb-8 text-sm text-brand-blue" data-reveal>{{ session('status') }}</p>
        @endif

        @if($items->isEmpty())
            <div class="border border-surface-line bg-surface-cream p-12 text-center" data-reveal>
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full border border-surface-line text-brand-black/30 mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z"/>
                    </svg>
                </div>
                <p class="text-brand-black/70">Your wishlist is empty.</p>
                <a href="{{ route('shop') }}" class="btn-primary mt-8 inline-flex">Explore the shop</a>
            </div>
        @else
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-x-6 gap-y-12">
                @foreach($items as $idx => $item)
                    @php $product = $item->product; @endphp
                    @if($product && $product->is_active)
                        <div data-reveal data-reveal-delay="{{ min($idx * 60, 300) }}">
                            @include('shop.partials.product-card', ['product' => $product])
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
    </section>
@endsection
