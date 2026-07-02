@extends('shop.layouts.app', [
    'title' => 'Our Story — Sutra Conscious',
    'metaDescription' => 'Our story: bringing back the soul of Bharat\'s cotton. From soil, to skin, to soil.',
])

@section('content')
    {{-- Hero --}}
    <section class="relative bg-brand-skin overflow-hidden">
        <div class="container-wide py-24 lg:py-32 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-16 items-center">
                <div class="lg:col-span-7" data-reveal>
                    <p class="eyebrow">Our Story</p>
                    <h1 class="mt-5 font-display text-display-lg text-brand-black max-w-3xl">A conscious choice for everyday cotton.</h1>
                    <p class="mt-8 text-lg text-brand-black/80 max-w-xl leading-relaxed">
                        Sutra Conscious is born from a simple belief — what touches our skin every day should be honest, breathable, and rooted in the soil it came from.
                    </p>
                </div>
                <div class="lg:col-span-5 flex justify-center lg:justify-end bg-transparent" data-reveal="right">
                    <img
                        src="{{ asset('img/about/founders.png') }}?v=4"
                        alt="The founders of Sutra Conscious"
                        loading="eager"
                        class="w-full max-w-sm lg:max-w-md h-auto object-contain bg-transparent"
                    >
                </div>
            </div>
        </div>
    </section>

    {{-- Pillars: stacked editorial --}}
    <section class="py-section">
        <div class="container-wide">
            @php
                $pillars = [
                    [
                        'num' => '01',
                        'title' => 'Plastic on the skin?',
                        'body' => 'For glamorous looks, at parties and functions, we tend to buy fancy stuff blended with polyester. Polyester is plastic. Wearing plastic on the skin is not good for health.',
                    ],
                    [
                        'num' => '02',
                        'title' => "Bharat's textile heritage.",
                        'body' => 'Our beloved Bharat was, and still is, a textile exporter. The quality of fabric today is to be questioned. The variety woven across Bharat is so rich and diverse — most of it is never even seen.',
                    ],
                    [
                        'num' => '03',
                        'title' => 'Old fashion. New mood.',
                        'body' => 'Modern fashion need not be imported from the west. With carefully selected fabrics, we bring a mix of old and new — cuts that fit today, fabric that has stood the test of time.',
                    ],
                    [
                        'num' => '04',
                        'title' => '100% Cotton.',
                        'body' => 'Every piece is 100% cotton. Perfectly suited for tropical weather. Once you use it, you fall in love with the fabric — and want to use it for life.',
                    ],
                    [
                        'num' => '05',
                        'title' => 'Checked by us, personally.',
                        'body' => 'Every fabric is verified by us to be 100% cotton — no blends, no shortcuts. Each piece is inspected again before it goes into stock and once more before it leaves for delivery.',
                    ],
                    [
                        'num' => '06',
                        'title' => 'Thank you, truly.',
                        'body' => 'We value our customers and thank them for choosing earth-friendly fabric — a handwritten note of gratitude goes out with every order.',
                        'image' => 'img/about/handwritten-tag.jpg',
                        'image_alt' => 'Handwritten thank-you tag from Sutra Conscious',
                    ],
                    [
                        'num' => '07',
                        'title' => 'Our mission.',
                        'body' => 'Our mission is to create a conscious world — and our customers are our real assets.',
                    ],
                ];
            @endphp

            <div class="space-y-16">
                @foreach($pillars as $idx => $p)
                    <article class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-12 items-start" data-reveal>
                        <div class="lg:col-span-3">
                            <div class="font-script text-7xl text-brand-blue/30 leading-none">{{ $p['num'] }}</div>
                        </div>
                        <div class="lg:col-span-9 max-w-2xl">
                            <h2 class="font-display text-3xl lg:text-4xl text-brand-black">{{ $p['title'] }}</h2>
                            <p class="mt-5 text-brand-black/75 text-lg leading-relaxed">{{ $p['body'] }}</p>

                            @if(! empty($p['image']))
                                @php $tagImagePath = public_path($p['image']); @endphp
                                <figure class="mt-8">
                                    @if(file_exists($tagImagePath))
                                        <div class="inline-block bg-brand-skin/40 border border-surface-line p-3 shadow-soft">
                                            <img
                                                src="{{ asset($p['image']) }}"
                                                alt="{{ $p['image_alt'] ?? '' }}"
                                                loading="lazy"
                                                class="max-w-full sm:max-w-md w-full h-auto"
                                            >
                                        </div>
                                    @else
                                        <div class="inline-block max-w-sm bg-brand-skin/50 border border-surface-line px-8 py-10 shadow-soft rotate-[-1deg]">
                                            <p class="font-script text-3xl text-brand-blue leading-snug">Thank you</p>
                                            <p class="mt-3 text-sm text-brand-black/70 leading-relaxed">for choosing earth-friendly cotton.</p>
                                            <p class="mt-6 font-script text-xl text-brand-black/80">— Sutra Conscious</p>
                                        </div>
                                        <figcaption class="mt-3 text-[0.7rem] uppercase tracking-[0.18em] text-brand-black/45">Handwritten thank-you tag with every order</figcaption>
                                    @endif
                                </figure>
                            @endif
                        </div>
                    </article>

                    @if(! $loop->last)
                        <div class="rule"></div>
                    @endif
                @endforeach
            </div>
        </div>
    </section>

    {{-- Closing block --}}
    <section class="bg-brand-black text-surface-cream py-section relative overflow-hidden">
        <div class="container-narrow text-center relative z-10" data-reveal>
            <p class="eyebrow text-brand-skin">The Sutra</p>
            <h2 class="mt-6 font-script text-script-xl text-brand-blue">From soil, to skin, to soil.</h2>
            <p class="mt-8 text-surface-cream/80 max-w-xl mx-auto text-lg leading-relaxed">
                And when its time comes, the soil can decompose it completely. As per nature's law, the fabric is sourced from soil and goes back to soil.
            </p>
            <div class="mt-10">
                <a href="{{ route('shop') }}" class="btn-outline border-surface-cream text-surface-cream hover:bg-surface-cream hover:text-brand-black">Shop the Collection</a>
            </div>
        </div>
    </section>

    @include('shop.partials.trust-strip')
@endsection
