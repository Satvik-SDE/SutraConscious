<section id="reviews" class="mt-16 lg:mt-20 border-t border-surface-line pt-12 lg:pt-16 scroll-mt-28" data-reveal>
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-8">
        <div>
            <p class="eyebrow">Reviews</p>
            <h2 class="mt-3 font-display text-3xl text-brand-black">Ratings &amp; reviews</h2>
        </div>
        @if($product->reviewsCount() > 0)
            <div class="flex items-center gap-3">
                @include('shop.partials.star-rating', ['rating' => $product->averageRating(), 'size' => 'md'])
                <span class="text-sm text-brand-black/70">
                    <span class="font-medium text-brand-black">{{ number_format($product->averageRating(), 1) }}</span>
                    · {{ $product->reviewsCount() }} {{ Str::plural('review', $product->reviewsCount()) }}
                </span>
            </div>
        @endif
    </div>

    @if(session('review_status'))
        <p class="mb-6 text-sm text-brand-blue">{{ session('review_status') }}</p>
    @endif

    @auth
        @if($canReview)
            <div class="mb-10 border border-surface-line bg-brand-skin/20 p-6 lg:p-8">
                <h3 class="font-display text-xl text-brand-black">
                    {{ $userReview ? 'Update your review' : 'Write a review' }}
                </h3>
                <p class="mt-2 text-sm text-brand-black/60">Verified purchase — only customers who bought this kurta can review.</p>

                <form action="{{ route('product.reviews.store', $product) }}" method="POST" class="mt-6 space-y-5" x-data="{ rating: {{ old('rating', $userReview?->rating ?? 0) }} }">
                    @csrf

                    <div>
                        <div class="field-label">Your rating</div>
                        <div class="mt-2 flex items-center gap-1">
                            @for($star = 1; $star <= 5; $star++)
                                <button type="button"
                                        @click="rating = {{ $star }}"
                                        class="p-1 transition-colors"
                                        aria-label="Rate {{ $star }} stars">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="w-7 h-7" :class="rating >= {{ $star }} ? 'text-brand-blue' : 'text-brand-black/20'" fill="currentColor">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 0 0 .95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 0 0-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 0 0-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 0 0-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 0 0 .951-.69l1.07-3.292z"/>
                                    </svg>
                                </button>
                            @endfor
                        </div>
                        <input type="hidden" name="rating" :value="rating" required>
                        @error('rating') <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="review-body" class="field-label">Your review <span class="text-brand-black/30 normal-case tracking-normal">(optional)</span></label>
                        <textarea name="body" id="review-body" rows="4" class="field-input resize-y min-h-[120px]" placeholder="Fit, fabric feel, everyday wear…">{{ old('body', $userReview?->body) }}</textarea>
                        @error('body') <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit" class="btn-primary" :disabled="rating < 1">Submit review</button>
                </form>
            </div>
        @elseif(! $userReview)
            <p class="mb-10 text-sm text-brand-black/60 border border-surface-line bg-surface-cream p-5">
                Reviews are open to verified buyers. Purchase this kurta and sign in to share your experience.
            </p>
        @endif
    @else
        <p class="mb-10 text-sm text-brand-black/60 border border-surface-line bg-surface-cream p-5">
            <a href="{{ route('login', ['redirect' => url()->current() . '#reviews']) }}" class="text-brand-blue link-underline">Sign in</a>
            to write a review after you have purchased this product.
        </p>
    @endauth

    @if($product->publishedReviews->isNotEmpty())
        <div class="space-y-6">
            @foreach($product->publishedReviews as $review)
                <article class="border-b border-surface-line pb-6">
                    <div class="flex flex-wrap items-center gap-3">
                        @include('shop.partials.star-rating', ['rating' => $review->rating, 'size' => 'sm'])
                        <span class="text-sm font-medium text-brand-black">{{ $review->reviewer_name }}</span>
                        <span class="text-xs text-brand-black/45">{{ $review->created_at->format('j M Y') }}</span>
                    </div>
                    @if($review->body)
                        <p class="mt-3 text-brand-black/75 leading-relaxed">{{ $review->body }}</p>
                    @endif
                </article>
            @endforeach
        </div>
    @else
        <p class="text-sm text-brand-black/55">No reviews yet. Be the first to share your thoughts after purchase.</p>
    @endif
</section>
