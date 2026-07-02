<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Department;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $featured = Product::query()
            ->where('is_active', true)
            ->where('is_featured', true)
            ->with(['images', 'category', 'variants'])
            ->orderBy('sort_order')
            ->limit(8)
            ->get();

        if ($featured->isEmpty()) {
            $featured = Product::query()
                ->where('is_active', true)
                ->with(['images', 'category', 'variants'])
                ->orderBy('sort_order')
                ->limit(8)
                ->get();
        }

        $categories = Category::query()
            ->where('is_active', true)
            ->with(['department', 'products' => fn ($q) => $q->where('is_active', true)->with(['images', 'variants'])->limit(4)])
            ->orderBy('sort_order')
            ->get();

        $departments = Department::query()
            ->visibleOnStorefront()
            ->with(['activeCategories' => fn ($q) => $q->with(['products' => fn ($pq) => $pq->where('is_active', true)->with(['images', 'variants'])->limit(1)])])
            ->orderBy('sort_order')
            ->get();

        return view('shop.home', [
            'featured' => $featured,
            'categories' => $categories,
            'departments' => $departments,
        ]);
    }
}
