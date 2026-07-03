<?php

namespace App\Http\Controllers;

use App\Models\BookOrder;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\LegalEntityDetail;
use App\Models\Order;
use App\Models\ShopMode;
use App\Models\UserPvz;
use App\Services\DeliveryCalculatorService;
use App\Services\TbankService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Notifications\OrderShippedNotification;

class CheckoutController extends BaseController
{
    public function showCheckout(Request $request)
    {
        $this->shareCommonData($request);
        $shopMode = $this->ShopModeGet($request);
        $userId = Auth::id();
        $selectedCartItemIds = $request->input('cart_items', []);
        $legalDetailCheck = LegalEntityDetail::isUserLegalEntity($userId);

        if (empty($selectedCartItemIds)) {
            return back()->withErrors(['error' => 'Не выбраны позиции для оформления заказа']);
        }

        $cart = Cart::where('user_id', $userId)->first();
        if (!$cart) {
            return back()->withErrors(['error' => 'Корзина не найдена']);
        }

        $cartItems = CartItem::where('cart_id', $cart->id)
            ->whereIn('id', $selectedCartItemIds)
            ->with('seller')
            ->get();

        if ($cartItems->isEmpty()) {
            return back()->withErrors(['error' => 'Выбранные позиции не найдены в корзине']);
        }

        // Логика для книг (ShopMode=8)
        if ((int)$shopMode->id === 8) {
            $subtotal = $cartItems->sum(fn($item) => $item->quantity * ($item->getProduct()->getProductPrice() ?? 0));

            return view('index', [
                'view' => 'pages.checkout',
                'cartItems' => $cartItems,
                'subtotal' => $subtotal,
                'deliveryCost' => 0,
                'deliveryBreakdown' => [],
                'totalAmount' => $subtotal,
                'selectedCartItemIds' => $selectedCartItemIds,
                'Cards' => (new TbankService())->GetCards(customerKey: Auth::id()),
                'legalDetailCheck' => $legalDetailCheck,
                'title' => 'Оформление заказа | Брауни Арт — маркетплейс качественных товаров с доставкой по России',
                'isBooksMode' => true,
            ]);
        }

        // Обычная логика для товаров
        $subtotal = $cartItems->sum(fn($item) => $item->quantity * ($item->getProduct()->getProductPrice() ?? 0));

        $deliveryData = ['total' => 0, 'breakdown' => []];
        if ($shopMode->Model === 'App\Models\Products') {
            $deliveryCalculator = new DeliveryCalculatorService();
            $deliveryData = $deliveryCalculator->calculate($cartItems);
        }
        $totalAmount = $subtotal + ($deliveryData['total'] ?? 0);

        return view('index', [
            'view' => 'pages.checkout',
            'cartItems' => $cartItems,
            'subtotal' => $subtotal,
            'deliveryCost' => $deliveryData['total'] ?? 0,
            'deliveryBreakdown' => $deliveryData['breakdown'] ?? [],
            'totalAmount' => $totalAmount,
            'selectedCartItemIds' => $selectedCartItemIds,
            'Cards' => (new TbankService())->GetCards(customerKey: Auth::id()),
            'legalDetailCheck' => $legalDetailCheck,
            'title' => 'Оформление заказа | Брауни Арт — маркетплейс качественных товаров с доставкой по России',
            'isBooksMode' => false,
        ]);
    }

    public function processOrder(Request $request)
    {
        $userId = Auth::id();
        $shopMode = $this->ShopModeGet($request);
        $paymentMethod = $request->input('pay_type');
        $selectedItems = $request->input('selected_items', []);
        $SelectedCard = $request->input('payment-card');

        try {
            if ((int)$shopMode->id === 8) {
                return $this->processBooksOrder($request, $userId, $paymentMethod, $selectedItems, $SelectedCard, $shopMode);
            }
            return $this->processGoodsOrder($request, $userId, $paymentMethod, $selectedItems, $SelectedCard, $shopMode);
        } catch (\Exception $e) {
            \Log::error('Ошибка оформления заказа: ' . $e->getMessage());
            return redirect()->route('cart.index')
                ->withErrors(['error' => 'Произошла ошибка при оформлении заказа: ' . $e->getMessage()])
                ->withInput();
        }
    }

