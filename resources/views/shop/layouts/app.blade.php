<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#FBFAF6">

    <title>{{ $title ?? 'Sutra Conscious — 100% Cotton Kurtas, Crafted in Bharat' }}</title>
    <meta name="description" content="{{ $metaDescription ?? '100% premium cotton kurtas, crafted in Bharat. Breathable, decades-of-wear, no synthetics.' }}">

    <link rel="canonical" href="{{ url()->current() }}">

    <meta property="og:type" content="{{ $ogType ?? 'website' }}">
    <meta property="og:site_name" content="Sutra Conscious">
    <meta property="og:title" content="{{ $title ?? 'Sutra Conscious' }}">
    <meta property="og:description" content="{{ $metaDescription ?? '100% premium cotton kurtas, crafted in Bharat.' }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ asset('img/brand/logo.png') }}">
    <meta name="twitter:card" content="summary_large_image">

    <link rel="icon" type="image/png" href="{{ asset('img/brand/logo.png') }}">

    {{-- Preconnect for Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    @include('shop.partials.jsonld-organization')

    @stack('head')

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col bg-surface-cream">
    @include('shop.partials.announcement')
    @include('shop.partials.header')

    <main class="flex-1">
        {{ $slot ?? '' }}
        @yield('content')
    </main>

    @include('shop.partials.footer')

    @stack('scripts')
</body>
</html>
