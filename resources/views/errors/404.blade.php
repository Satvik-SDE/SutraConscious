@extends('shop.layouts.app', ['title' => 'Page not found — Sutra Conscious'])

@section('content')
    <section class="container-narrow py-24 lg:py-32 text-center relative overflow-hidden">
        <div aria-hidden="true" class="absolute inset-0 flex items-center justify-center pointer-events-none">
            <div class="font-script text-[18rem] lg:text-[26rem] text-brand-blue/10 select-none leading-none">404</div>
        </div>

        <div class="relative z-10" data-reveal>
            <p class="eyebrow">Lost thread</p>
            <h1 class="mt-5 font-display text-display-md text-brand-black">This page took the long way home.</h1>
            <p class="mt-5 text-brand-black/70 max-w-md mx-auto leading-relaxed">The weave didn't quite reach here. Let's get you back to the kurtas.</p>

            <div class="mt-10 flex flex-col sm:flex-row justify-center gap-3">
                <a href="{{ route('home') }}" class="btn-primary">Back home</a>
                <a href="{{ route('shop') }}" class="btn-outline">Browse Kurtas</a>
            </div>
        </div>
    </section>
@endsection
