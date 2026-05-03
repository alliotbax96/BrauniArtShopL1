<?php

use App\Http\Controllers\Dashboard\Quest\QuestController;
use App\Http\Controllers\Dashboard\Settings\SettingsController;
use App\Http\Controllers\Dashboard\Users\UsersController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\HomeController;
use App\Http\Controllers\Login\LoginController;
use App\Http\Controllers\Dashboard\Profile\ProfileController;
use App\Http\Controllers\Dashboard\Products\ProductsController;
use App\Http\Controllers\Dashboard\Products\ImageUploadController;
use App\Http\Controllers\Dashboard\Orders\OrdersController;
use App\Http\Controllers\Dashboard\Orders\PackingController;
use App\Http\Controllers\Dashboard\Finance\PaymentsController;
use App\Http\Controllers\Dashboard\Support\ChatController;
use App\Http\Controllers\Dashboard\Support\MessageController;
use App\Http\Controllers\Dashboard\Products\ProductQuantityController;
use App\Http\Controllers\Login\SsoController;

Route::domain('id.brauniart.shop')->group(function () {
    Route::group(['middleware' => ['guest']], function () {
        Route::get('/sso/auth', [SsoController::class, 'handleSsoAuth'])->name('sso.auth');
        Route::get('login', [LoginController::class, 'dashboardLogin'])->name('login');
      Route::prefix('auth')->name('auth.')->group(function () {
          Route::post('/code', [LoginController::class, 'sendCode'])->name('code');
          Route::post('/checkCode', [LoginController::class, 'checkCode'])->name('checkCode');
          Route::post('/login', [LoginController::class, 'login'])->name('login');
          Route::get('/yandex', [LoginController::class, 'redirectToYandex'])->name('yandex');
          Route::get('/yandex/callback', [LoginController::class, 'handleYandexCallback'])->name('yandex.callback');
      });

    });
    Route::group(['middleware' => ['auth', 'check.permissions']], function () {
        Route::get('/', [HomeController::class, 'index'])->name('home');
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [ProfileController::class, 'index'])->name('index');

            Route::post('/', [ProfileController::class, 'update'])->name('update');
            Route::get('/attach/yandex', [ProfileController::class, 'attachYandex'])->name('attach.yandex');
            Route::get('/attach/yandex/callback', [ProfileController::class, 'handleAttachYandexCallback'])->name('attach.yandex.callback');
            Route::post('/detach/{service}', [ProfileController::class, 'detachYandex'])->name('detach.yandex');

            Route::prefix('delivery')->name('delivery.')->group(function () {
                Route::get('/', [ProfileController::class, 'index'])->name('index');
                Route::post('/', [ProfileController::class, 'PvzUpdateOrCreate'])->name('update');
                Route::delete('/{id}', [ProfileController::class, 'PvzDelete'])->name('delete');
            });

            Route::prefix('pay')->name('pay.')->group(function () {
                Route::get('/', [ProfileController::class, 'index'])->name('index');
                Route::post('/legal', [ProfileController::class, 'LegalUpsertOrDelete'])->name('legal.update');
                Route::get('/legal/{id}', [ProfileController::class, 'GetLegal'])->name('GetLegal');
                Route::delete('/legal/{id}', [ProfileController::class, 'LegalHandleDelete'])->name('DeleteLegal');
                Route::get('/AddCard', [ProfileController::class, 'AddPaymentCard'])->name('AddCard');
                Route::delete('/RemoveCard/{id}', [ProfileController::class, 'DeletePaymentCard'])->name('RemoveCard');
            });

        });
        Route::get('/BecomeASeller', [SettingsController::class, 'BecomeASeller'])->name('BecomeASeller');
        Route::post('/BecomeASeller', [SettingsController::class, 'BecomeASeller_process'])->name('BecomeASeller_process');
        Route::prefix('seller')->name('seller.')->group(function () {
          Route::prefix('products')->name('products.')->group(function () {
              Route::get('/', [ProductsController::class, 'index'])->name('index');
              Route::get('/ajax', [ProductsController::class, 'ajax'])->name('ajax');
              Route::prefix('quantity')->name('quantity.')->group(function () {
                  Route::get('/', [ProductQuantityController::class, 'index'])->name('index');
                  Route::get('/ajax', [ProductQuantityController::class, 'ajax'])->name('ajax');
                  Route::post('/', [ProductQuantityController::class, 'updateQuantity'])->name('update');
              });
              Route::get('/create', [ProductsController::class, 'create'])->name('create');
              Route::post('/create', [ProductsController::class, 'store'])->name('store');
              Route::get('/delete/{id}', [ProductsController::class, 'destroy'])->name('delete');
              Route::get('/{id}', [ProductsController::class, 'show'])->name('show');
              Route::post('/{id}', [ProductsController::class, 'update'])->name('update');
          });
          Route::prefix('files')->name('files.')->group(function () {
              Route::post('/tempImageUpload', [ImageUploadController::class, 'upload'])->name('upload');
              Route::post('/tempImageDelete/{id}', [ImageUploadController::class, 'deleteImage'])->name('delete');
          });
          Route::prefix('settings')->name('settings.')->group(function () {
           Route::get('/', [SettingsController::class, 'index'])->name('index');
           Route::get('/logistic', [SettingsController::class, 'index'])->name('logistic');
           Route::get('/contract', [SettingsController::class, 'index'])->name('contract');
           Route::post('/', [SettingsController::class, 'update'])->name('update');
           Route::post('/pvz', [SettingsController::class, 'PvzUpdateOrCreate'])->name('pvz');
          });
          Route::prefix('orders')->name('orders.')->group(function () {
              Route::get('/', [OrdersController::class, 'index'])->name('index');
              Route::get('/ajax', [OrdersController::class, 'ajax'])->name('ajax');
              Route::get('/packing', [OrdersController::class, 'getOrderForPacking'])->name('packing');
              Route::post('/packing', [PackingController::class, 'savePacking'])->name('packing');
              Route::get('/SentForDelivery/{id}', [OrdersController::class, 'SentForDelivery'])->name('SentForDelivery');
              Route::get('/getDeliveryLabeles/{id}', [OrdersController::class, 'getDeliveryLabeles'])->name('getDeliveryLabeles');
              Route::get('/Sharing/{id}', [OrdersController::class, 'Sharing'])->name('Sharing');
          });
          Route::prefix('users')->name('users.')->group(function () {
              Route::get('/', [UsersController::class, 'index'])->name('index');
              Route::get('/ajax', [UsersController::class, 'ajax'])->name('ajax');
              Route::post('/GroupUpdate', [UsersController::class, 'updateGroup'])->name('GroupUpdate');
          });
          Route::prefix('finance')->name('users.')->group(function () {
              Route::get('/payments', [PaymentsController::class, 'index'])->name('index');
              Route::get('/payments/ajaxPayments', [PaymentsController::class, 'ajaxPayments'])->name('ajaxPayments');
          });
          Route::prefix('chat')->name('chat.')->group(function () {
              Route::get('/', [ChatController::class, 'index'])->name('index');
              Route::get('/ajax', [ChatController::class, 'ajax'])->name('ajax');
              Route::get('/store', [ChatController::class, 'store'])->name('store');
              Route::get('/user/{userId}/status', [ChatController::class, 'checkUserStatus']);
//              Route::get('/delete/{chatId}', [ChatController::class, 'delete'])->name('delete');
              Route::get('/{chat}', [ChatController::class, 'show'])->name('show');
              Route::post('/{chat}/message', [MessageController::class, 'store']);
              Route::post('/{chat}/read', [MessageController::class, 'markAsRead']);
              Route::post('/{chat}/upload', [MessageController::class, 'upload']);
          });
          Route::prefix('quests')->name('quests.')->group(function () {
              Route::get('/', [QuestController::class, 'index'])->name('index');
              Route::get('/ajax', [QuestController::class, 'ajax'])->name('ajax');
              Route::get('/create', [QuestController::class, 'create'])->name('create');
              Route::post('/create', [QuestController::class, 'store'])->name('store');
              Route::get('/{id}', [QuestController::class, 'edit'])->name('edit');
              Route::post('/{id}', [QuestController::class, 'update'])->name('update');
          });
        });
        Route::prefix('admin')->name('admin.')->group(function () {

        });
        Route::prefix('tests')->name('tests.')->group(function () {

        });
        Route::get('/logout', [LoginController::class, 'logout']);
    });


});
