<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductController extends Controller
{
    public function show(Product $product)
    {
        abort_unless($product->is_active, 404);

        $product->load(['images', 'variants', 'category']);

        $related = Product::query()
            ->where('is_active', true)
            ->where('id', '!=', $product->id)
            ->when($product->category_id, fn ($q) => $q->where('category_id', $product->category_id))
            ->with(['images', 'variants', 'category'])
            ->limit(4)
            ->get();

        return view('shop.product', [
            'product' => $product,
            'related' => $related,
        ]);
    }
}
