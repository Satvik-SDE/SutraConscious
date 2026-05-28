<?php

use App\Http\Controllers\Api\RazorpayPaymentController;
use Illuminate\Support\Facades\Route;

Route::post('/create-order', [RazorpayPaymentController::class, 'createOrder']);
Route::post('/verify-payment', [RazorpayPaymentController::class, 'verifyPayment']);
