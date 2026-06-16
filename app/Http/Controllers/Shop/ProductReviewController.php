<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\ProductReviewService;
use Illuminate\Http\Request;

class ProductReviewController extends Controller
{
    public function __construct(protected ProductReviewService $reviews) {}

    public function store(Request $request, Product $product)
    {
        abort_unless($product->is_active, 404);

        $data = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'body' => ['nullable', 'string', 'max:2000'],
        ]);

        $this->reviews->upsert($request->user(), $product, $data);

        return redirect()
            ->to(route('product.show', $product->slug) . '#reviews')
            ->with('review_status', 'Thanks — your review has been posted.');
    }
}
