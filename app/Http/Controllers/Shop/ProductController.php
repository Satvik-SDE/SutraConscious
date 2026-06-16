<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\ProductReviewService;

class ProductController extends Controller
{
    public function __construct(protected ProductReviewService $reviews) {}

    public function show(Product $product)
    {
        abort_unless($product->is_active, 404);

        $product->loadAvg('publishedReviews as published_reviews_avg_rating', 'rating');
        $product->loadCount('publishedReviews as published_reviews_count');
        $product->load([
            'images',
            'variants',
            'category.department',
            'publishedReviews' => fn ($query) => $query->latest(),
        ]);

        $related = Product::query()
            ->where('is_active', true)
            ->where('id', '!=', $product->id)
            ->when($product->category_id, fn ($q) => $q->where('category_id', $product->category_id))
            ->with(['images', 'variants', 'category'])
            ->withAvg('publishedReviews as published_reviews_avg_rating', 'rating')
            ->withCount('publishedReviews as published_reviews_count')
            ->limit(4)
            ->get();

        $user = auth()->user();
        $canReview = $user ? $this->reviews->userHasPurchased($user, $product) : false;
        $userReview = $user ? $this->reviews->userReview($user, $product) : null;

        return view('shop.product', [
            'product' => $product,
            'related' => $related,
            'canReview' => $canReview,
            'userReview' => $userReview,
        ]);
    }
}
