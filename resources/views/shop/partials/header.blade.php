@php
    $cartService = app(\App\Services\CartService::class);
    $cart = $cartService->current()->load(['items.variant.product.images']);
    $cartCount = $cart->itemCount();
    $navCategories = \App\Models\Category::query()->where('is_active', true)->orderBy('sort_order')->get(['name', 'slug']);
@endphp

<header
    x-data
    :class="$store.nav.scrolled ? 'bg-surface-cream/95 backdrop-blur-md shadow-soft' : 'bg-transparent'"
    class="sticky top-0 z-40 transition-all duration-500 ease-silk border-b border-surface-line/60"
>
    <div class="container-bleed flex items-center justify-between gap-6"
         :class="$store.nav.scrolled ? 'h-16' : 'h-20'"
         style="transition: height 500ms cubic-bezier(0.22, 1, 0.36, 1);">

        <a href="{{ route('home') }}" class="flex items-center gap-3 group shrink-0" aria-label="Sutra Conscious home">
            <img src="{{ asset('img/brand/logo.png') }}" alt="Sutra Conscious"
                 class="w-auto transition-all duration-500 ease-silk no-drag"
                 :class="$store.nav.scrolled ? 'h-9' : 'h-12'">
            <span class="sr-only">Sutra Conscious</span>
        </a>

        <nav class="hidden lg:flex items-center gap-9 text-[0.78rem] tracking-[0.18em] uppercase">
            <a href="{{ route('shop') }}" class="link-underline text-brand-black hover:text-brand-blue transition-colors">Shop All</a>
            @foreach($navCategories as $nav)
                <a href="{{ route('category.show', $nav->slug) }}" class="link-underline text-brand-black hover:text-brand-blue transition-colors">{{ $nav->name }}</a>
            @endforeach
            <a href="{{ route('about') }}" class="link-underline text-brand-black hover:text-brand-blue transition-colors">Our Story</a>
            <a href="{{ route('contact') }}" class="link-underline text-brand-black hover:text-brand-blue transition-colors">Contact</a>
        </nav>

        <div class="flex items-center gap-2 sm:gap-4">
            <button type="button"
                    @click="$store.drawer.show()"
                    class="relative inline-flex items-center gap-2 p-2 text-brand-black hover:text-brand-blue transition-colors"
                    aria-label="Open cart">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-[22px] h-[22px]">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.658-.463 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007Z"/>
                </svg>
                @if($cartCount > 0)
                    <span class="absolute -top-0.5 -right-0.5 bg-brand-blue text-surface-cream text-[0.6rem] rounded-full h-[18px] min-w-[18px] px-1 flex items-center justify-center font-medium animate-fade-in">{{ $cartCount }}</span>
                @endif
            </button>

            <button type="button"
                    @click="$store.nav.mobileOpen = !$store.nav.mobileOpen"
                    class="lg:hidden p-2 text-brand-black"
                    aria-label="Menu">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path x-show="!$store.nav.mobileOpen" stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
                    <path x-show="$store.nav.mobileOpen" stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Mobile menu --}}
    <div x-cloak
         x-show="$store.nav.mobileOpen"
         x-transition:enter="transition ease-silk duration-400"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-silk duration-300"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="lg:hidden border-t border-surface-line bg-surface-cream">
        <nav class="container-wide py-6 flex flex-col">
            <a href="{{ route('shop') }}" class="py-3 text-brand-black text-sm uppercase tracking-[0.2em] border-b border-surface-line">Shop All</a>
            @foreach($navCategories as $nav)
                <a href="{{ route('category.show', $nav->slug) }}" class="py-3 text-brand-black text-sm uppercase tracking-[0.2em] border-b border-surface-line">{{ $nav->name }}</a>
            @endforeach
            <a href="{{ route('about') }}" class="py-3 text-brand-black text-sm uppercase tracking-[0.2em] border-b border-surface-line">Our Story</a>
            <a href="{{ route('contact') }}" class="py-3 text-brand-black text-sm uppercase tracking-[0.2em]">Contact</a>
        </nav>
    </div>
</header>

@include('shop.partials.cart-drawer', ['cart' => $cart])
