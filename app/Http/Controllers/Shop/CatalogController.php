<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Department;
use App\Models\Product;

class CatalogController extends Controller
{
    public function shop()
    {
        $departments = $this->navDepartments();

        $products = Product::query()
            ->where('is_active', true)
            ->with(['images', 'category.department', 'variants'])
            ->withAvg('publishedReviews as published_reviews_avg_rating', 'rating')
            ->withCount('publishedReviews as published_reviews_count')
            ->orderBy('sort_order')
            ->get();

        return view('shop.shop', [
            'products' => $products,
            'departments' => $departments,
            'activeDepartment' => null,
            'activeCategory' => null,
        ]);
    }

    public function department(Department $department)
    {
        abort_unless($department->isVisibleOnStorefront(), 404);

        $departments = $this->navDepartments();

        $products = Product::query()
            ->where('is_active', true)
            ->whereHas('category', fn ($query) => $query->where('department_id', $department->id))
            ->with(['images', 'category.department', 'variants'])
            ->withAvg('publishedReviews as published_reviews_avg_rating', 'rating')
            ->withCount('publishedReviews as published_reviews_count')
            ->orderBy('sort_order')
            ->get();

        return view('shop.department', [
            'department' => $department,
            'products' => $products,
            'departments' => $departments,
            'activeDepartment' => $department->slug,
            'activeCategory' => null,
        ]);
    }

    public function category(Category $category)
    {
        abort_unless($category->is_active, 404);

        $category->load('department');
        $departments = $this->navDepartments();

        $products = $category->products()
            ->where('is_active', true)
            ->with(['images', 'variants', 'category.department'])
            ->withAvg('publishedReviews as published_reviews_avg_rating', 'rating')
            ->withCount('publishedReviews as published_reviews_count')
            ->orderBy('sort_order')
            ->get();

        return view('shop.category', [
            'category' => $category,
            'products' => $products,
            'departments' => $departments,
            'activeDepartment' => $category->department?->slug,
            'activeCategory' => $category->slug,
        ]);
    }

    protected function navDepartments()
    {
        return Department::forStorefront();
    }
}
