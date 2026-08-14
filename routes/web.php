<?php

use App\Http\Controllers\Login\SignupController;
use App\Http\Controllers\StaticPagesController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Products\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\Login\LoginController;
use App\Http\Controllers\UserPvzController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Products\ReviewController;
use App\Http\Controllers\Orders\OrdersController;
use App\Http\Controllers\Quests\QuestController;
use App\Http\Controllers\Booking\BookingController;
use App\Http\Controllers\Login\SsoController;
use App\Http\Controllers\Api\BookReaderController;
use App\Http\Controllers\Books\BookController;
use App\Http\Controllers\Orders\BookOrdersController;
use App\Http\Controllers\Books\PlayerController;
use App\Http\Controllers\Api\PaymentController;

require __DIR__ . '/dashboard.php';

Route::domain('brauniart.shop')->group(function () {

    // ============================================
    // ПЛАТЕЖИ
    // ============================================
    Route::get('/payment/redirect', [PaymentController::class, 'redirect'])->name('payment.redirect');

    // ============================================
    // ГЛАВНАЯ СТРАНИЦА
    // ============================================
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/home', [HomeController::class, 'index'])->name('home.alt');
    Route::get('/home/ajax', [HomeController::class, 'ajax_home'])->name('home.ajax');

    // Переключение режима магазина
    Route::get('/shopMode/{id}', function ($id) {
        return redirect('/')->cookie('ShopMode', $id);
    })->name('shopMode');

    // ============================================
    // ТОВАРЫ
    // ============================================
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('index');
        Route::get('/{id}', [ProductController::class, 'show'])->name('show');
    });

    // ============================================
    // КВЕСТЫ
    // ============================================
    Route::prefix('quests')->name('quests.')->group(function () {
        Route::get('/', [QuestController::class, 'index'])->name('index');
        Route::get('/{id}', [QuestController::class, 'show'])->name('show');
    });

    // ============================================
    // КНИГИ
    // ============================================
    Route::prefix('books')->name('books.')->group(function () {
        Route::get('/', [BookController::class, 'index'])->name('index');
        Route::get('/{id}', [BookController::class, 'show'])->name('show');
    });

    // ============================================
    // КОРЗИНА (доступна всем)
    // ============================================
    Route::prefix('cart')->name('cart.')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('index');
        Route::get('/mini', [CartController::class, 'getMiniCart'])->name('mini');
        Route::post('/add', [CartController::class, 'add'])->name('add');
        Route::put('/update/{itemId}', [CartController::class, 'update'])->name('update');
        Route::delete('/remove/{itemId}', [CartController::class, 'remove'])->name('remove');
        Route::post('/clear', [CartController::class, 'clear'])->name('clear');
    });

    // ============================================
    // ОТЗЫВЫ (публичные)
    // ============================================
    Route::get('/products/{product}/reviews', [ReviewController::class, 'show'])->name('products.reviews');

    // ============================================
    // СТАТИЧЕСКИЕ СТРАНИЦЫ
    // ============================================
    Route::get('/aboutUs', [StaticPagesController::class, 'aboutUs'])->name('aboutUs');
    Route::get('/contacts', [StaticPagesController::class, 'contacts'])->name('contacts');
    Route::get('/PayAndDelivery', [StaticPagesController::class, 'PayAndDelivery'])->name('PayAndDelivery');
    Route::get('/refunds', [StaticPagesController::class, 'refunds'])->name('refunds');

    // ============================================
    // ГРУППА МАРШРУТОВ ДЛЯ НЕАВТОРИЗОВАННЫХ ПОЛЬЗОВАТЕЛЕЙ
    // ============================================
    Route::group(['middleware' => ['guest']], function () {

        // Аутентификация
        Route::prefix('auth')->name('auth.')->group(function () {
            Route::get('/', [LoginController::class, 'authForm'])->name('index');
            Route::post('/code', [LoginController::class, 'sendCode'])->name('code');
            Route::post('/checkCode', [LoginController::class, 'checkCode'])->name('checkCode');
            Route::post('/login', [LoginController::class, 'login'])->name('login');

            // OAuth: Yandex
            Route::get('/login/yandex', [LoginController::class, 'redirectToYandex'])->name('login.yandex');
            Route::get('/login/yandex/callback', [LoginController::class, 'handleYandexCallback'])->name('login.yandex.callback');

            // OAuth: VK
            Route::get('/login/vk', [LoginController::class, 'redirectToVk'])->name('login.vk');
            Route::get('/login/vk/callback', [LoginController::class, 'handleVkCallback'])->name('login.vk.callback');
        });

        // Регистрация
        Route::prefix('signup')->name('signup.')->group(function () {
            Route::get('/', [SignupController::class, 'signupForm'])->name('index');
            Route::post('/', [SignupController::class, 'register'])->name('register');
        });
    });

    // ============================================
    // ГРУППА МАРШРУТОВ ДЛЯ АВТОРИЗОВАННЫХ ПОЛЬЗОВАТЕЛЕЙ
    // ============================================
    Route::middleware(['auth'])->group(function () {

        // SSO инициализация
        Route::get('/sso/initiate', [SsoController::class, 'initiateSso'])->name('sso.initiate');

        // Выход (POST для безопасности)
        Route::post('/logout', [LoginController::class, 'logout'])->name('logout'); // ИСПРАВЛЕНО: GET -> POST

        // -------- ПОЛЬЗОВАТЕЛЬ --------
        Route::prefix('user')->name('user.')->group(function () {
            Route::post('/pvz/update', [UserPvzController::class, 'updateOrCreate'])->name('pvz.update');
        });

        // -------- ОФОРМЛЕНИЕ ЗАКАЗА --------
        Route::prefix('checkout')->name('checkout.')->group(function () {
            Route::post('/', [CheckoutController::class, 'showCheckout'])->name('index');
            Route::post('/process', [CheckoutController::class, 'processOrder'])->name('process');
        });

        // -------- ОТЗЫВЫ (авторизованные) --------
        Route::prefix('reviews')->name('reviews.')->group(function () { // ИСПРАВЛЕНО: вынесено в отдельную группу
            // Создание отзыва
            Route::post('/{reviewableType}/{reviewableId}', [ReviewController::class, 'store'])->name('store');
            // Получение отзывов
            Route::get('/{reviewableType}/{reviewableId}', [ReviewController::class, 'show'])->name('show');
            // Удаление отзыва
            Route::delete('/{review}', [ReviewController::class, 'destroy'])->name('destroy');
        });

        // -------- ЗАКАЗЫ --------
        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/', [OrdersController::class, 'index'])->name('index');
            Route::get('/sharing/{id}', [OrdersController::class, 'sharing'])->name('sharing');
            Route::get('/{id}', [OrdersController::class, 'show'])->name('show');
        });

        // -------- ЗАКАЗЫ КНИГ --------
        Route::prefix('bookOrders')->name('bookOrders.')->group(function () {
            Route::get('/', [BookOrdersController::class, 'index'])->name('index');
        });

        // -------- БРОНИРОВАНИЯ --------
        Route::prefix('bookings')->name('bookings.')->group(function () {
            Route::get('/', [BookingController::class, 'index'])->name('index');
        });

        // -------- ЧИТАЛКА КНИГ --------
        Route::get('/reader/{bookId}', [BookReaderController::class, 'index'])->name('reader');

        // -------- АУДИОПЛЕЕР --------
        Route::prefix('books/{book}')->name('player.')->group(function () {
            Route::get('/player', [PlayerController::class, 'show'])->name('show');
            Route::post('/progress', [PlayerController::class, 'saveProgress'])->name('progress');
            Route::post('/bookmarks', [PlayerController::class, 'toggleBookmark'])->name('bookmark');
            Route::get('/bookmarks', [PlayerController::class, 'getBookmarks'])->name('bookmarks');
        });
    });
});
