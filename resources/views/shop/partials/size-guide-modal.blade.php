@if($imageUrl)
    <div
        x-show="sizeGuideOpen"
        x-cloak
        class="fixed inset-0 z-[60] flex items-end sm:items-center justify-center p-0 sm:p-6"
        role="dialog"
        aria-modal="true"
        aria-label="Size guide">
        <div
            x-show="sizeGuideOpen"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="sizeGuideOpen = false"
            class="absolute inset-0 bg-brand-black/50 backdrop-blur-sm"
            aria-hidden="true">
        </div>

        <div
            x-show="sizeGuideOpen"
            x-transition:enter="transition ease-silk duration-400"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="transition ease-in duration-250"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            @keydown.escape.window="sizeGuideOpen = false"
            class="relative w-full sm:max-w-2xl max-h-[90dvh] bg-surface-cream border border-surface-line shadow-lift flex flex-col">
            <header class="flex items-center justify-between px-5 py-4 border-b border-surface-line flex-shrink-0">
                <div>
                    <p class="eyebrow-dim">Size guide</p>
                    <h2 class="font-display text-lg text-brand-black mt-0.5">{{ $title }}</h2>
                </div>
                <button
                    type="button"
                    @click="sizeGuideOpen = false"
                    class="p-2 -mr-2 text-brand-black hover:text-brand-blue transition-colors"
                    aria-label="Close size guide">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </header>

            <div class="overflow-y-auto scroll-thin p-5">
                <img
                    src="{{ $imageUrl }}"
                    alt="Size guide for {{ $title }}"
                    class="w-full h-auto"
                    loading="lazy">
            </div>
        </div>
    </div>
@endif
