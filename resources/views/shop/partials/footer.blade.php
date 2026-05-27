<footer class="bg-brand-black text-surface-cream mt-section">
    {{-- Newsletter --}}
    <section class="border-b border-surface-cream/10">
        <div class="container-wide py-16 grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
            <div>
                <p class="text-brand-skin/80 text-xs uppercase tracking-[0.3em]">The Sutra Letter</p>
                <h2 class="font-display text-3xl lg:text-4xl mt-3 max-w-md">First dibs on new drops and the stories behind every weave.</h2>
            </div>
            <form class="flex flex-col sm:flex-row gap-3" onsubmit="event.preventDefault(); this.querySelector('button').textContent = 'Thanks · We will be in touch';">
                <input type="email" required placeholder="your@email.com" class="flex-1 bg-transparent border border-surface-cream/30 px-4 py-3 text-surface-cream placeholder:text-surface-cream/40 focus:border-brand-blue focus:ring-0 transition-colors">
                <button type="submit" class="btn-primary bg-surface-cream text-brand-black hover:bg-brand-blue hover:text-surface-cream whitespace-nowrap">Subscribe</button>
            </form>
        </div>
    </section>

    {{-- Main grid --}}
    <div class="container-wide py-16 grid grid-cols-2 sm:grid-cols-2 md:grid-cols-12 gap-10">
        <div class="col-span-2 md:col-span-5">
            <div class="font-script text-brand-blue text-5xl leading-none">Sutra</div>
            <div class="font-display text-4xl tracking-tight">Conscious<sup class="text-[0.6rem] align-super ml-0.5">™</sup></div>
            <p class="mt-6 max-w-sm text-sm text-surface-cream/70 leading-relaxed">
                Conscious cotton, made daily-wear ready. Thoughtfully crafted kurtas in 100% premium cotton — from soil, to skin, to soil.
            </p>
            <div class="mt-8 flex items-center gap-4">
                <a href="https://www.instagram.com/sutraconscious/" target="_blank" rel="noopener" aria-label="Instagram"
                   class="w-10 h-10 inline-flex items-center justify-center border border-surface-cream/20 rounded-full hover:bg-brand-blue hover:border-brand-blue transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-4 h-4 fill-current"><path d="M12 2.2c3.2 0 3.584.012 4.85.07 1.366.062 2.633.336 3.608 1.311.975.975 1.249 2.242 1.311 3.608.058 1.266.07 1.65.07 4.85s-.012 3.584-.07 4.85c-.062 1.366-.336 2.633-1.311 3.608-.975.975-2.242 1.249-3.608 1.311-1.266.058-1.65.07-4.85.07s-3.584-.012-4.85-.07c-1.366-.062-2.633-.336-3.608-1.311C2.567 19.522 2.293 18.255 2.231 16.889 2.173 15.623 2.161 15.239 2.161 12.039s.012-3.584.07-4.85c.062-1.366.336-2.633 1.311-3.608C4.517 2.606 5.784 2.332 7.15 2.27 8.416 2.212 8.8 2.2 12 2.2zm0 1.8c-3.146 0-3.519.011-4.764.067-1.041.048-1.605.221-1.98.367-.498.193-.853.424-1.227.798-.374.374-.605.729-.798 1.227-.146.375-.319.939-.367 1.98C2.811 9.481 2.8 9.854 2.8 13s.011 3.519.067 4.764c.048 1.041.221 1.605.367 1.98.193.498.424.853.798 1.227.374.374.729.605 1.227.798.375.146.939.319 1.98.367 1.245.056 1.618.067 4.764.067s3.519-.011 4.764-.067c1.041-.048 1.605-.221 1.98-.367.498-.193.853-.424 1.227-.798.374-.374.605-.729.798-1.227.146-.375.319-.939.367-1.98.056-1.245.067-1.618.067-4.764s-.011-3.519-.067-4.764c-.048-1.041-.221-1.605-.367-1.98a3.302 3.302 0 00-.798-1.227 3.302 3.302 0 00-1.227-.798c-.375-.146-.939-.319-1.98-.367C15.519 4.011 15.146 4 12 4zm0 3.062a4.938 4.938 0 110 9.876 4.938 4.938 0 010-9.876zm0 1.8a3.138 3.138 0 100 6.276 3.138 3.138 0 000-6.276zm5.144-1.62a1.152 1.152 0 11-2.304 0 1.152 1.152 0 012.304 0z"/></svg>
                </a>
                <a href="mailto:sutra.conscious@gmail.com" aria-label="Email"
                   class="w-10 h-10 inline-flex items-center justify-center border border-surface-cream/20 rounded-full hover:bg-brand-blue hover:border-brand-blue transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/>
                    </svg>
                </a>
            </div>
        </div>

        <div class="md:col-span-3">
            <div class="text-[0.7rem] uppercase tracking-[0.3em] text-surface-cream/50 mb-5">Shop</div>
            <ul class="space-y-3 text-sm">
                <li><a href="{{ route('shop') }}" class="text-surface-cream/85 hover:text-brand-blue transition-colors">All Kurtas</a></li>
                @foreach(\App\Models\Category::where('is_active', true)->orderBy('sort_order')->get() as $cat)
                    <li><a href="{{ route('category.show', $cat->slug) }}" class="text-surface-cream/85 hover:text-brand-blue transition-colors">{{ $cat->name }}</a></li>
                @endforeach
            </ul>
        </div>

        <div class="md:col-span-2">
            <div class="text-[0.7rem] uppercase tracking-[0.3em] text-surface-cream/50 mb-5">Brand</div>
            <ul class="space-y-3 text-sm">
                <li><a href="{{ route('about') }}" class="text-surface-cream/85 hover:text-brand-blue transition-colors">Our Story</a></li>
                <li><a href="{{ route('contact') }}" class="text-surface-cream/85 hover:text-brand-blue transition-colors">Contact</a></li>
            </ul>
        </div>

        <div class="md:col-span-2">
            <div class="text-[0.7rem] uppercase tracking-[0.3em] text-surface-cream/50 mb-5">Care</div>
            <ul class="space-y-3 text-sm">
                <li><a href="{{ route('shipping-returns') }}" class="text-surface-cream/85 hover:text-brand-blue transition-colors">Shipping &amp; Returns</a></li>
                <li><a href="{{ route('privacy') }}" class="text-surface-cream/85 hover:text-brand-blue transition-colors">Privacy</a></li>
                <li><a href="{{ route('terms') }}" class="text-surface-cream/85 hover:text-brand-blue transition-colors">Terms</a></li>
            </ul>
        </div>
    </div>

    {{-- Bottom bar --}}
    <div class="border-t border-surface-cream/10">
        <div class="container-wide py-6 flex flex-col-reverse sm:flex-row items-center justify-between gap-4 text-[0.7rem] uppercase tracking-[0.18em] text-surface-cream/50">
            <div>&copy; {{ date('Y') }} Sutra Conscious · Founded by Shuchi &amp; Adit</div>
            <div class="flex items-center gap-4">
                <span>Crafted in Bharat</span>
                <span class="w-1 h-1 rounded-full bg-brand-blue"></span>
                <span>Sutraconscious.com</span>
            </div>
        </div>
    </div>
</footer>
