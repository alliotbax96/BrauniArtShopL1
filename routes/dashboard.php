<?php

use App\Http\Controllers\Dashboard\Admin\ProductGroupController;
use App\Http\Controllers\Dashboard\Admin\SellersController;
use App\Http\Controllers\Dashboard\Books\BookController;
use App\Http\Controllers\Dashboard\Digital\DigitalProductsController;
use App\Http\Controllers\Dashboard\Quest\QuestController;
use App\Http\Controllers\Dashboard\Settings\SettingsController;
use App\Http\Controllers\Dashboard\Users\UsersController;
use App\Http\Controllers\Login\SignupController;
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
use App\Http\Controllers\Dashboard\Admin\EnvController;
use App\Http\Controllers\Dashboard\Orders\BookOrdersController;
use App\Http\Controllers\Dashboard\Admin\BudgetController;

Route::domain('id.brauniart.shop')->group(function () {
    Route::group(['middleware' => ['guest']], function () {
        Route::get('/sso/auth', [SsoController::class, 'handleSsoAuth'])->name('sso.auth');
        Route::get('login', [LoginController::class, 'dashboardLogin'])->name('login');
        Route::get('signup', [SignupController::class, 'dashboardSignup'])->name('signup');
        Route::post('signup', [SignupController::class, 'register'])->name('register');
      Route::prefix('auth')->name('auth.')->group(function () {
          Route::post('/code', [LoginController::class, 'sendCode'])->name('code');
          Route::post('/checkCode', [LoginController::class, 'checkCode'])->name('checkCode');
          Route::post('/login', [LoginController::class, 'login'])->name('login');
          Route::get('/yandex', [LoginController::class, 'redirectToYandex'])->name('yandex');
          Route::get('/yandex/callback', [LoginController::class, 'handleYandexCallback'])->name('yandex.callback');
          Route::get('/vk', [LoginController::class, 'redirectToVK'])->name('vk');
          Route::get('/vk/callback', [LoginController::class, 'handleVKCallback'])->name('vk.callback');
      });

    });
    Route::group(['middleware' => ['auth', 'check.permissions']], function () {
        Route::get('/', [HomeController::class, 'index'])->name('home');
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [ProfileController::class, 'index'])->name('index');
            Route::post('/', [ProfileController::class, 'update'])->name('update');
            Route::get('/attach/yandex', [ProfileController::class, 'attachYandex'])->name('attach.yandex');
            Route::get('/attach/yandex/callback', [ProfileController::class, 'handleAttachYandexCallback'])->name('attach.yandex.callback');
            Route::get('/attach/vk', [ProfileController::class, 'attachVK'])->name('attach.vk');
            Route::get('/attach/vk/callback', [ProfileController::class, 'handleAttachVKCallback'])->name('attach.vk.callback');
            Route::post('/detach/{service}', [ProfileController::class, 'detachService'])->name('detach.service');

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

            Route::prefix('legalDetails')->name('legalDetails.')->group(function () {
               Route::get('/', [ProfileController::class, 'index'])->name('index');
            });

            Route::prefix('selfEmployed')->name('selfEmployed.')->group(function () {
                Route::get('/', [ProfileController::class, 'index'])->name('index');
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
          Route::prefix('digital')->name('digital.')->group(function () {
              Route::get('/', [DigitalProductsController::class, 'index'])->name('index');
          });
          Route::prefix('quests')->name('quests.')->group(function () {
                Route::get('/', [QuestController::class, 'index'])->name('index');
                Route::get('/ajax', [QuestController::class, 'ajax'])->name('ajax');
                Route::get('/create', [QuestController::class, 'create'])->name('create');
                Route::post('/create', [QuestController::class, 'store'])->name('store');
                Route::get('/{id}', [QuestController::class, 'edit'])->name('edit');
                Route::post('/{id}', [QuestController::class, 'update'])->name('update');
            });
          Route::prefix('books')->name('books.')->group(function () {
           Route::get('/', [BookController::class, 'index'])->name('index');
           Route::get('/ajax', [BookController::class, 'ajax'])->name('ajax');
           Route::get('/{id}', [BookController::class, 'show'])->name('show');
           Route::post('/', [BookController::class, 'store'])->name('store');
           Route::put('/{id}', [BookController::class, 'update'])->name('update');
           Route::get('/{bookId}/chapters', [BookController::class, 'show'])->name('chapters');
              Route::get('/{bookId}/chapters/create', [BookController::class, 'chapterCreate'])->name('chapterCreate');
           Route::get('/{bookId}/chapters/{chapterId}', [BookController::class, 'chapterShow'])->name('chapter');
           Route::post('/{bookId}/chapters', [BookController::class, 'storeChapter']);
           Route::put('/{bookId}/chapters/{chapterId}', [BookController::class, 'updateChapter']);
           Route::patch('/{id}/moderation', [BookController::class, 'updateModerationStatus']);
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
          Route::prefix('bookOrders')->name('bookOrders.')->group(function () {
              Route::get('/', [BookOrdersController::class, 'index'])->name('index');
              Route::post('/ajax', [BookOrdersController::class, 'ajax'])->name('ajax');
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
        });
        Route::prefix('tests')->name('tests.')->group(function () {
            Route::get('/Send', function () {
                $sellerId = 2411;
                $seller = \App\Models\Seller::findOrFail($sellerId);

                // Получаем связанных пользователей (сразу с email, чтобы не делать N+1 в цикле)
                $users = $seller->users()->whereNotNull('email')->get();
                $count = $users->count();

                if ($count === 0) {
                    return response()->json([
                        'status'  => 'warning',
                        'message' => 'У продавца нет связанных пользователей с email',
                        'seller'  => $seller->name,
                        'count'   => 0,
                    ], 200);
                }

                // Отправляем уведомления
                $seller->notifyRelatedUsers(
                    subject: 'Статус продавца изменён',
                    greeting: 'Здравствуйте, ' . $seller->name . '!',
                    line: 'Статус вашего продавца был одобрен. Теперь вы можете размещать товары.',
                    actionUrl: 'https://id.brauniart.shop',
                    actionText: 'Перейти в кабинет продавца',
                );

                return response()->json([
                    'status'      => 'success',
                    'message'     => 'Уведомления поставлены в очередь',
                    'seller'      => $seller->name,
                    'users_count' => $count,
                    'users'       => $users->pluck('name', 'email')->toArray(), // только имена и email для отладки
                ], 200);
            })->name('Send');
        });
        Route::get('/logout', [LoginController::class, 'logout']);
    });
    Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::prefix('env')->name('env.')->group(function () {
            Route::get('/', [EnvController::class, 'index'])->name('index');
            Route::put('/', [EnvController::class, 'update'])->name('update');
            Route::post('/backup', [EnvController::class, 'createBackup'])->name('backup');
            Route::post('/restore/{filename}', [EnvController::class, 'restoreBackup'])->name('restore');
            Route::delete('/backup/{filename}', [EnvController::class, 'deleteBackup'])->name('delete-backup');
        });
        Route::prefix('productGroups')->name('productGroups.')->group(function () {
            Route::get('/', [ProductGroupController::class, 'index'])->name('index');
            Route::get('/ajax', [ProductGroupController::class, 'ajax'])->name('ajax');
            Route::get('/create', [ProductGroupController::class, 'create'])->name('create');
            Route::post('/', [ProductGroupController::class, 'store'])->name('store');
            Route::get('/{id}', [ProductGroupController::class, 'edit'])->name('edit');
            Route::put('/{id}', [ProductGroupController::class, 'update'])->name('update');
            Route::delete('/{id}', [ProductGroupController::class, 'destroy'])->name('destroy');
        });
        Route::prefix('sellers')->name('sellers.')->group(function () {
            Route::get('/', [SellersController::class, 'index'])->name('index');
            Route::get('/ajax', [SellersController::class, 'ajax'])->name('ajax');
            Route::post('/contracts/{contract_id}/status', [SellersController::class, 'updateContractStatus'])
                ->name('contracts.status.update');
        });
        Route::prefix('budget')->name('budget.')->group(function () {
                Route::get('/', [BudgetController::class, 'index'])->name('index');
                Route::get('/transactions', [BudgetController::class, 'getTransactions'])->name('transactions');
                Route::post('/income', [BudgetController::class, 'addIncome'])->name('income');
                Route::post('/expense', [BudgetController::class, 'addExpense'])->name('expense');
                Route::post('/manager-balance', [BudgetController::class, 'updateManagerBalance'])->name('manager-balance');
                Route::delete('/transaction/{id}', [BudgetController::class, 'deleteTransaction'])->name('delete');
        });
    });
});