    private function processBooksOrder(Request $request, int $userId, string $paymentMethod, array $selectedItems, ?string $SelectedCard, ShopMode $shopMode)
    {
        $cart = Cart::where('user_id', $userId)->firstOrFail();

        $cartItems = CartItem::where('cart_id', $cart->id)
            ->whereIn('id', $selectedItems)
            ->with(['seller'])
            ->get();

        if ($cartItems->isEmpty()) {
            throw new \Exception('Не выбраны позиции для оформления заказа');
        }

        $pvzId = UserPvz::where('user_id', $userId)
            ->where('last', 1)
            ->value('id');

        if (!$pvzId) {
            throw new \Exception('Не выбран пункт выдачи для получения заказа');
        }

        $tbankService = new TbankService();
        $paymentUrls = [];
        $ordersCreated = [];

        foreach ($cartItems as $cartItem) {
            $product = $cartItem->getProduct(); // Book

            // Проверка: уже куплена ли эта книга
            if (BookOrder::where('user_id', $userId)->where('book_id', $product->id)->exists()) {
                throw new \Exception("Книга «{$product->GetProductName()}» уже была куплена вами ранее");
            }

            DB::transaction(function () use ($userId, $cartItem, $product, $pvzId, $paymentMethod, $SelectedCard, &$tbankService, &$paymentUrls, &$ordersCreated, $shopMode) {
                $amount = $cartItem->quantity * ($product->getProductPrice() ?? 0);

                // Создаём заказ книги
                $bookOrder = BookOrder::create([
                    'user_id' => $userId,
                    'book_id' => $product->id,
                    'seller_id' => $cartItem->seller_id,
                    'amount' => $amount,
                    'payment_method' => $paymentMethod,
                    'status' => 'pending',
                ]);

                $ordersCreated[] = $bookOrder;

                // Уведомление продавца
                $seller = \App\Models\Seller::findOrFail($cartItem->seller_id);
                $users = $seller->users()->whereNotNull('email')->get();
                if ($users->isNotEmpty()) {
                    $seller->notifyRelatedUsers(
                        subject: 'Оформлен новый заказ на книгу',
                        greeting: 'Здравствуйте, ' . $seller->name . '!',
                        line: 'Оформлен заказ на книгу, обработайте его в личном кабинете!',
                        actionUrl: 'https://id.brauniart.shop',
                        actionText: 'Перейти в кабинет продавца',
                    );
                }

                // Удаляем позицию из корзины
                $cartItem->delete();

                // Обработка оплаты
                if ($amount > 0) {
                    // Для книг создаём временный Order только для передачи в processPayment
                    $tempOrder = \App\Models\Order::forceCreate([
                        'user_id' => $userId,
                        'amount' => $amount,
                        'status' => 'pending',
                        'PickUpPoint' => $pvzId,
                    ]);

                    $paymentResult = $this->processPayment($tempOrder, $paymentMethod, $SelectedCard);
                    $tempOrder->delete();

                    if (!$paymentResult['Success']) {
                        throw new \Exception('Ошибка интеграции с платёжной системой: ' . ($paymentResult['Error'] ?? 'неизвестная ошибка'));
                    }

                    $bookOrder->update([
                        'payment_id' => $paymentResult['PaymentId'] ?? null,
                        'payment_meta' => $paymentResult['result'] ?? null,
                    ]);

                    if (isset($paymentResult['Paid']) && $paymentResult['Paid'] === 'CONFIRMED') {
                        $bookOrder->forceFill([
                            'status' => 'paid',
                            'paid_at' => now(),
                        ])->save();
                    } else {
                        // Перенаправление на оплату
                        $paymentUrl = $paymentResult['result']['pdfUrl'] ?? $paymentResult['PaymentURL'] ?? null;
                        if (!$paymentUrl) {
                            throw new \Exception('Не удалось получить URL для оплаты');
                        }
                        session(['payment_url_' . $bookOrder->id => $paymentUrl]);
                        $paymentUrls[$bookOrder->id] = $paymentUrl;
                    }
                } else {
                    // БЕСПЛАТНАЯ КНИГА: сразу ставим оплачено
                    $bookOrder->forceFill([
                        'status' => 'paid',
                        'paid_at' => now(),
                        'payment_method' => 'free', // можно явно указать
                    ])->save();
                }
            });
        }

        if (!empty($paymentUrls)) {
            return redirect(reset($paymentUrls));
        }

        return redirect()->route('orders')
            ->with('success', 'Заказы на книги успешно оформлены!');
    }

