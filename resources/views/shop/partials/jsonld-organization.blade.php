@php
    $orgLd = [
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => 'Sutra Conscious',
        'url' => url('/'),
        'logo' => asset('img/brand/logo.png'),
        'email' => 'sutra.conscious@gmail.com',
        'telephone' => '+91 93215 39748',
        'description' => '100% premium cotton kurtas crafted in Bharat. From soil, to skin, to soil.',
        'sameAs' => [
            'https://www.instagram.com/sutraconscious/',
        ],
    ];
@endphp
<script type="application/ld+json">{!! json_encode($orgLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
