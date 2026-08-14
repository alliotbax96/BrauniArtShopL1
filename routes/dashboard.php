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

    // ============================================
    // ГРУППА МАРШРУТОВ ДЛЯ НЕАВТОРИЗОВАННЫХ ПОЛЬЗОВАТЕЛЕЙ
    // ============================================
    Route::group(['middleware' => ['guest']], function () {
        // SSO авторизация
        Route::get('/sso/auth', [SsoController::class, 'handleSsoAuth'])->name('sso.auth');

        // Страницы входа и регистрации
        Route::get('login', [LoginController::class, 'dashboardLogin'])->name('login');
        Route::get('signup', [SignupController::class, 'dashboardSignup'])->name('signup');
        Route::post('signup', [SignupController::class, 'register'])->name('register');

        // Группа маршрутов авторизации
        Route::prefix('auth')->name('auth.')->group(function () {
            Route::post('/code', [LoginController::class, 'sendCode'])->name('Scode');
            Route::post('/checkCode', [LoginController::class, 'checkCode'])->name('ScheckCode');
            Route::post('/login', [LoginController::class, 'login'])->name('Slogin');

            // OAuth маршруты для Yandex и VK
            Route::get('/yandex', [LoginController::class, 'redirectToYandex'])->name('yandex');
            Route::get('/yandex/callback', [LoginController::class, 'handleYandexCallback'])->name('yandex.callback');
            Route::get('/vk', [LoginController::class, 'redirectToVK'])->name('vk');
            Route::get('/vk/callback', [LoginController::class, 'handleVKCallback'])->name('vk.callback');
        });
    });

    // ============================================
    // ГРУППА МАРШРУТОВ ДЛЯ АВТОРИЗОВАННЫХ ПОЛЬЗОВАТЕЛЕЙ
    // ============================================
    Route::group(['middleware' => ['auth', 'check.permissions']], function () {

        // Главная страница
        Route::get('/', [HomeController::class, 'index'])->name('home');

        // ========================================
        // ПРОФИЛЬ ПОЛЬЗОВАТЕЛЯ
        // ========================================
        Route::prefix('profile')->name('profile.')->group(function () {
            // Основные настройки профиля
            Route::get('/', [ProfileController::class, 'index'])->name('index');
            Route::post('/', [ProfileController::class, 'update'])->name('update');

            // Привязка/отвязка социальных сетей
            Route::prefix('social')->name('social.')->group(function () {
                // Yandex
                Route::get('/attach/yandex', [ProfileController::class, 'attachYandex'])->name('attach.yandex');
                Route::get('/attach/yandex/callback', [ProfileController::class, 'handleAttachYandexCallback'])->name('attach.yandex.callback');
                // VK
                Route::get('/attach/vk', [ProfileController::class, 'attachVK'])->name('attach.vk');
                Route::get('/attach/vk/callback', [ProfileController::class, 'handleAttachVKCallback'])->name('attach.vk.callback');
                // Отвязка
                Route::post('/detach/{service}', [ProfileController::class, 'detachService'])->name('detach.service');
            });

            // Доставка (ПВЗ)
            Route::prefix('delivery')->name('delivery.')->group(function () {
                Route::get('/', [ProfileController::class, 'index'])->name('index');
                Route::post('/', [ProfileController::class, 'PvzUpdateOrCreate'])->name('update');
                Route::delete('/{id}', [ProfileController::class, 'PvzDelete'])->name('delete');
            });

            // Платежные настройки
            Route::prefix('pay')->name('pay.')->group(function () {
                Route::get('/', [ProfileController::class, 'index'])->name('index');

                // Юридические лица
                Route::post('/legal', [ProfileController::class, 'LegalUpsertOrDelete'])->name('legal.update');
                Route::get('/legal/{id}', [ProfileController::class, 'GetLegal'])->name('GetLegal');
                Route::delete('/legal/{id}', [ProfileController::class, 'LegalHandleDelete'])->name('DeleteLegal');

                // Платежные карты
                Route::get('/AddCard', [ProfileController::class, 'AddPaymentCard'])->name('AddCard');
                Route::delete('/RemoveCard/{id}', [ProfileController::class, 'DeletePaymentCard'])->name('RemoveCard');
            });

            // Юридические детали
            Route::prefix('legalDetails')->name('legalDetails.')->group(function () {
                Route::get('/', [ProfileController::class, 'index'])->name('index');
            });

            // Самозанятые
            Route::prefix('selfEmployed')->name('selfEmployed.')->group(function () {
                Route::get('/', [ProfileController::class, 'index'])->name('index');
            });
        });

        // ========================================
        // СТАТУС ПРОДАВЦА
        // ========================================
        Route::get('/BecomeASeller', [SettingsController::class, 'BecomeASeller'])->name('BecomeASeller');
        Route::post('/BecomeASeller', [SettingsController::class, 'BecomeASeller_process'])->name('BecomeASeller_process');

        // ========================================
        // ПАНЕЛЬ ПРОДАВЦА
        // ========================================
        Route::prefix('seller')->name('seller.')->group(function () {

            // -------- ТОВАРЫ --------
            Route::prefix('products')->name('products.')->group(function () {
                Route::get('/', [ProductsController::class, 'index'])->name('index');
                Route::get('/ajax', [ProductsController::class, 'ajax'])->name('ajax');

                // Управление количеством товаров
                Route::prefix('quantity')->name('quantity.')->group(function () {
                    Route::get('/', [ProductQuantityController::class, 'index'])->name('index');
                    Route::get('/ajax', [ProductQuantityController::class, 'ajax'])->name('ajax');
                    Route::post('/', [ProductQuantityController::class, 'updateQuantity'])->name('update');
                });

                // CRUD операции с товарами
                Route::get('/create', [ProductsController::class, 'create'])->name('create');
                Route::post('/create', [ProductsController::class, 'store'])->name('store');
                Route::get('/{id}', [ProductsController::class, 'show'])->name('show');
                Route::post('/{id}', [ProductsController::class, 'update'])->name('update');
                Route::delete('/{id}', [ProductsController::class, 'destroy'])->name('delete'); // ИСПРАВЛЕНО: GET -> DELETE
            });

            // -------- ЦИФРОВЫЕ ТОВАРЫ --------
            Route::prefix('digital')->name('digital.')->group(function () {
                Route::get('/', [DigitalProductsController::class, 'index'])->name('index');
            });

            // -------- КВЕСТЫ --------
            Route::prefix('quests')->name('quests.')->group(function () {
                Route::get('/', [QuestController::class, 'index'])->name('index');
                Route::get('/ajax', [QuestController::class, 'ajax'])->name('ajax');
                Route::get('/create', [QuestController::class, 'create'])->name('create');
                Route::post('/create', [QuestController::class, 'store'])->name('store');
                Route::get('/{id}', [QuestController::class, 'edit'])->name('edit');
                Route::put('/{id}', [QuestController::class, 'update'])->name('update'); // ИСПРАВЛЕНО: POST -> PUT
            });

            // -------- КНИГИ --------
            Route::prefix('books')->name('books.')->group(function () {
                Route::get('/', [BookController::class, 'index'])->name('index');
                Route::get('/ajax', [BookController::class, 'ajax'])->name('ajax');

                // CRUD для книг
                Route::post('/', [BookController::class, 'store'])->name('store');
                Route::get('/{id}', [BookController::class, 'show'])->name('show');
                Route::put('/{id}', [BookController::class, 'update'])->name('update');
                Route::delete('/{id}', [BookController::class, 'destroy'])->name('destroy'); // ИСПРАВЛЕНО: /delete/{id} -> /{id}

                // Главы книг
                Route::prefix('{bookId}/chapters')->group(function () {
                    Route::get('/', [BookController::class, 'show'])->name('chapters');
                    Route::get('/create', [BookController::class, 'chapterCreate'])->name('chapterCreate');
                    Route::get('/{chapterId}', [BookController::class, 'chapterShow'])->name('chapter');
                    Route::post('/', [BookController::class, 'storeChapter']);
                    Route::put('/{chapterId}', [BookController::class, 'updateChapter']);
                });

                // Модерация (только для администраторов)
                Route::middleware('admin')->patch('/{id}/moderation', [BookController::class, 'updateModerationStatus']);
            });

            // -------- ЗАГРУЗКА ФАЙЛОВ --------
            Route::prefix('files')->name('files.')->group(function () {
                Route::post('/tempImageUpload', [ImageUploadController::class, 'upload'])->name('upload');
                Route::delete('/tempImageDelete/{id}', [ImageUploadController::class, 'deleteImage'])->name('delete'); // ИСПРАВЛЕНО: POST -> DELETE
            });

            // -------- НАСТРОЙКИ ПРОДАВЦА --------
            Route::prefix('settings')->name('settings.')->group(function () {
                Route::get('/', [SettingsController::class, 'index'])->name('index');
                Route::get('/logistic', [SettingsController::class, 'index'])->name('logistic');
                Route::get('/contract', [SettingsController::class, 'index'])->name('contract');
                Route::post('/', [SettingsController::class, 'update'])->name('update');
                Route::post('/pvz', [SettingsController::class, 'PvzUpdateOrCreate'])->name('pvz');
            });

            // -------- ЗАКАЗЫ --------
            Route::prefix('orders')->name('orders.')->group(function () {
                Route::get('/', [OrdersController::class, 'index'])->name('index');
                Route::get('/ajax', [OrdersController::class, 'ajax'])->name('ajax');

                // Упаковка заказов
                Route::get('/packing', [OrdersController::class, 'getOrderForPacking'])->name('packing');
                Route::post('/packing', [PackingController::class, 'savePacking']);

                // Управление заказами
                Route::post('/SentForDelivery/{id}', [OrdersController::class, 'SentForDelivery'])->name('SentForDelivery'); // ИСПРАВЛЕНО: GET -> POST
                Route::get('/getDeliveryLabeles/{id}', [OrdersController::class, 'getDeliveryLabeles'])->name('getDeliveryLabeles');
                Route::post('/Sharing/{id}', [OrdersController::class, 'Sharing'])->name('Sharing'); // ИСПРАВЛЕНО: GET -> POST
            });

            // -------- ЗАКАЗЫ КНИГ --------
            Route::prefix('bookOrders')->name('bookOrders.')->group(function () {
                Route::get('/', [BookOrdersController::class, 'index'])->name('index');
                Route::get('/ajax', [BookOrdersController::class, 'ajax'])->name('ajax'); // ИСПРАВЛЕНО: POST -> GET
            });

            // -------- ПОЛЬЗОВАТЕЛИ --------
            Route::prefix('users')->name('users.')->group(function () {
                Route::get('/', [UsersController::class, 'index'])->name('index');
                Route::get('/ajax', [UsersController::class, 'ajax'])->name('ajax');
                Route::post('/GroupUpdate', [UsersController::class, 'updateGroup'])->name('GroupUpdate');
            });

            // -------- ФИНАНСЫ --------
            Route::prefix('finance')->name('finance.')->group(function () { // ИСПРАВЛЕНО: users. -> finance.
                Route::get('/payments', [PaymentsController::class, 'index'])->name('index');
                Route::get('/payments/ajaxPayments', [PaymentsController::class, 'ajaxPayments'])->name('ajaxPayments');
            });

            // -------- ЧАТ ПОДДЕРЖКИ --------
            Route::prefix('chat')->name('chat.')->group(function () {
                // Список админов для передачи чата
                Route::get('/admins-list', [ChatController::class, 'getAdminsForTransfer'])
                    ->name('admins.list')
                    ->middleware('admin');

                // Основные маршруты чата
                Route::get('/', [ChatController::class, 'index'])->name('index');
                Route::get('/ajax', [ChatController::class, 'ajax'])->name('ajax');
                Route::post('/store', [ChatController::class, 'store'])->name('store'); // ИСПРАВЛЕНО: GET -> POST

                // Статус пользователя
                Route::get('/user/{userId}/status', [ChatController::class, 'checkUserStatus']);

                // Конкретный чат
                Route::get('/{chat}', [ChatController::class, 'show'])->name('show');

                // Сообщения
                Route::post('/{chat}/message', [MessageController::class, 'store']);
                Route::put('/message/{message}', [MessageController::class, 'update'])->name('message.update');
                Route::delete('/message/{message}', [MessageController::class, 'destroy'])->name('message.destroy');
                Route::post('/{chat}/read', [MessageController::class, 'markAsRead']);
                Route::post('/{chat}/upload', [MessageController::class, 'upload']);

                // Файлы и изображения
                Route::get('/file/{message}', [MessageController::class, 'downloadFile'])
                    ->name('file.download')
                    ->where('message', '[0-9]+');
                Route::get('/image/{message}', [MessageController::class, 'showImage'])
                    ->name('image.show')
                    ->where('message', '[0-9]+');

                // Передача чата (только для админов)
                Route::post('/{chat}/transfer', [ChatController::class, 'transferChat'])
                    ->name('transfer')
                    ->middleware('admin');
            });
        });

        // ========================================
        // ТЕСТЫ
        // ========================================
        Route::prefix('tests')->name('tests.')->group(function () {
            // TODO: Добавить тестовые маршруты
        });

        // ========================================
        // ВЫХОД
        // ========================================
        Route::post('/logout', [LoginController::class, 'logout'])->name('logout'); // ИСПРАВЛЕНО: GET -> POST
    });

    // ============================================
    // ГРУППА МАРШРУТОВ ДЛЯ АДМИНИСТРАТОРОВ
    // ============================================
    Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

        // -------- УПРАВЛЕНИЕ .ENV --------
        Route::prefix('env')->name('env.')->group(function () {
            Route::get('/', [EnvController::class, 'index'])->name('index');
            Route::put('/', [EnvController::class, 'update'])->name('update');

            // Бэкапы .env
            Route::post('/backup', [EnvController::class, 'createBackup'])->name('backup');
            Route::post('/restore/{filename}', [EnvController::class, 'restoreBackup'])->name('restore');
            Route::delete('/backup/{filename}', [EnvController::class, 'deleteBackup'])->name('delete-backup');
        });

        // -------- ГРУППЫ ТОВАРОВ --------
        Route::prefix('productGroups')->name('productGroups.')->group(function () {
            Route::get('/', [ProductGroupController::class, 'index'])->name('index');
            Route::get('/ajax', [ProductGroupController::class, 'ajax'])->name('ajax');
            Route::get('/create', [ProductGroupController::class, 'create'])->name('create');
            Route::post('/', [ProductGroupController::class, 'store'])->name('store');
            Route::get('/{id}', [ProductGroupController::class, 'edit'])->name('edit');
            Route::put('/{id}', [ProductGroupController::class, 'update'])->name('update');
            Route::delete('/{id}', [ProductGroupController::class, 'destroy'])->name('destroy');
        });

        // -------- УПРАВЛЕНИЕ ПРОДАВЦАМИ --------
        Route::prefix('sellers')->name('sellers.')->group(function () {
            Route::get('/', [SellersController::class, 'index'])->name('index');
            Route::get('/ajax', [SellersController::class, 'ajax'])->name('ajax');
            Route::patch('/contracts/{contract_id}/status', [SellersController::class, 'updateContractStatus']) // ИСПРАВЛЕНО: POST -> PATCH
            ->name('contracts.status.update');
        });

        // -------- БЮДЖЕТ --------
        Route::prefix('budget')->name('budget.')->group(function () {
            Route::get('/', [BudgetController::class, 'index'])->name('index');
            Route::post('/invoice', [BudgetController::class, 'invoice'])->name('invoice');
            Route::get('/transactions', [BudgetController::class, 'getTransactions'])->name('transactions');
            Route::post('/income', [BudgetController::class, 'addIncome'])->name('income');
            Route::post('/expense', [BudgetController::class, 'addExpense'])->name('expense');
            Route::post('/manager-balance', [BudgetController::class, 'updateManagerBalance'])->name('manager-balance');
            Route::delete('/transaction/{id}', [BudgetController::class, 'deleteTransaction'])->name('delete');
        });
    });
});
