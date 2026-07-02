<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#FBFAF6">
    <meta name="site-build" content="2026-05-28-v3">

    @php
        $seoTitle = $title ?? 'Sutra Conscious — Conscious Cotton Clothing, Crafted in Bharat';
        $seoDescription = $metaDescription ?? '100% cotton clothing for men, women, and kids — crafted in Bharat. Breathable, decades-of-wear, no synthetics.';
        $seoImage = $ogImage ?? asset('img/brand/logo.png');
    @endphp
    <title>{{ $seoTitle }}</title>
    <meta name="description" content="{{ $seoDescription }}">
    <meta name="robots" content="{{ $metaRobots ?? 'index, follow, max-image-preview:large' }}">
    <meta name="author" content="Sutra Conscious">

    <link rel="canonical" href="{{ $canonical ?? url()->current() }}">

    <meta property="og:type" content="{{ $ogType ?? 'website' }}">
    <meta property="og:site_name" content="Sutra Conscious">
    <meta property="og:title" content="{{ $seoTitle }}">
    <meta property="og:description" content="{{ $seoDescription }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ $seoImage }}">
    <meta property="og:image:alt" content="{{ $ogImageAlt ?? 'Sutra Conscious — conscious cotton clothing' }}">
    <meta property="og:locale" content="en_IN">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $seoTitle }}">
    <meta name="twitter:description" content="{{ $seoDescription }}">
    <meta name="twitter:image" content="{{ $seoImage }}">

    <link rel="icon" type="image/png" href="{{ asset('img/brand/favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('img/brand/favicon.png') }}">

    {{-- Preconnect for Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    @include('shop.partials.jsonld-organization')

    @stack('head')

    @include('shop.partials.assets')
</head>
<body class="min-h-screen flex flex-col bg-surface-cream">
    @include('shop.partials.header')

    <main class="flex-1">
        {{ $slot ?? '' }}
        @yield('content')
    </main>

    @include('shop.partials.footer')

    @stack('scripts')
</body>
</html>
