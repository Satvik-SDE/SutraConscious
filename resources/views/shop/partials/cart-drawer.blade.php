<div x-data x-cloak>
    {{-- Overlay --}}
    <div
        x-show="$store.drawer.open"
        x-transition:enter="transition ease-out duration-400"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="$store.drawer.close()"
        class="fixed inset-0 bg-brand-black/40 backdrop-blur-sm z-50"
        aria-hidden="true">
    </div>

    {{-- Drawer panel --}}
    <aside
        x-show="$store.drawer.open"
        x-transition:enter="transition ease-silk duration-500"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-silk duration-400"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        @keydown.escape.window="$store.drawer.close()"
        class="fixed top-0 right-0 h-[100dvh] w-full sm:w-[440px] bg-surface-cream z-50 flex flex-col shadow-lift"
        role="dialog"
        aria-modal="true"
        aria-label="Cart">

        <header class="flex items-center justify-between px-6 py-5 border-b border-surface-line">
            <div>
                <p class="eyebrow-dim">Your Bag</p>
                <h2 class="font-display text-xl text-brand-black mt-1">{{ $cart->itemCount() }} item{{ $cart->itemCount() === 1 ? '' : 's' }}</h2>
            </div>
            <button type="button"
                    @click="$store.drawer.close()"
                    class="p-2 -mr-2 text-brand-black hover:text-brand-blue transition-colors"
                    aria-label="Close cart">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </header>

        @if($cart->isEmpty())
            <div class="flex-1 flex flex-col items-center justify-center px-8 text-center">
                <div class="w-16 h-16 rounded-full bg-brand-skin/50 flex items-center justify-center mb-5">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.2" stroke="currentColor" class="w-7 h-7 text-brand-blue">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.658-.463 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007Z"/>
                    </svg>
                </div>
                <p class="font-display text-2xl text-brand-black">Your bag is empty.</p>
                <p class="text-sm text-brand-black/60 mt-2 leading-relaxed">Start with a piece of conscious cotton.</p>
                <a href="{{ route('shop') }}" @click="$store.drawer.close()" class="btn-primary mt-6">Browse Kurtas</a>
            </div>
        @else
            <div class="flex-1 overflow-y-auto scroll-thin">
                <ul class="divide-y divide-surface-line">
                    @foreach($cart->items as $item)
                        @php
                            $product = $item->variant->product;
                            $img = $product->images->first();
                        @endphp
                        <li class="px-6 py-5 flex gap-4">
                            <a href="{{ route('product.show', $product->slug) }}" @click="$store.drawer.close()" class="w-20 h-24 flex-shrink-0 bg-brand-skin/40 overflow-hidden">
                                @if($img)
                                    <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($img->path) }}" alt="{{ $product->name }}" loading="lazy" class="w-full h-full object-cover">
                                @endif
                            </a>
                            <div class="flex-1 min-w-0">
                                <a href="{{ route('product.show', $product->slug) }}" @click="$store.drawer.close()" class="block text-sm font-medium text-brand-black hover:text-brand-blue truncate">{{ $product->name }}</a>
                                <div class="mt-1 text-[0.7rem] text-brand-black/60 uppercase tracking-[0.2em]">{{ $item->variant->label() }}</div>
                                <div class="mt-3 flex items-center justify-between">
                                    <div class="inline-flex items-center border border-surface-line">
                                        <form action="{{ route('cart.update', $item) }}" method="POST" class="inline-flex">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="quantity" value="{{ max(0, $item->quantity - 1) }}">
                                            <button type="submit" class="w-7 h-7 text-brand-black hover:bg-brand-skin/50">−</button>
                                        </form>
                                        <span class="w-8 text-center text-sm">{{ $item->quantity }}</span>
                                        <form action="{{ route('cart.update', $item) }}" method="POST" class="inline-flex">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="quantity" value="{{ min(10, $item->quantity + 1) }}">
                                            <button type="submit" class="w-7 h-7 text-brand-black hover:bg-brand-skin/50">+</button>
                                        </form>
                                    </div>
                                    <div class="text-sm font-medium text-brand-black">₹{{ number_format($item->lineTotal()) }}</div>
                                </div>
                            </div>
                            <form action="{{ route('cart.remove', $item) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-brand-black/40 hover:text-red-600 transition-colors" aria-label="Remove">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            </div>

            <footer class="border-t border-surface-line px-6 py-5 space-y-4 bg-brand-skin/10">
                <div class="flex items-center justify-between">
                    <span class="eyebrow-dim">Subtotal</span>
                    <span class="font-display text-2xl text-brand-blue">₹{{ number_format($cart->subtotal()) }}</span>
                </div>
                <p class="text-[0.7rem] text-brand-black/50 uppercase tracking-[0.18em]">Shipping calculated at checkout</p>
                <div class="grid grid-cols-2 gap-3">
                    <a href="{{ route('cart.show') }}" @click="$store.drawer.close()" class="btn-outline w-full">View Bag</a>
                    <a href="{{ route('checkout.show') }}" @click="$store.drawer.close()" class="btn-primary w-full">Checkout</a>
                </div>
            </footer>
        @endif
    </aside>
</div>
