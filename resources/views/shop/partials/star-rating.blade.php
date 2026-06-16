@props([
    'rating' => 0,
    'size' => 'sm',
])

@php
    $rating = max(0, min(5, (float) $rating));
    $sizeClass = match ($size) {
        'lg' => 'w-5 h-5',
        'md' => 'w-4 h-4',
        default => 'w-3.5 h-3.5',
    };
@endphp

<div {{ $attributes->merge(['class' => 'inline-flex items-center gap-0.5']) }} aria-label="{{ $rating }} out of 5 stars">
    @for($star = 1; $star <= 5; $star++)
        @php
            $filled = $rating >= $star;
            $partial = ! $filled && $rating > ($star - 1);
        @endphp
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="{{ $sizeClass }} {{ $filled || $partial ? 'text-brand-blue' : 'text-brand-black/15' }}" fill="currentColor" aria-hidden="true">
            @if($partial)
                <defs>
                    <linearGradient id="star-partial-{{ $star }}-{{ md5((string) $rating) }}">
                        <stop offset="0%" stop-color="currentColor"/>
                        <stop offset="{{ (($rating - ($star - 1)) * 100) }}%" stop-color="currentColor"/>
                        <stop offset="{{ (($rating - ($star - 1)) * 100) }}%" stop-color="transparent"/>
                        <stop offset="100%" stop-color="transparent"/>
                    </linearGradient>
                </defs>
                <path fill="url(#star-partial-{{ $star }}-{{ md5((string) $rating) }})" d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 0 0 .95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 0 0-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 0 0-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 0 0-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 0 0 .951-.69l1.07-3.292z"/>
            @else
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 0 0 .95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 0 0-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 0 0-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 0 0-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 0 0 .951-.69l1.07-3.292z"/>
            @endif
        </svg>
    @endfor
</div>
