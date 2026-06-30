<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\BaseController;
use App\Http\Integrations\YandexDelivery\Requests\OrderInfoRequest;
use App\Http\Integrations\YandexDelivery\YandexDeliveryConnector;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\OrderItem;
use Response;

class OrdersController extends BaseController
{
    public function index(Request $request) {
        $this->shareCommonData($request);
        $orders = Order::where('user_id', Auth::id())->orderBy('created_at', 'desc')->get();
        return view('index', ['view'=>'pages.orders.index', 'title'=>'Заказы | Брауни Арт — маркетплейс качественных товаров с доставкой по России', 'orders' => $orders
        ]);
    }

    public function show(int $id, Request $request) {
        $this->shareCommonData($request);
        $order = Order::where('id', $id)->first();
        return view('index', ['view'=>'pages.orders.show', 'title' => 'Заказ №'.$id.' | Брауни Арт — маркетплейс качественных товаров с доставкой по России', 'order' => $order]);
    }

    public function sharing(int $itemId)
    {
        try {

            // Получаем заказ с товарами конкретного продавца
            $orderItem = OrderItem::find($itemId);

            if (!$orderItem) {
                if (request()->expectsJson()) {
                    return Response::json([
                        'success' => false,
                        'error' => 'Позиция не найдена',
                        'data' => null
                    ], 404);
                }
                return response('Заказ не найден', 404)
                    ->header('Content-Type', 'text/plain; charset=utf-8');
            }

            $sellerId = $orderItem->seller_id;
            $statusInfo = $orderItem->order->getSellerStatus($sellerId);
//            print_r($statusInfo);
            if ($statusInfo->status != 'OnTheWay') {
                if (request()->expectsJson()) {
                    return Response::json([
                        'success' => false,
                        'error' => 'Не подходящий статус заказа!',
                        'data' => null
                    ], 400);
                }
                return response('Не подходящий статус заказа!', 400)
                    ->header('Content-Type', 'text/plain; charset=utf-8');
            }

            // Проверяем наличие связи OrderItemDeliveryMethod и корректность delivery_id

            if (!isset($orderItem->OrderItemDeliveryMethod) || empty($orderItem->OrderItemDeliveryMethod->delivery_id)) {
                if (request()->expectsJson()) {
                    return Response::json([
                        'success' => false,
                        'error' => 'Для заказа не найден ID доставки',
                        'data' => null
                    ], 400);
                }
                return response('Для заказа не найден ID доставки', 400)
                    ->header('Content-Type', 'text/plain; charset=utf-8');
            }

            $deliveryId = $orderItem->OrderItemDeliveryMethod->delivery_id;

            $connector = new YandexDeliveryConnector();
            $request = new OrderInfoRequest($deliveryId);
            $result = $connector->send($request);

            // Проверяем статус ответа от API
            if ($result->failed()) {
                if (request()->expectsJson()) {
                    return Response::json([
                        'success' => false,
                        'error' => 'Не удалось получить информацию о заказе от службы доставки',
                        'data' => null
                    ], 500);
                }
                return response('Не удалось получить информацию о заказе от службы доставки', 500)
                    ->header('Content-Type', 'text/plain; charset=utf-8');
            }

            $resultData = $result->json();

            // Проверяем наличие sharing_url в ответе
            if (!isset($resultData['sharing_url']) || empty($resultData['sharing_url'])) {
                if (request()->expectsJson()) {
                    return Response::json([
                        'success' => false,
                        'error' => 'В ответе службы доставки отсутствует URL для шаринга',
                        'data' => null
                    ], 500);
                }
                return response('В ответе службы доставки отсутствует URL для шаринга', 500)
                    ->header('Content-Type', 'text/plain; charset=utf-8');
            }

            // Выполняем редирект на URL для шаринга
            return redirect($resultData['sharing_url']);

        } catch (\Illuminate\Http\Client\RequestException $e) {
            if (request()->expectsJson()) {
                return Response::json([
                    'success' => false,
                    'error' => 'Ошибка соединения с сервисом доставки',
                    'data' => null
                ], 503);
            }
            return response('Ошибка соединения с сервисом доставки', 503)
                ->header('Content-Type', 'text/plain; charset=utf-8');

        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return Response::json([
                    'success' => false,
                    'error' => 'Внутренняя ошибка сервера',
                    'data' => null
                ], 500);
            }
            return response('Внутренняя ошибка сервера', 500)
                ->header('Content-Type', 'text/plain; charset=utf-8');
        }
    }
}
