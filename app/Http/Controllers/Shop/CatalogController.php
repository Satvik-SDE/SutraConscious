<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;

class CatalogController extends Controller
{
    public function shop()
    {
        $products = Product::query()
            ->where('is_active', true)
            ->with(['images', 'category', 'variants'])
            ->orderBy('sort_order')
            ->get();

        $categories = Category::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('shop.shop', [
            'products' => $products,
            'categories' => $categories,
            'activeCategory' => null,
        ]);
    }

    public function category(Category $category)
    {
        abort_unless($category->is_active, 404);

        $products = $category->products()
            ->where('is_active', true)
            ->with(['images', 'variants', 'category'])
            ->orderBy('sort_order')
            ->get();

        $categories = Category::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('shop.category', [
            'category' => $category,
            'products' => $products,
            'categories' => $categories,
            'activeCategory' => $category->slug,
        ]);
    }
}
