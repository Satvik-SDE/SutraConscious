@php
    $messages = [
        '100% Premium Cotton · Crafted in Bharat',
        'Free shipping across India on orders above ₹2,499',
        'Soft new arrivals — The Solids Edition',
        'No synthetics. No compromises. From soil, to skin, to soil.',
    ];
@endphp

<div class="bg-brand-black text-surface-cream/90 text-[0.7rem] tracking-[0.18em] uppercase font-medium overflow-hidden">
    <div class="container-bleed py-2.5 relative overflow-hidden">
        <div class="marquee-track">
            @for ($i = 0; $i < 2; $i++)
                @foreach($messages as $msg)
                    <span class="inline-flex items-center gap-3">
                        <span class="w-1 h-1 rounded-full bg-brand-blue"></span>
                        <span>{{ $msg }}</span>
                    </span>
                @endforeach
            @endfor
        </div>
    </div>
</div>
