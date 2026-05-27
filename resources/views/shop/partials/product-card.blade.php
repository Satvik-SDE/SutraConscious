@php
    /** @var \App\Models\Product $product */
    $images = $product->images;
    $primary = $images->first();
    $alt = $images->skip(1)->first();
    $sizes = $product->variants->where('is_active', true)->pluck('size')->unique()->values();
    $hasStock = $product->variants->sum('stock') > 0;
@endphp

<a href="{{ route('product.show', $product->slug) }}"
   class="group block focus-visible:outline-offset-4">
    <div class="product-media aspect-product bg-brand-skin/30 border border-surface-line">
        @if($primary)
            <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($primary->path) }}"
                 alt="{{ $primary->alt ?? $product->name }}"
                 loading="lazy"
                 decoding="async"
                 class="media-primary {{ $alt ? '' : 'media-solo' }} w-full h-full object-cover no-drag">
        @else
            <div class="w-full h-full flex items-center justify-center text-brand-black/30 text-[0.7rem] uppercase tracking-[0.2em]">No image</div>
        @endif

        @if($alt)
            <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($alt->path) }}"
                 alt="{{ $alt->alt ?? $product->name }}"
                 loading="lazy"
                 decoding="async"
                 class="media-alt w-full h-full object-cover no-drag">
        @endif

        @if($product->is_featured)
            <span class="absolute top-3 left-3 chip bg-brand-black text-surface-cream">New</span>
        @endif
        @if(! $hasStock)
            <span class="absolute top-3 right-3 chip bg-surface-cream/90 text-brand-black/60">Sold out</span>
        @endif

        {{-- Hover quick-view --}}
        <div class="absolute inset-x-0 bottom-0 translate-y-full group-hover:translate-y-0 transition-transform duration-500 ease-silk bg-surface-cream/95 backdrop-blur border-t border-surface-line py-3 px-4">
            <div class="flex items-center justify-between gap-3">
                <span class="text-[0.7rem] uppercase tracking-[0.2em] text-brand-blue font-medium">View product</span>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="w-4 h-4 text-brand-blue">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 19.5 15-15m0 0H8.25m11.25 0v11.25"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="mt-5">
        @if($product->category)
            <div class="text-[0.65rem] uppercase tracking-[0.3em] text-brand-black/45 mb-1.5">{{ $product->category->name }}</div>
        @endif
        <div class="flex items-baseline justify-between gap-3">
            <h3 class="font-medium text-brand-black group-hover:text-brand-blue transition-colors leading-tight">{{ $product->name }}</h3>
            <div class="text-brand-black font-medium whitespace-nowrap">₹{{ number_format($product->base_price) }}</div>
        </div>
        @if($sizes->isNotEmpty())
            <div class="mt-2 flex items-center gap-1.5">
                @foreach($sizes as $size)
                    <span class="text-[0.65rem] uppercase tracking-[0.18em] text-brand-black/45">{{ $size }}</span>
                    @unless($loop->last)
                        <span class="w-px h-2 bg-surface-line"></span>
                    @endunless
                @endforeach
            </div>
        @endif
    </div>
</a>
