<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\OrderItem;
use App\Models\OrderSellerStatus;
use App\Models\UserPvz;
use App\Services\DeliveryCalculatorService;
use App\Services\TbankService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderController extends ApiBaseController
{
    /**
     * Расчёт стоимости доставки
     */
    public function calculateDelivery(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cart_item_ids' => 'required|array|min:1',
            'cart_item_ids.*' => 'integer',
            'pvz_id' => 'required|string'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation error', 422, $validator->errors());
        }

        $userId = auth()->id();
        $cart = Cart::where('user_id', $userId)->first();

        if (!$cart) {
            return $this->errorResponse('Корзина не найдена', 404);
        }

        $cartItems = CartItem::where('cart_id', $cart->id)
            ->whereIn('id', $request->cart_item_ids)
            ->with('seller')
            ->get();

        if ($cartItems->isEmpty()) {
            return $this->errorResponse('Выбранные позиции не найдены', 404);
        }

        // Расчёт стоимости товаров
        $subtotal = $cartItems->sum(function ($item) {
            $product = $item->getProduct();
            return $item->quantity * ($product->getProductPrice() ?? 0);
        });

        // Расчёт доставки
        $deliveryCalculator = new DeliveryCalculatorService();
        $deliveryData = $deliveryCalculator->calculate($cartItems, $request->pvz_id);

        $totalAmount = $subtotal + $deliveryData['total'];

        return $this->successResponse([
            'subtotal' => number_format($subtotal, 2, '.', ''),
            'delivery' => [
                'total' => number_format($deliveryData['total'], 2, '.', ''),
                'breakdown' => $deliveryData['breakdown']
            ],
            'total' => number_format($totalAmount, 2, '.', ''),
            'currency' => 'RUB',
            'items_count' => $cartItems->count()
        ]);
    }

    /**
     * Оформление заказа
     */
    public function processOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cart_item_ids' => 'required|array|min:1',
            'cart_item_ids.*' => 'integer',
            'payment_method' => 'required|string|in:card,invoice',
            'pvz_id' => 'required|string',
            'card_id' => 'nullable|integer'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation error', 422, $validator->errors());
        }

        $userId = auth()->id();

        try {
            $orderId = null;
            $paymentUrl = null;
            $isPaid = false;

            DB::transaction(function () use ($userId, $request, &$orderId, &$paymentUrl, &$isPaid) {
                $cart = Cart::where('user_id', $userId)->firstOrFail();

                $cartItems = CartItem::where('cart_id', $cart->id)
                    ->whereIn('id', $request->cart_item_ids)
                    ->with(['seller', 'cart'])
                    ->get();

                if ($cartItems->isEmpty()) {
                    throw new \Exception('Не выбраны позиции для оформления заказа');
                }

                // Расчёт суммы
                $orderAmount = $cartItems->sum(function ($item) {
                    $product = $item->getProduct();
                    return $item->quantity * ($product->getProductPrice() ?? 0);
                });

                $deliveryCalculator = new DeliveryCalculatorService();
                $deliveryData = $deliveryCalculator->calculate($cartItems, $request->pvz_id);
                $finalOrderAmount = $orderAmount + $deliveryData['total'];

                // Сохраняем ПВЗ
                UserPvz::updateOrCreate(
                    ['user_id' => $userId, 'pvz' => $request->pvz_id],
                    [
                        'pvz_name' => $request->input('pvz_name', 'ПВЗ'),
                        'last' => true
                    ]
                );

                // Создаём заказ
                $order = Order::create([
                    'user_id' => $userId,
                    'amount' => $finalOrderAmount,
                    'payment_method' => $request->payment_method,
                    'status' => 'pending',
                    'selected_items' => json_encode($request->cart_item_ids),
                    'PickUpPoint' => UserPvz::where('user_id', $userId)
                        ->where('last', 1)->first()->id,
                ]);

                $orderId = $order->id;

                // Создаём статусы продавцов
                $sellerIds = $cartItems->pluck('seller_id')->unique()->toArray();
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
                }

                // Удаляем позиции из корзины
                CartItem::where('cart_id', $cart->id)
                    ->whereIn('id', $request->cart_item_ids)
                    ->delete();

                // Обработка оплаты
                $paymentResult = $this->processPayment($order, $request->payment_method, $request->card_id);

                if (!$paymentResult['Success']) {
                    throw new \Exception('Ошибка оплаты: ' . ($paymentResult['Error'] ?? 'неизвестная ошибка'));
                }

                $order->update(['paymentId' => $paymentResult['PaymentId'] ?? null]);

                if (isset($paymentResult['Paid']) && $paymentResult['Paid'] === 'CONFIRMED') {
                    $order->update(['status' => 'paid']);
                    $order->sellerStatuses()->update(['status' => 'paid']);
                    $isPaid = true;
                } else {
                    $paymentUrl = $paymentResult['PaymentURL'] ?? $paymentResult['result']['pdfUrl'] ?? null;
                    if (!$paymentUrl) {
                        throw new \Exception('Не удалось получить URL для оплаты');
                    }
                }
            });

            $response = [
                'order_id' => $orderId,
                'status' => $isPaid ? 'paid' : 'pending'
            ];

            if ($paymentUrl) {
                $response['payment_url'] = $paymentUrl;
            }

            return $this->successResponse($response, 'Заказ успешно создан');

        } catch (\Exception $e) {
            \Log::error('Ошибка оформления заказа: ' . $e->getMessage());
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    /**
     * Список заказов пользователя
     */
    public function userOrders(Request $request)
    {
        $perPage = $request->per_page ?? 20;

        $orders = Order::where('user_id', auth()->id())
            ->with(['items.product', 'sellerStatuses.seller', 'PickUpPointInfo'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        $formattedOrders = $orders->map(function ($order) {
            return $this->formatOrder($order);
        });

        return $this->paginatedResponse($orders->setCollection($formattedOrders));
    }

    /**
     * Детали заказа
     */
    public function show(int $id)
    {
        $order = Order::where('id', $id)
            ->where('user_id', auth()->id())
            ->with([
                'items.product',
                'items.OrderItemDeliveryMethod',
                'sellerStatuses.seller',
                'PickUpPointInfo'
            ])
            ->first();

        if (!$order) {
            return $this->errorResponse('Заказ не найден', 404);
        }

        return $this->successResponse($this->formatOrderDetail($order));
    }

    /**
     * Обработка оплаты
     */
    private function processPayment(Order $order, string $paymentMethod, ?int $cardId = null): array
    {
        $tbankService = new TbankService();

        if ($paymentMethod === 'card') {
            if ($cardId) {
                $initResult = $tbankService->init($order->id, $order->amount, auth()->id());
                if (!$initResult['Success']) {
                    return ['Success' => false, 'Error' => 'Ошибка инициализации'];
                }

                $card = \App\Models\UserCard::where('CardID', $cardId)->first();
                if (!$card) {
                    return ['Success' => false, 'Error' => 'Карта не найдена'];
                }

                $payResult = $tbankService->ReqPayment($initResult['PaymentId'], $card->RebildID);
                if ($payResult['Status'] !== 'CONFIRMED') {
                    return ['Success' => false, 'Error' => $payResult['Error'] ?? 'Ошибка оплаты'];
                }

                return ['Success' => true, 'PaymentId' => $initResult['PaymentId'], 'Paid' => 'CONFIRMED'];
            } else {
                return $tbankService->init($order->id, $order->amount, auth()->id(), 'Y');
            }
        } elseif ($paymentMethod === 'invoice') {
            $invoiceResult = $tbankService->SendInvoice($order);
            if (!isset($invoiceResult['pdfUrl'])) {
                return ['Success' => false, 'Error' => 'Не удалось создать счёт'];
            }
            return ['Success' => true, 'result' => $invoiceResult, 'PaymentId' => $order->id];
        }

        return ['Success' => false, 'Error' => 'Неподдерживаемый метод оплаты'];
    }

    /**
     * Форматирование заказа для списка
     */
    private function formatOrder(Order $order): array
    {
        return [
            'id' => $order->id,
            'amount' => number_format($order->amount, 2, '.', ''),
            'status' => $order->status,
            'status_label' => $this->getStatusLabel($order->status),
            'payment_method' => $order->payment_method,
            'items_count' => $order->items->count(),
            'created_at' => $order->created_at->format('d.m.Y H:i'),
            'delivery_address' => $order->PickUpPointInfo->pvz_name ?? null
        ];
    }

    /**
     * Форматирование деталей заказа
     */
    private function formatOrderDetail(Order $order): array
    {
        return [
            'id' => $order->id,
            'amount' => number_format($order->amount, 2, '.', ''),
            'status' => $order->status,
            'status_label' => $this->getStatusLabel($order->status),
            'payment_method' => $order->payment_method,
            'created_at' => $order->created_at->format('d.m.Y H:i'),
            'delivery_address' => $order->PickUpPointInfo->pvz_name ?? null,
            'items' => $order->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'price' => number_format($item->price, 2, '.', ''),
                    'quantity' => $item->quantity,
                    'total' => number_format($item->price * $item->quantity, 2, '.', ''),
                    'seller_name' => $item->seller->name ?? null,
                    'tracking' => $item->OrderItemDeliveryMethod
                        ? $item->OrderItemDeliveryMethod->delivery_id
                        : null
                ];
            }),
            'sellers' => $order->sellerStatuses->map(function ($status) {
                return [
                    'seller_id' => $status->seller_id,
                    'seller_name' => $status->seller->name ?? null,
                    'status' => $status->status,
                    'status_label' => $this->getStatusLabel($status->status)
                ];
            })
        ];
    }

    private function getStatusLabel(string $status): string
    {
        return match($status) {
            'pending' => 'Ожидает оплаты',
            'paid' => 'Оплачен',
            'assembly' => 'В сборке',
            'OnTheWay' => 'В пути',
            'delivered' => 'Доставлен',
            'cancelled' => 'Отменён',
            default => $status
        };
    }
}
