<?php

use App\Http\Controllers\Dashboard\Profile\SelfEmployedController;
use Illuminate\Support\Facades\Route;
//use App\Http\Controllers\Products\ProductController;
use App\Http\Controllers\Quests\QuestController;
use App\Http\Controllers\Booking\BookingController;
use App\Http\Controllers\Orders\PayHook;
use App\Http\Controllers\Api\BookReaderController;
use App\Http\Controllers\Dashboard\Books\BookController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\PvzController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\BuyerChatController;
use App\Http\Controllers\Api\PaymentController;

Route::middleware(['web'])->group(function () {
    // Инициализация чата покупателя
    Route::post('/buyer/chat/init', [BuyerChatController::class, 'initChat'])
        ->name('api.buyer.chat.init');

    // Отправка сообщения
    Route::post('/buyer/chat/{chat}/message', [BuyerChatController::class, 'sendMessage'])
        ->name('api.buyer.chat.message');

    // Получение сообщений
    Route::get('/buyer/chat/{chat}/messages', [BuyerChatController::class, 'getMessages'])
        ->name('api.buyer.chat.messages');

    // Закрытие чата
    Route::post('/buyer/chat/{chat}/close', [BuyerChatController::class, 'closeChat'])
        ->name('api.buyer.chat.close');
});

// Квесты
Route::prefix('quests')->group(function () {
   Route::get('/{questId}/services', [QuestController::class, 'api_services']);
});

Route::post('/bookings', [BookingController::class, 'store']);
Route::post('/PaymentHook', [PayHook::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/bookmarks', [BookReaderController::class, 'saveBookmark']);
    Route::post('/reading-progress', [BookReaderController::class, 'updateProgress']);
    Route::post('/chapters/complete', [BookReaderController::class, 'markChapterCompleted']);

    Route::prefix('books')->name('books.')->group(function () {
        Route::get('/{bookId}/info', [BookReaderController::class, 'getBookInfo'])->name('info');
        Route::get('/{bookId}/chapters/{chapterId}', [BookReaderController::class, 'getChapterContent'])->name('content');
    });

    Route::prefix('selfEmployed')->name('selfEmployed.')->group(function () {
     Route::post('/save', [SelfEmployedController::class, 'saveSelfEmployedRecord'])->name('save');
     Route::put('/save', [SelfEmployedController::class, 'update'])->name('update');
    });
});

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Публичные маршруты
Route::post('/payment/webhook', [PaymentController::class, 'webhook']);

// Защищенные маршруты (только ваш api.key)
Route::middleware(['api.key'])->group(function () {
    // POST для создания платежа
    Route::post('/payment/create', [PaymentController::class, 'create']);

    // GET для тестирования (только в разработке)
    if (app()->environment('local', 'development')) {
        Route::get('/payment/create', [PaymentController::class, 'test']);
    }


});
Route::get('/payment/status/{id}', [PaymentController::class, 'status']);
// В routes/api.php
//Route::get('/payment/status/{id}', [PaymentController::class, 'getStatus']);

// Публичные маршруты
Route::prefix('auth')->group(function () {
    Route::post('/send-code', [AuthController::class, 'sendCode']);
    Route::post('/verify-code', [AuthController::class, 'verifyCode']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/social/{provider}/redirect', [AuthController::class, 'redirectToSocial']);
    Route::get('/social/{provider}/callback', [AuthController::class, 'handleSocialCallback']);
});

// Каталог товаров (публичный)
Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index']);
    Route::get('/{id}', [ProductController::class, 'show']);
    Route::get('/{id}/reviews', [ReviewController::class, 'getProductReviews']);
});

// Категории (публичные)
Route::prefix('categories')->group(function () {
    Route::get('/', [CategoryController::class, 'index']);
    Route::get('/{id}', [CategoryController::class, 'show']);
    Route::get('/{id}/children', [CategoryController::class, 'children']);
});

// ПВЗ (публичные)
Route::prefix('pvz')->group(function () {
    Route::get('/search', [PvzController::class, 'search']);
    Route::get('/{id}', [PvzController::class, 'show']);
});

// Защищённые маршруты (требуется авторизация)
Route::middleware('auth:sanctum')->group(function () {
    // Профиль пользователя
    Route::prefix('user')->group(function () {
        Route::get('/', [UserController::class, 'profile']);
        Route::put('/', [UserController::class, 'updateProfile']);
        Route::get('/orders', [OrderController::class, 'userOrders']);
        Route::get('/orders/{id}', [OrderController::class, 'show']);
        Route::get('/pvzs', [PvzController::class, 'userPvzs']);
    });

    // Корзина
    Route::prefix('cart')->group(function () {
        Route::get('/', [CartController::class, 'index']);
        Route::post('/add', [CartController::class, 'add']);
        Route::put('/{itemId}', [CartController::class, 'update']);
        Route::delete('/{itemId}', [CartController::class, 'remove']);
        Route::delete('/', [CartController::class, 'clear']);
    });

    // Оформление заказа
    Route::prefix('checkout')->group(function () {
        Route::post('/calculate', [OrderController::class, 'calculateDelivery']);
        Route::post('/process', [OrderController::class, 'processOrder']);
        Route::post('/select-pvz', [PvzController::class, 'selectPvz']);
    });

    // Отзывы
    Route::prefix('reviews')->group(function () {
        Route::post('/{type}/{id}', [ReviewController::class, 'store']);
        Route::delete('/{id}', [ReviewController::class, 'destroy']);
    });

    // Выход
    Route::post('/auth/logout', [AuthController::class, 'logout']);
});
