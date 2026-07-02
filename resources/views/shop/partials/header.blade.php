@php
    $cartService = app(\App\Services\CartService::class);
    $cart = $cartService->current()->load(['items.variant.product.images']);
    $cartCount = $cart->itemCount();
    $wishlistCount = $wishlistCount ?? 0;
    $navDepartments = $navDepartments ?? collect();
    $navTabSlugs = ['mens-wear', 'kids-boys'];
    $navTabDepartments = $navDepartments->whereIn('slug', $navTabSlugs)->values();
    $navOtherDepartments = $navDepartments->whereNotIn('slug', $navTabSlugs)->values();
@endphp

<header
    x-data
    :class="$store.nav.scrolled ? 'bg-surface-cream/95 backdrop-blur-md shadow-soft' : 'bg-transparent'"
    class="sticky top-0 z-40 transition-all duration-500 ease-silk border-b border-surface-line/60"
>
    <div class="container-bleed relative flex items-center justify-between gap-6"
         :class="$store.nav.scrolled ? 'h-16' : 'h-20'"
         style="transition: height 500ms cubic-bezier(0.22, 1, 0.36, 1);">

        <div class="flex items-center shrink-0 lg:flex-1 lg:min-w-0">
            <a href="{{ route('home') }}" class="flex items-center gap-3 group" aria-label="Sutra Conscious home">
                <img src="{{ asset('img/brand/logo-transparent.png') }}" alt="Sutra Conscious"
                     class="w-auto max-w-[min(220px,52vw)] object-contain object-left transition-all duration-500 ease-silk no-drag"
                     :class="$store.nav.scrolled ? 'h-8' : 'h-11'">
                <span class="sr-only">Sutra Conscious</span>
            </a>
        </div>

        <nav class="site-nav absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2">
            <a href="{{ route('shop') }}" class="site-nav-link"><span class="site-nav-text">Shop</span></a>

            @foreach($navTabDepartments as $department)
                <a href="{{ route('department.show', $department->slug) }}" class="site-nav-link">
                    <span class="site-nav-text">{{ $department->name }}</span>
                </a>
            @endforeach

            <a href="{{ route('about') }}" class="site-nav-link"><span class="site-nav-text">Our Story</span></a>
            <a href="{{ route('contact') }}" class="site-nav-link"><span class="site-nav-text">Contact</span></a>
        </nav>

        <div class="flex items-center justify-end gap-2 sm:gap-4 shrink-0 lg:flex-1">
            @auth
                <a href="{{ route('account.orders') }}"
                   class="hidden sm:inline-flex p-2 text-brand-black hover:text-brand-blue transition-colors"
                   aria-label="My orders">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-[22px] h-[22px]">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/>
                    </svg>
                </a>
            @else
                <a href="{{ route('login') }}"
                   class="hidden sm:inline-flex text-[0.7rem] uppercase tracking-[0.18em] text-brand-black hover:text-brand-blue transition-colors px-2">
                    Sign in
                </a>
            @endauth

            <a href="{{ route('wishlist.show') }}"
               class="relative inline-flex p-2 text-brand-black hover:text-brand-blue transition-colors"
               aria-label="Wishlist">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-[22px] h-[22px]">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z"/>
                </svg>
                @if($wishlistCount > 0)
                    <span class="absolute -top-0.5 -right-0.5 bg-brand-blue text-surface-cream text-[0.6rem] rounded-full h-[18px] min-w-[18px] px-1 flex items-center justify-center font-medium">{{ $wishlistCount }}</span>
                @endif
            </a>

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
         class="lg:hidden border-t border-surface-line bg-surface-cream max-h-[80dvh] overflow-y-auto scroll-thin">
        <nav class="container-wide py-6 flex flex-col">
            <a href="{{ route('shop') }}" class="py-3 text-brand-black text-sm uppercase tracking-[0.2em] border-b border-surface-line">Shop All</a>
            @foreach($navTabDepartments as $department)
                <a href="{{ route('department.show', $department->slug) }}" class="py-3 text-brand-black text-sm uppercase tracking-[0.2em] font-medium border-b border-surface-line">{{ $department->name }}</a>
            @endforeach
            @foreach($navOtherDepartments as $department)
                <div class="border-b border-surface-line">
                    <a href="{{ route('department.show', $department->slug) }}" class="block py-3 text-brand-black text-sm uppercase tracking-[0.2em] font-medium">{{ $department->name }}</a>
                    @if($department->activeCategories->isNotEmpty())
                        <div class="pb-3 pl-4 space-y-2">
                            @foreach($department->activeCategories as $cat)
                                <a href="{{ route('category.show', $cat->slug) }}" class="block text-xs uppercase tracking-[0.18em] text-brand-black/65 hover:text-brand-blue">{{ $cat->name }}</a>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
            <a href="{{ route('about') }}" class="py-3 text-brand-black text-sm uppercase tracking-[0.2em] border-b border-surface-line">Our Story</a>
            <a href="{{ route('contact') }}" class="py-3 text-brand-black text-sm uppercase tracking-[0.2em] border-b border-surface-line">Contact</a>
            <a href="{{ route('wishlist.show') }}" class="py-3 text-brand-black text-sm uppercase tracking-[0.2em] border-b border-surface-line">
                Wishlist
                @if($wishlistCount > 0)
                    ({{ $wishlistCount }})
                @endif
            </a>
            @auth
                <a href="{{ route('account.orders') }}" class="py-3 text-brand-blue text-sm uppercase tracking-[0.2em]">My orders</a>
            @else
                <a href="{{ route('login') }}" class="py-3 text-brand-black text-sm uppercase tracking-[0.2em] border-b border-surface-line">Sign in</a>
                <a href="{{ route('orders.track') }}" class="py-3 text-brand-black text-sm uppercase tracking-[0.2em]">Track order</a>
            @endauth
        </nav>
    </div>
</header>

@include('shop.partials.cart-drawer', ['cart' => $cart])
