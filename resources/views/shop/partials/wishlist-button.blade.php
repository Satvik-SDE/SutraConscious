@php
    /** @var \App\Models\Product $product */
    $inWishlist = in_array($product->id, $wishlistProductIds ?? [], true);
    $size = $size ?? 'md';
    $classes = $size === 'lg'
        ? 'w-11 h-11'
        : 'w-9 h-9';
@endphp

<form action="{{ route('wishlist.toggle', $product) }}" method="POST" class="inline-flex">
    @csrf
    <button type="submit"
            class="{{ $classes }} inline-flex items-center justify-center rounded-full border transition-all duration-300 ease-silk
                {{ $inWishlist ? 'border-brand-blue bg-brand-blue text-surface-cream' : 'border-surface-line bg-surface-cream/90 text-brand-black hover:border-brand-blue hover:text-brand-blue backdrop-blur-sm' }}"
            aria-label="{{ $inWishlist ? 'Remove from wishlist' : 'Add to wishlist' }}">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-[18px] h-[18px]" fill="{{ $inWishlist ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z"/>
        </svg>
    </button>
</form>
