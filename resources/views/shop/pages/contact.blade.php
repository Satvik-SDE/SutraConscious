@extends('shop.layouts.app', ['title' => 'Contact — Sutra Conscious'])

@section('content')
    <section class="container-wide py-16 lg:py-24">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-20">
            <div class="lg:col-span-5" data-reveal>
                <p class="eyebrow">Get in touch</p>
                <h1 class="mt-5 font-display text-display-lg text-brand-black">Say hello.</h1>
                <p class="mt-6 text-brand-black/70 text-lg leading-relaxed max-w-md">
                    We're a small, hands-on team. Email is best — we read everything.
                </p>

                <div class="mt-12 font-script text-script-lg text-brand-blue/80 leading-none">सूत्र</div>
            </div>

            <div class="lg:col-span-7 space-y-1" data-reveal data-reveal-delay="200">
                @php
                    $rows = [
                        ['label' => 'Email', 'value' => 'sutra.conscious@gmail.com', 'href' => 'mailto:sutra.conscious@gmail.com'],
                        ['label' => 'Phone', 'value' => '+91 93215 39748', 'href' => 'tel:+919321539748'],
                        ['label' => 'Instagram', 'value' => '@sutraconscious', 'href' => 'https://www.instagram.com/sutraconscious/'],
                        ['label' => 'Founders', 'value' => 'Shuchi & Adit', 'href' => null],
                        ['label' => 'Based in', 'value' => 'Bharat', 'href' => null],
                    ];
                @endphp

                @foreach($rows as $row)
                    <div class="grid grid-cols-[120px_1fr] gap-6 py-6 border-b border-surface-line items-center">
                        <div class="text-[0.7rem] uppercase tracking-[0.25em] text-brand-black/50">{{ $row['label'] }}</div>
                        <div>
                            @if($row['href'])
                                <a href="{{ $row['href'] }}" target="{{ Str::startsWith($row['href'], 'http') ? '_blank' : '_self' }}" rel="noopener" class="text-xl text-brand-black hover:text-brand-blue transition-colors link-underline">{{ $row['value'] }}</a>
                            @else
                                <div class="text-xl text-brand-black">{{ $row['value'] }}</div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection
