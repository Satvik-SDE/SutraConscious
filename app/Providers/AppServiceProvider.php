<?php

namespace App\Providers;

use App\Models\Department;
use App\Models\Order;
use App\Policies\OrderPolicy;
use App\Services\WishlistService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Order::class, OrderPolicy::class);

        if (! $this->app->runningInConsole()) {
            $this->app->booted(function () {
                try {
                    $wishlist = app(WishlistService::class);
                    View::share('wishlistProductIds', $wishlist->productIds());
                    View::share('wishlistCount', $wishlist->count());
                } catch (\Throwable) {
                    View::share('wishlistProductIds', []);
                    View::share('wishlistCount', 0);
                }

                try {
                    View::share('navDepartments', Department::query()
                        ->where('is_active', true)
                        ->with(['activeCategories' => fn ($query) => $query->orderBy('sort_order')])
                        ->orderBy('sort_order')
                        ->get());
                } catch (\Throwable) {
                    View::share('navDepartments', collect());
                }
            });
        }
    }
}
