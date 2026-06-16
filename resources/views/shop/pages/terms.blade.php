@extends('shop.layouts.app', ['title' => 'Terms & Conditions — Sutra Conscious'])

@section('content')
    <section class="container-narrow py-16 lg:py-24" data-reveal>
        <p class="eyebrow">Policy</p>
        <h1 class="mt-4 font-display text-display-md text-brand-black">Terms &amp; Conditions</h1>
        <p class="mt-5 text-brand-black/70 text-lg leading-relaxed max-w-2xl">
            By accessing and using this website, you agree to the following terms.
        </p>

        <div class="mt-14 space-y-14">

            <article>
                <h2 class="font-display text-2xl text-brand-black">Product Information</h2>
                <p class="mt-4 text-brand-black/80 leading-relaxed">
                    We strive to ensure that product descriptions, images, pricing, and availability are accurate. However, errors may occasionally occur, and we reserve the right to correct them without prior notice.
                </p>
            </article>

            <div class="rule"></div>

            <article>
                <h2 class="font-display text-2xl text-brand-black">Pricing</h2>
                <p class="mt-4 text-brand-black/80 leading-relaxed">
                    All prices displayed on the website are subject to change without notice.
                </p>
            </article>

            <div class="rule"></div>

            <article>
                <h2 class="font-display text-2xl text-brand-black">Order Acceptance</h2>
                <p class="mt-4 text-brand-black/80 leading-relaxed">
                    We reserve the right to refuse, cancel, or limit any order for reasons including suspected fraud, pricing errors, stock unavailability, or violations of our policies.
                </p>
            </article>

            <div class="rule"></div>

            <article>
                <h2 class="font-display text-2xl text-brand-black">Intellectual Property</h2>
                <p class="mt-4 text-brand-black/80 leading-relaxed">
                    All website content, including images, logos, text, graphics, and designs, is the property of Sutra Conscious and may not be copied, reproduced, or used without written permission.
                </p>
            </article>

            <div class="rule"></div>

            <article>
                <h2 class="font-display text-2xl text-brand-black">Limitation of Liability</h2>
                <p class="mt-4 text-brand-black/80 leading-relaxed">
                    Our liability shall be limited to the value of the purchased product. We shall not be liable for indirect, incidental, or consequential damages arising from the use of our products or services.
                </p>
            </article>

            <div class="rule"></div>

            <article>
                <h2 class="font-display text-2xl text-brand-black">Governing Law</h2>
                <p class="mt-4 text-brand-black/80 leading-relaxed">
                    These terms shall be governed by and interpreted in accordance with the laws of India.
                </p>
            </article>

        </div>

        <p class="mt-14 text-sm text-brand-black/50">
            For shipping, returns, and cancellations, see our
            <a href="{{ route('shipping-returns') }}" class="text-brand-blue link-underline">Shipping, Returns &amp; Cancellation</a> policy.
        </p>
    </section>
@endsection
