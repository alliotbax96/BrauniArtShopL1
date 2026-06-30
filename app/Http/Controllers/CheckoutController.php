<?php

namespace App\Http\Controllers;

use App\Http\Integrations\TBank\TbankConnector;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\LegalEntityDetail;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ShopMode;
use App\Models\UserCard;
use App\Models\UserPvz;
use App\Services\DeliveryCalculatorService;
use App\Services\TbankService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\OrderSellerStatus;
use App\Notifications\OrderShippedNotification;

class CheckoutController extends BaseController
{
    public function showCheckout(Request $request) {

        $this->shareCommonData($request); // вызываем один раз
        $shopMode = $this->ShopModeGet($request);
        $userId = Auth::id();
        $selectedCartItemIds = $request->input('cart_items', []);
        $legalDetailCheck = LegalEntityDetail::isUserLegalEntity($userId);

        // Проверяем, что переданы ID позиций
        if (empty($selectedCartItemIds)) {
            return back()->withErrors(['error' => 'Не выбраны позиции для оформления заказа']);
        }

        // Получаем корзину пользователя
        $cart = Cart::where('user_id', $userId)->first();

        if (!$cart) {
            return back()->withErrors(['error' => 'Корзина не найдена']);
        }

        // Получаем только выбранные позиции корзины
        $cartItems = CartItem::where('cart_id', $cart->id)
            ->whereIn('id', $selectedCartItemIds)
            ->with('seller')
            ->get();

        // Если ни одна позиция не найдена — ошибка
        if ($cartItems->isEmpty()) {
            return back()->withErrors(['error' => 'Выбранные позиции не найдены в корзине']);
        }

        // Рассчитываем итоговую сумму товаров
        $subtotal = $cartItems->sum(function ($item) {
            $product = $item->getProduct();
            return $item->quantity * ($product->getProductPrice() ?? 0);
        });

        // Расчёт стоимости доставки
        if($shopMode->Model == 'App\Models\Products') {
            $deliveryCalculator = new DeliveryCalculatorService();
            $deliveryData = $deliveryCalculator->calculate($cartItems);
            $totalAmount = $subtotal + $deliveryData['total'];
        } else {
            $deliveryData = 0;
            $totalAmount = $subtotal;
        }

        $tbankService = new TbankService();
        $TBankresult = $tbankService->GetCards(
            customerKey: Auth::id(),
        );

        return view('index', [
            'view'=> 'pages.checkout',
            'cartItems' => $cartItems,
            'subtotal' => $subtotal,
            'deliveryCost' => isset($deliveryData['total']) ? $deliveryData['total'] : '',
            'deliveryBreakdown' => isset($deliveryData['breakdown']) ? $deliveryData['breakdown'] : '',
            'totalAmount' => $totalAmount,
            'selectedCartItemIds' => $selectedCartItemIds,
            'Cards' => $TBankresult,
            'legalDetailCheck' => $legalDetailCheck,
            'title'=> 'Оформление заказа | Брауни Арт — маркетплейс качественных товаров с доставкой по России',
        ]);
    }

    private function ShopModeGet(Request $request){

        if (isset($shopMode)) {
            $CurrentShopMode = $shopMode;
        } elseif ($request->cookie('ShopMode') !== null) {
            $CurrentShopMode = $request->cookie('ShopMode');
        } else {
            $CurrentShopMode = 1;
        }

        return ShopMode::find($CurrentShopMode);
    }

