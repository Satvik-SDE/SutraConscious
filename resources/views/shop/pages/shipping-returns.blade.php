@extends('shop.layouts.app', ['title' => 'Shipping & Returns — Sutra Conscious'])

@section('content')
    <section class="container-narrow py-16 lg:py-24" data-reveal>
        <p class="eyebrow">Policy</p>
        <h1 class="mt-4 font-display text-display-md text-brand-black">Shipping &amp; Returns</h1>
        <p class="mt-5 text-brand-black/70 text-lg leading-relaxed">Shipping from Bharat. Honest timelines. Easy exchanges.</p>

        <div class="mt-12 space-y-12">
            <article>
                <h2 class="font-display text-2xl text-brand-black">Shipping</h2>
                <ul class="mt-4 space-y-2 text-brand-black/80">
                    <li class="flex items-start gap-3"><span class="w-1.5 h-1.5 rounded-full bg-brand-blue mt-2.5 flex-shrink-0"></span>We ship across India.</li>
                    <li class="flex items-start gap-3"><span class="w-1.5 h-1.5 rounded-full bg-brand-blue mt-2.5 flex-shrink-0"></span>Orders are typically dispatched within 2–3 business days.</li>
                    <li class="flex items-start gap-3"><span class="w-1.5 h-1.5 rounded-full bg-brand-blue mt-2.5 flex-shrink-0"></span>Standard delivery: 4–7 business days.</li>
                    <li class="flex items-start gap-3"><span class="w-1.5 h-1.5 rounded-full bg-brand-blue mt-2.5 flex-shrink-0"></span>International shipping coming soon.</li>
                </ul>
            </article>

            <div class="rule"></div>

            <article>
                <h2 class="font-display text-2xl text-brand-black">Returns &amp; Exchanges</h2>
                <p class="mt-4 text-brand-black/80 leading-relaxed">
                    If the fit isn't right, write to <a href="mailto:sutra.conscious@gmail.com" class="text-brand-blue link-underline">sutra.conscious@gmail.com</a> within 7 days of delivery. We'll guide you through the exchange.
                </p>
            </article>
        </div>

        <p class="mt-14 text-xs uppercase tracking-[0.18em] text-brand-black/40">Placeholder · Final policy to be confirmed before launch.</p>
    </section>
@endsection
