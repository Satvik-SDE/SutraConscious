@php
    $manifestPath = public_path('build/manifest.json');
@endphp

@if (file_exists($manifestPath))
    @php
        $manifest = json_decode(file_get_contents($manifestPath), true);
        $cssFile = $manifest['resources/css/app.css']['file'] ?? null;
        $jsFile = $manifest['resources/js/app.js']['file'] ?? null;
    @endphp
    @if ($cssFile)
        <link rel="preload" href="{{ asset('build/'.$cssFile) }}" as="style">
        <link rel="stylesheet" href="{{ asset('build/'.$cssFile) }}">
    @endif
    @if ($jsFile)
        <script type="module" src="{{ asset('build/'.$jsFile) }}" defer></script>
    @endif
@else
    @vite(['resources/css/app.css', 'resources/js/app.js'])
@endif
