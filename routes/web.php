<?php

use App\Http\Controllers\Login\SignupController;
use App\Http\Controllers\StaticPagesController;
use App\Http\Integrations\RedSMS\RedSMSConnector;
use App\Http\Integrations\RedSMS\Requests\SendCallCodeRequest;
use App\Http\Integrations\TBank\TbankConnector;
use App\Services\TbankService;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Products\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\Login\LoginController;
use App\Http\Controllers\UserPvzController;
use App\Http\Controllers\CheckoutController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Orders\PayHook;
use App\Http\Controllers\Products\ReviewController;
use App\Http\Controllers\Orders\OrdersController;


require __DIR__ . '/dashboard.php';

Route::domain('brauniart.shop')->group(function () {
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
        Route::prefix('user')->name('user.')->group(function () {
            Route::post('/pvz/update', [UserPvzController::class, 'updateOrCreate'])->name('pvz.update');
        });
        Route::prefix('checkout')->name('checkout.')->group(function () {
            Route::post('/', [CheckoutController::class, 'showCheckout'])->name('index');
            Route::post('/process', [CheckoutController::class, 'processOrder'])->name('process');
        });

        Route::post('/products/{product}/reviews', [ReviewController::class, 'store'])->name('review.store');
        Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('review.destroy');

        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/', [OrdersController::class, 'index'])->name('index');
            Route::get('/sharing/{id}', [OrdersController::class, 'sharing'])->name('sharing');
            Route::get('/{id}', [OrdersController::class, 'show'])->name('show');
        });
    });

    Route::group(['middleware' => ['guest']], function () {
        Route::prefix('auth')->name('auth.')->group(function () {
            Route::get('/', [LoginController::class, 'authForm'])->name('index');
            Route::post('/code', [LoginController::class, 'sendCode'])->name('code');
            Route::post('/checkCode', [LoginController::class, 'checkCode'])->name('checkCode');
            Route::post('/login', [LoginController::class, 'login'])->name('login');
            Route::get('/login/yandex', [LoginController::class, 'redirectToYandex'])->name('login.yandex');
            Route::get('/login/yandex/callback', [LoginController::class, 'handleYandexCallback'])->name('login.yandex.callback');
        });
        Route::prefix('signup')->name('signup.')->group(function () {
            Route::get('/', [SignupController::class, 'signupForm'])->name('index');
            Route::post('/', [SignupController::class, 'register'])->name('register');
        });
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

    Route::post('/checkout/PaymentHook', [PayHook::class, 'index'])->name('checkout.paymentHook');
    Route::get('/products/{product}/reviews', [ReviewController::class, 'show']);

// Статические страницы
    Route::get('/aboutUs', [StaticPagesController::class, 'aboutUs'])->name('aboutUs');
    Route::get('/contacts', [StaticPagesController::class, 'contacts'])->name('contacts');
    Route::get('/PayAndDelivery', [StaticPagesController::class, 'PayAndDelivery'])->name('PayAndDelivery');
    Route::get('/refunds', [StaticPagesController::class, 'refunds'])->name('refunds');
});
