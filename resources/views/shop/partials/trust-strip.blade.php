@php
    $badges = [
        [
            'title' => '100% Cotton',
            'desc'  => 'No blends. Ever.',
            'svg'   => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 21c-4.5 0-7.5-2.5-7.5-7.5C4.5 8.5 7.5 4 12 3c4.5 1 7.5 5.5 7.5 10.5C19.5 18.5 16.5 21 12 21Z M12 3v18 M4.5 13.5h15"/>',
        ],
        [
            'title' => 'Crafted in Bharat',
            'desc'  => 'Heritage textiles.',
            'svg'   => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5 M3.75 21V8.25h16.5V21 M9 21v-4.5h6V21 M3.75 8.25 12 3l8.25 5.25"/>',
        ],
        [
            'title' => 'Breathes Tropical',
            'desc'  => 'Made for our weather.',
            'svg'   => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25 M12 18.75V21 M5.636 5.636l1.591 1.591 M16.773 16.773l1.591 1.591 M3 12h2.25 M18.75 12H21 M5.636 18.364l1.591-1.591 M16.773 7.227l1.591-1.591 M16.5 12a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0Z"/>',
        ],
        [
            'title' => 'Decades of Wear',
            'desc'  => 'Softens with each wash.',
            'svg'   => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5 M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>',
        ],
        [
            'title' => 'Soil-to-Soil',
            'desc'  => 'Fully biodegradable.',
            'svg'   => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5h18 M5.25 16.5V21h13.5v-4.5 M12 16.5V8 M8 12c0-3 2-5 4-5s4 2 4 5"/>',
        ],
        [
            'title' => 'No Synthetics',
            'desc'  => 'Plastic-free on skin.',
            'svg'   => '<path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>',
        ],
    ];
@endphp

<section class="bg-surface-cream border-y border-surface-line">
    <div class="container-bleed py-8 sm:py-10">
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-x-6 gap-y-8">
            @foreach($badges as $i => $b)
                <div data-reveal data-reveal-delay="{{ $i * 80 }}" class="flex items-center gap-3">
                    <div class="shrink-0 w-10 h-10 rounded-full bg-brand-skin/40 inline-flex items-center justify-center text-brand-blue">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.4" stroke="currentColor" class="w-5 h-5">
                            {!! $b['svg'] !!}
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <div class="text-[0.78rem] font-medium text-brand-black uppercase tracking-[0.12em] leading-tight">{{ $b['title'] }}</div>
                        <div class="text-[0.72rem] text-brand-black/55 mt-0.5">{{ $b['desc'] }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