    private function processGoodsOrder(Request $request, int $userId, string $paymentMethod, array $selectedItems, ?string $SelectedCard, ShopMode $shopMode)
    {
        try {
            $orderId = null;
            $paymentUrl = null;

            DB::transaction(function () use ($userId, $paymentMethod, $selectedItems, $SelectedCard, &$orderId, &$paymentUrl, $shopMode) {
                $cart = Cart::where('user_id', $userId)->firstOrFail();

                $cartItems = CartItem::where('cart_id', $cart->id)
                    ->whereIn('id', $selectedItems)
                    ->with(['seller', 'cart'])
                    ->get();

                if ($cartItems->isEmpty()) {
                    throw new \Exception('Не выбраны позиции для оформления заказа');
                }

                $orderAmount = $cartItems->sum(fn($item) => $item->quantity * ($item->getProduct()->getProductPrice() ?? 0));

                // Расчёт доставки только если это товары (а не книги)
                if ($shopMode->Model === 'App\Models\Products') {
                    $deliveryCalculator = new DeliveryCalculatorService();
                    $deliveryData = $deliveryCalculator->calculate($cartItems);
                    $finalOrderAmount = $orderAmount + $deliveryData['total'];
                } else {
                    $deliveryData = ['total' => 0, 'breakdown' => []];
                    $finalOrderAmount = $orderAmount;
                }

                $pvzId = UserPvz::where('user_id', $userId)
                    ->where('last', 1)
                    ->value('id');

                if (!$pvzId) {
                    throw new \Exception('Не выбран пункт выдачи для получения заказа');
                }

                $order = Order::create([
                    'user_id' => $userId,
                    'amount' => $finalOrderAmount,
                    'payment_method' => $paymentMethod,
                    'status' => 'pending',
                    'selected_items' => json_encode($selectedItems),
                    'PickUpPoint' => $pvzId,
                ]);

                $orderId = $order->id;

                // Статусы по продавцам
                $sellerIds = $cartItems->pluck('seller_id')->unique()->toArray();
                foreach ($sellerIds as $sellerId) {
                    \App\Models\OrderSellerStatus::create([
                        'order_id' => $order->id,
                        'seller_id' => $sellerId,
                        'status' => 'pending'
                    ]);
                }

                // Позиции заказа
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

                    // Уведомление продавца
                    $seller = \App\Models\Seller::findOrFail($cartItem->seller_id);
                    $users = $seller->users()->whereNotNull('email')->get();
                    if ($users->count() > 0) {
                        $seller->notifyRelatedUsers(
                            subject: 'Оформлен новый заказ',
                            greeting: 'Здравствуйте, ' . $seller->name . '!',
                            line: 'Оформлен новый заказ, обработайте его, пожалуйста, в личном кабинете!',
                            actionUrl: 'https://id.brauniart.shop',
                            actionText: 'Перейти в кабинет продавца',
                        );
                    }
                }

                // Удаляем из корзины выбранные позиции
                CartItem::where('cart_id', $cart->id)
                    ->whereIn('id', $selectedItems)
                    ->delete();

                // Платёж
                $paymentResult = $this->processPayment($order, $paymentMethod, $SelectedCard);

                if (!$paymentResult['Success']) {
                    throw new \Exception('Ошибка интеграции с платёжной системой: ' . ($paymentResult['Error'] ?? 'неизвестная ошибка'));
                }

                // Если платёж сразу подтверждён
                if (isset($paymentResult['Paid']) && $paymentResult['Paid'] === 'CONFIRMED') {
                    $order->forceFill([
                        'status' => 'paid',
                        'paid_at' => now(),
                    ])->save();
                } else {
                    // Перенаправление на оплату
                    $paymentUrl = $paymentResult['result']['pdfUrl'] ?? $paymentResult['PaymentURL'] ?? null;
                    if (!$paymentUrl) {
                        throw new \Exception('Не удалось получить URL для оплаты');
                    }
                    session(['payment_url_' . $order->id => $paymentUrl]);
                }
            });

            if ($paymentUrl) {
                return redirect($paymentUrl);
            }

            return redirect()->route('orders')
                ->with('success', 'Заказ успешно оформлен!');
        } catch (\Exception $e) {
            \Log::error('Ошибка оформления обычного заказа: ' . $e->getMessage());
            return redirect()->route('cart.index')
                ->withErrors(['error' => 'Произошла ошибка при оформлении заказа: ' . $e->getMessage()])
                ->withInput();
        }
    }

    private function ShopModeGet(Request $request): ShopMode
    {
        $CurrentShopMode = null;

        if (isset($this->shopMode)) {
            $CurrentShopMode = $this->shopMode;
        } elseif ($request->cookie('ShopMode') !== null) {
            $CurrentShopMode = $request->cookie('ShopMode');
        } else {
            $CurrentShopMode = 1;
        }

        return ShopMode::findOrFail($CurrentShopMode);
    }

}
