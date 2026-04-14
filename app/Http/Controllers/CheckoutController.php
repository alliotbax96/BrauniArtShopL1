<?php

namespace App\Http\Controllers;

use App\Http\Integrations\TBank\TbankConnector;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\LegalEntityDetail;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\UserCard;
use App\Models\UserPvz;
use App\Services\DeliveryCalculatorService;
use App\Services\TbankService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\OrderSellerStatus;

class CheckoutController extends BaseController
{
    public function showCheckout(Request $request)
    {
        $this->shareCommonData(); // вызываем один раз
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
        $deliveryCalculator = new DeliveryCalculatorService();
        $deliveryData = $deliveryCalculator->calculate($cartItems);
//        $deliveryData = DeliveryCalculator::calculate($cartItems->toArray());
        $totalAmount = $subtotal + $deliveryData['total'];

        $tbankService = new TbankService();
        $TBankresult = $tbankService->GetCards(
            customerKey: Auth::id(),
        );

        return view('index', [
            'view'=> 'pages.checkout',
            'cartItems' => $cartItems,
            'subtotal' => $subtotal,
            'deliveryCost' => $deliveryData['total'],
            'deliveryBreakdown' => $deliveryData['breakdown'],
            'totalAmount' => $totalAmount,
            'selectedCartItemIds' => $selectedCartItemIds,
            'Cards' => $TBankresult,
            'legalDetailCheck' => $legalDetailCheck,
            'title'=> 'Оформление заказа | Брауни Арт — маркетплейс качественных товаров с доставкой по России',
        ]);
    }

    public function processOrder(Request $request)
    {
        $userId = Auth::id();
        $paymentMethod = $request->input('pay_type');
        $selectedItems = $request->input('selected_items', []);
        $SelectedCard = $request->input('payment-card');

        try {
            // Инициализируем переменную для хранения ID заказа
            $orderId = null;

            DB::transaction(function () use ($userId, $paymentMethod, $selectedItems, $SelectedCard, &$orderId) {
                // Получаем корзину пользователя
                $cart = Cart::where('user_id', $userId)->firstOrFail();

                // Получаем выбранные позиции корзины с данными
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
                    return $item->quantity * ($product->price ?? 0);
                });

                // Расчёт доставки
                $deliveryCalculator = new DeliveryCalculatorService();
                $deliveryData = $deliveryCalculator->calculate($cartItems);
                $finalOrderAmount = $orderAmount + $deliveryData['total'];

                // Создаём заказ
                $order = Order::create([
                    'user_id' => $userId,
                    'amount' => $finalOrderAmount,
                    'payment_method' => $paymentMethod,
                    'status' => 'pending',
                    'selected_items' => json_encode($selectedItems),
                    'PickUpPoint' => UserPvz::where('user_id', $userId)->where('last', 1)->firstOrFail()->id,
                ]);

                // Сохраняем ID заказа для использования вне транзакции
                $orderId = $order->id;

                // Собираем уникальные ID продавцов из позиций корзины
                $sellerIds = $cartItems->pluck('seller_id')->unique()->toArray();

                // Создаём записи в order_seller_statuses для каждого продавца
                foreach ($sellerIds as $sellerId) {
                    OrderSellerStatus::create([
                        'order_id' => $order->id,
                        'seller_id' => $sellerId,
                        'status' => 'pending' // Начальный статус для каждого продавца
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

                // Удаляем выбранные позиции из корзины
                CartItem::where('cart_id', $cart->id)
                    ->whereIn('id', $selectedItems)
                    ->delete();

                // Интеграция с эквайрингом
                $paymentResult = $this->processPayment($order, $paymentMethod, $SelectedCard);

                if ($paymentResult['Success'] != 1) {
                    throw new \Exception('Ошибка интеграции');
                }

                // Обновляем статус заказа после успешной оплаты
                if (isset($paymentResult['Paid'])) {
                    $order->update(['status' => 'paid']);

                    // Дополнительно обновляем статусы всех продавцов на 'paid' после успешной оплаты
                    $order->sellerStatuses()->update(['status' => 'paid']);
                } else {
                    // Если требуется перенаправление на страницу оплаты, сохраняем URL в сессии
                    if (!empty($paymentResult['result'])) {
                        session(['payment_url_' . $order->id => $paymentResult['result']['pdfUrl']]);
                    } else {
                        session(['payment_url_' . $order->id => $paymentResult['PaymentURL']]);
                    }
                    throw new \Exception('Требуется перенаправление на страницу оплаты');
                }
            });

            // Обработка результатов после успешной транзакции
            if (session()->has('payment_url_' . $orderId)) {
                // Перенаправляем на страницу оплаты
                return redirect(session('payment_url_' . $orderId));
            }

            return redirect()->route('orders', ['order_id' => $orderId])
                ->with('success', 'Заказ успешно оформлен и оплачен!');

        } catch (\Exception $e) {
            \Log::error('Ошибка оформления заказа: ' . $e->getMessage());

            // Если ошибка связана с необходимостью оплаты, перенаправляем на URL
            if (str_contains($e->getMessage(), 'Требуется перенаправление')) {
                $paymentUrl = session('payment_url_' . $orderId);
                if ($paymentUrl) {
                    return redirect($paymentUrl);
                }
            }
//            echo $e->getMessage();
                        return back()
                            ->withErrors(['error' => 'Произошла ошибка при оформлении заказа: ' . $e->getMessage()])
                            ->withInput();
        }
    }


    private function processPayment(Order $order, string $paymentMethod, string $SelectedCard = null): array
    {
      if($paymentMethod == 'card'){
          $tbankService = new TbankService();
          if($SelectedCard != 0){
              $result = $tbankService->init($order->id, $order->amount, Auth::id());
              if (!$result['Success']) {
                  return ['Success' => false, 'PaymentId' => null, 'Error' => 'Ошибка инициализации платежа!'];
              }
              $card = UserCard::where('user_id', Auth::id())->where('CardID', $SelectedCard)->first();
              if(!$card){
                  return ['Success' => false, 'PaymentId' => null, 'Error' => 'Карта не найдена!'];
              }
              $payResult = $tbankService->ReqPayment($result['PaymentId'], $card['RebildID']);
              if($payResult['Status'] != 'CONFIRMED'){
                  return ['Success' => false, 'PaymentId' => null, 'Error' => $payResult['Error']];
              }
//              return ['Success' => true, 'PaymentId' => '1111', 'Paid'=> 'CONFIRMED'];
          } else {

              return $tbankService->init($order->id, $order->amount, Auth::id(),'Y');
          }
      }  elseif($paymentMethod == 'invoice') {
          $tbankService = new TbankService();
          $result = $tbankService->SendInvoice($order);
          if(!isset($result['pdfUrl'])){
              return ['Success' => false, 'result' => null];
          }
          return ['Success' => true, 'result' => $result];
      }
      return ['Success' => false, 'PaymentId' => null];
    }
}
