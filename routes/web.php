<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Products\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\Login\LoginController;

Route::get('/', [HomeController::class, 'index']);
Route::get('laravel', function () {
    return view('welcome');
});
Route::get('/home', [HomeController::class, 'index']);
Route::get('/home/ajax', [HomeController::class, 'ajax_home']);

Route::prefix('products')->name('products.')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('index');
    Route::get('/{id}', [ProductController::class, 'show'])->name('show');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/logout', [LoginController::class, 'logout']);
});

Route::group(['middleware' => ['guest']], function () {
    Route::get('/auth', [LoginController::class, 'authForm']);
    Route::post('/auth/code', [LoginController::class, 'sendCode']);
    Route::post('/auth/checkCode', [LoginController::class, 'checkCode']);
    Route::get('/auth/login', [LoginController::class, 'login']);
});

// Группы маршрутов для удобства
Route::prefix('cart')->name('cart.')->group(function () {
    // Просмотр корзины
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::get('/mini', [CartController::class, 'getMiniCart'])->name('mini');
    // Добавление товара в корзину
    Route::post('/add', [CartController::class, 'add'])->name('add');
    // Обновление количества товара
    Route::put('/update/{itemId}', [CartController::class, 'update'])->name('update');
    // Удаление товара из корзины
    Route::delete('/remove/{itemId}', [CartController::class, 'remove'])->name('remove');
    // Очистка корзины
    Route::post('/clear', [CartController::class, 'clear'])->name('clear');
});
