<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Products\ProductController;
use App\Http\Controllers\Quests\QuestController;
use App\Http\Controllers\Booking\BookingController;
use App\Http\Controllers\Orders\PayHook;

// Продукты
Route::prefix('products')->group(function () {
  Route::get('/', [ProductController::class, 'apiIndex']);
  Route::get('/{id}', [ProductController::class, 'apiShow']);
  Route::get('/groups', [ProductController::class, 'apiCategories']);
});

// Квесты
Route::prefix('quests')->group(function () {
   Route::get('/{questId}/services', [QuestController::class, 'api_services']);
});

Route::post('/bookings', [BookingController::class, 'store']);

Route::post('/PaymentHook', [PayHook::class, 'index']);