    public function processOrder(Request $request)
    {
        $userId = Auth::id();
        $shopMode = $this->ShopModeGet($request);
        $paymentMethod = $request->input('pay_type');
        $selectedItems = $request->input('selected_items', []);
        $SelectedCard = $request->input('payment-card');

        try {
            $orderId = null;
            $paymentUrl = null;

            DB::transaction(function () use ($userId, $paymentMethod, $selectedItems, $SelectedCard, &$orderId, &$paymentUrl, $shopMode) {
                // Этап 1: создание заказа
                $cart = Cart::where('user_id', $userId)->firstOrFail();

                $cartItems = CartItem::where('cart_id', $cart->id)
                    ->whereIn('id', $selectedItems)
                    ->with(['seller', 'cart'])
                    ->get();

                if ($cartItems->isEmpty()) {
                    throw new \Exception('Не выбраны позиции для оформления заказа');
                }

                // Рассчитываем сумму заказа
                $orderAmount = $cartItems->sum(function ($item) {
                    $product = $item->getProduct();
                    return $item->quantity * ($product->getProductPrice() ?? 0);
                });

                // Расчёт стоимости доставки
                if($shopMode->Model == 'App\Models\Products') {
                    $deliveryCalculator = new DeliveryCalculatorService();
                    $deliveryData = $deliveryCalculator->calculate($cartItems);
                    $finalOrderAmount = $orderAmount + $deliveryData['total'];
                } else {
                    $deliveryData = 0;
                    $finalOrderAmount = $orderAmount;
                }

                // Создаём заказ в статусе 'pending'
                $order = Order::create([
                    'user_id' => $userId,
                    'amount' => $finalOrderAmount,
                    'payment_method' => $paymentMethod,
                    'status' => 'pending',
                    'selected_items' => json_encode($selectedItems),
                    'PickUpPoint' => UserPvz::where('user_id', $userId)
                        ->where('last', 1)->firstOrFail()->id,
                ]);

                $orderId = $order->id;

                // Собираем уникальные ID продавцов из позиций корзины
                $sellerIds = $cartItems->pluck('seller_id')->unique()->toArray();

                // Создаём записи в order_seller_statuses для каждого продавца
                foreach ($sellerIds as $sellerId) {
                    OrderSellerStatus::create([
                        'order_id' => $order->id,
                        'seller_id' => $sellerId,
                        'status' => 'pending'
                    ]);
                }

                // Создаём позиции заказа
                foreach ($cartItems as $cartItem) {
                    $product = $cartItem->getProduct();

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $cartItem->product_id,
                        'product_type' => $cartItem->product_type,
                        'seller_id' => $cartItem->seller_id,
                        'quantity' => $cartItem->quantity,
                        'price' => $product->getProductPrice() ?? 0,
                        'name' => $product->GetProductName() ?? 'Товар без названия',
                    ]);

                    $seller = \App\Models\Seller::findOrFail($cartItem->seller_id);

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
                        subject: 'Оформлен новый заказ',
                        greeting: 'Здравствуйте, ' . $seller->name . '!',
                        line: 'Оформлен новый заказ, обработайте его пожалуйста в личном кабинете!',
                        actionUrl: 'https://id.brauniart.shop',
                        actionText: 'Перейти в кабинет продавца',
                    );
                }

                // Удаляем выбранные позиции из корзины
                CartItem::where('cart_id', $cart->id)
                    ->whereIn('id', $selectedItems)
                    ->delete();


                // Этап 2: обработка оплаты (внутри той же транзакции)
                $paymentResult = $this->processPayment($order, $paymentMethod, $SelectedCard);

                if (!$paymentResult['Success']) {
                    throw new \Exception('Ошибка интеграции с платёжной системой: ' . ($paymentResult['Error'] ?? 'неизвестная ошибка'));
                }

                $order->update(['paymentId' => $paymentResult['PaymentId'] ?? null]);
                if (isset($paymentResult['Paid']) && $paymentResult['Paid'] === 'CONFIRMED') {
                    // Успешная оплата: обновляем статус
                    $order->update(['status' => 'paid']);
                    $order->sellerStatuses()->update(['status' => 'paid']);
                } else {
                    // Требуется перенаправление на оплату
                    if (!empty($paymentResult['result']['pdfUrl'])) {
                        $paymentUrl = $paymentResult['result']['pdfUrl'];
                    } elseif (!empty($paymentResult['PaymentURL'])) {
                        $paymentUrl = $paymentResult['PaymentURL'];
                    }
                    if (!$paymentUrl) {
                        throw new \Exception('Не удалось получить URL для оплаты');
                    }
                }
            });
            Auth::user()->notify(new OrderShippedNotification($order));
            // Транзакция успешно завершена — все изменения сохранены
            if ($paymentUrl) {
                session(['payment_url_' . $orderId => $paymentUrl]);
                return redirect($paymentUrl);
            } else {
                return redirect()->route('orders', ['order_id' => $orderId])
                    ->with('success', 'Заказ успешно оформлен и оплачен!');
            }
        } catch (\Exception $e) {
            \Log::error('Ошибка оформления заказа: ' . $e->getMessage());
            return redirect()->route('cart.index')
                ->withErrors(['error' => 'Произошла ошибка при оформлении заказа: ' . $e->getMessage()])
                ->withInput();
        }
    }

    private function processPayment(Order $order, string $paymentMethod, ?string $selectedCard = null): array
    {
        // Валидация входных данных
        if (!$order || !$order->id) {
            return ['Success' => false, 'PaymentId' => null, 'Error' => 'Неверный заказ'];
        }

        $tbankService = new TbankService();

        if ($paymentMethod === 'card') {
            // Обработка оплаты картой, если указана карта
            if ($selectedCard !== null && $selectedCard !== '' && $selectedCard != 0) {
                // Инициализация платежа
                $initResult = $tbankService->init($order->id, $order->amount, Auth::id());
                if (!$initResult['Success']) {
                    return [
                        'Success' => false,
                        'PaymentId' => null,
                        'Error' => 'Ошибка инициализации платежа: ' . ($initResult['Error'] ?? 'неизвестная ошибка')
                    ];
                }

                $card = UserCard::where('CardID', $selectedCard)
                    ->first();
                if (!$card) {
                    return [
                        'Success' => false,
                        'PaymentId' => null,
                        'Error' => 'Карта не найдена'
                    ];
                }
                // Оплата с выбранной картой
                $payResult = $tbankService->ReqPayment($initResult['PaymentId'], $card->RebildID);
                if ($payResult['Status'] !== 'CONFIRMED') {
                    return [
                        'Success' => false,
                        'PaymentId' => $initResult['PaymentId'],
                        'Error' => $payResult['Error'] ?? 'Ошибка подтверждения платежа'
                    ];
                }

                return [
                    'Success' => true,
                    'PaymentId' => $initResult['PaymentId'],
                    'Paid' => 'CONFIRMED'
                ];
            } else {
                // Быстрая оплата без выбора карты
                // Инициализация платежа
                $initResult = $tbankService->init($order->id, $order->amount, Auth::id(), 'Y');
                return $initResult;
            }
        } elseif ($paymentMethod === 'invoice') {
            $invoiceResult = $tbankService->SendInvoice($order);
            if (!isset($invoiceResult['pdfUrl'])) {
                return [
                    'Success' => false,
                    'PaymentId' => null,
                    'Error' => 'Не удалось создать счёт на оплату: ' . ($invoiceResult['Error'] ?? 'неизвестная ошибка')
                ];
            }
            return [
                'Success' => true,
                'result' => $invoiceResult,
                'PaymentId' => $order->id // или другой идентификатор, если есть
            ];
        } else {
            return [
                'Success' => false,
                'PaymentId' => null,
                'Error' => 'Неподдерживаемый метод оплаты'
            ];
        }
    }

}
