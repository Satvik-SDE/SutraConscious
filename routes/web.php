<?php

use App\Http\Controllers\Shop\CartController;
use App\Http\Controllers\Shop\CatalogController;
use App\Http\Controllers\Shop\CheckoutController;
use App\Http\Controllers\Shop\HomeController;
use App\Http\Controllers\Shop\PageController;
use App\Http\Controllers\Shop\ProductController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\Webhooks\RazorpayWebhookController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/shop', [CatalogController::class, 'shop'])->name('shop');
Route::get('/category/{category:slug}', [CatalogController::class, 'category'])->name('category.show');
Route::get('/product/{product:slug}', [ProductController::class, 'show'])->name('product.show');

Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'show'])->name('show');
    Route::post('/add', [CartController::class, 'add'])->name('add');
    Route::patch('/items/{item}', [CartController::class, 'update'])->name('update');
    Route::delete('/items/{item}', [CartController::class, 'remove'])->name('remove');
});

Route::prefix('checkout')->name('checkout.')->group(function () {
    Route::get('/', [CheckoutController::class, 'show'])->name('show');
    Route::post('/place-order', [CheckoutController::class, 'place'])->name('place');
    Route::get('/pay/{order:number}', [CheckoutController::class, 'pay'])->name('pay');
    Route::post('/verify/{order:number}', [CheckoutController::class, 'verify'])->name('verify');
});

Route::get('/order/{order:number}/confirmation', [CheckoutController::class, 'confirmation'])->name('order.confirmation');

Route::post('/webhooks/razorpay', [RazorpayWebhookController::class, 'handle'])->name('webhooks.razorpay');

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::get('/shipping-returns', [PageController::class, 'shippingReturns'])->name('shipping-returns');
Route::get('/privacy', [PageController::class, 'privacy'])->name('privacy');
Route::get('/terms', [PageController::class, 'terms'])->name('terms');
