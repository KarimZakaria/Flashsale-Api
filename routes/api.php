<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\HoldController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\PaymentWebhookController;

Route:: get('/products/{id}', [ProductController::class, 'show']);
Route::post('/holds',         [HoldController::class,     'store']);
Route::post('/orders',        [OrderController::class,    'store']);
Route::post('/payments/webhook', [PaymentWebhookController::class, 'handle']);

