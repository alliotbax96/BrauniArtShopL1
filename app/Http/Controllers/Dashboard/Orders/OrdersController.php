<?php

namespace App\Http\Controllers\Dashboard\Orders;

use App\Http\Controllers\Dashboard\BaseController;
use App\Http\Integrations\YandexDelivery\Requests\CreateOfferRequest;
use App\Http\Integrations\YandexDelivery\Requests\GenerateLabelsRequest;
use App\Http\Integrations\YandexDelivery\Requests\OffersConfirmRequest;
use App\Http\Integrations\YandexDelivery\Requests\OrderInfoRequest;
use App\Http\Integrations\YandexDelivery\YandexDeliveryConnector;
use App\Models\OrderBox;
use App\Models\OrderItem;
use App\Models\OrderItemDeliveryMethod;
use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use Response;
use Illuminate\Support\Facades\Log;

class OrdersController extends BaseController
{
    public function index() {
        if(!Auth::user()->groupInfo()->hasPermission('view_orders')) {
            abort(403, 'У вас нет прав для просмотра данного раздела!');
        }
        $this->shareCommonData(); // вызываем один раз
        return view('dashboard.index',['View' => 'dashboard.orders.index', 'title'=>'Заказы | Единая система BaID', 'PageName'=>'Заказы', 'InPageName'=>'Заказы товаров', 'modalContent'=>'orderPacking']);
    }

    public function ajax(Request $request)
    {
        if(!Auth::user()->groupInfo()->hasPermission('view_orders')) {
            abort(403, 'У вас нет прав для просмотра данного раздела!');
        }
        $sellerId = Auth::user()->getFirstSeller()->id;

        // Получаем параметры от DataTables
        $draw = request()->input('draw', 1);
        $start = request()->input('start', 0);
        $length = request()->input('length', 10);
        $search = request()->input('search.value', '');
        $orderColumn = request()->input('order.0.column', 0);
        $orderDir = request()->input('order.0.dir', 'desc');

        // Определяем поле для сортировки
        $columns = ['id', 'amount', 'status', 'created_at'];
        $orderField = $columns[$orderColumn] ?? 'created_at';

        // Базовый запрос с фильтрацией по продавцу
        $query = Order::whereHas('items', function ($orderQuery) use ($sellerId) {
            $orderQuery->where('seller_id', $sellerId);
        })->with([
            'items' => function ($itemQuery) use ($sellerId) {
                $itemQuery->where('seller_id', $sellerId)->with('product');
            },
            'statusInfo'
        ]);

        // Поиск
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%");
            });
        }

        // Сортировка и пагинация
        $query->orderBy($orderField, $orderDir);
        $total = $query->count();
        $orders = $query->skip($start)->take($length)->get();

        // Форматируем данные
        $data = [];
        foreach ($orders as $order) {
            // Сумма позиций
            $sellerTotal = $order->items->sum(function ($item) {
                return $item->quantity * $item->price;
            });

            // Форматируем позиции — теперь с проверкой на пустые данные
            $itemsHtml = '';
            if ($order->items && $order->items->count() > 0) {
                foreach ($order->items as $item) {
                    $imageUrl = $item->product->getMainImage()
                        ? 'https://s3.ru1.storage.beget.cloud/d5833d93d74c-brauniartfiles/' . $item->product->getMainImage()
                        : '/images/default-product.png';

                    $itemsHtml .= '
                                   <a href="javascript:void(0)" class="hstack gap-3">
                                      <div class="avatar-image avatar-md">
                                          <img src="' . $imageUrl . '" alt="" class="img-fluid">
                                      </div>
                                      <div>
                                          <span class="text-truncate-2-line">' .
                                                      htmlspecialchars($item->product->getProductName() ?? 'Товар без названия') . '(x' . $item->quantity . ')
                                          </span>
                                          <small class="fs-12 fw-normal text-muted"></small>
                                      </div>
                                  </a>';
                }
            } else {
                // Если позиций нет — показываем сообщение
                $itemsHtml = '<span class="text-muted">Нет позиций</span>';
            }

            // Получаем данные статуса из отношения statusInfo
            $statusInfo = $order->getSellerStatus($sellerId)->statusInfo;
            $statusName = $statusInfo ? $statusInfo->name : $order->status;
            $statusColor = $statusInfo ? $statusInfo->color : 'secondary';

            // Действия (как в вашей вёрстке)
            $actionsHtml = '
            <div class="hstack gap-2 justify-content-end">
                <div class="dropdown">
            <a href="javascript:void(0)" class="avatar-text avatar-md" data-bs-toggle="dropdown" data-bs-offset="0,21">
                <i class="feather feather-more-horizontal"></i>
            </a>
            <ul class="dropdown-menu order-actions">';

            $actionsCount = 0;

            if($statusInfo->slug == 'paid'){
                $actionsCount++;
                $actionsHtml .= '
                <li>
                  <a class="dropdown-item loadOrderBtn" data-id="'.$order->id.'" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#addNewTasks">
                    <i class="feather feather-archive me-3"></i>
                    <span>Собрать</span>
                  </a>
                </li>
                ';
            }

            if($statusInfo->slug == 'assembly'){
                $actionsCount++;
                $actionsHtml .= '
                <li>
                 <a class="dropdown-item GoToDelivery" data-id="'.$order->id.'" href="javascript:void(0)">
                   <i class="feather feather-send me-3"></i>
                   <span>Отгрузить</span>
                 </a>
                </li>
                ';
            }

            if($statusInfo->slug == 'SentForDelivery'){
                $actionsCount++;
                $actionsHtml .= '
                <li>
                 <a class="dropdown-item" href="/seller/orders/getDeliveryLabeles/'.$order->id.'" target="_blank">
                  <i class="feather feather-printer me-3"></i>
                  <span>Распечатать ярлыки</span>
                 </a>
                </li>
                ';
            }

            if($statusInfo->slug == 'OnTheWay'){
                $actionsCount++;
                $actionsHtml .= '
                <li>
                 <a class="dropdown-item" href="/seller/orders/Sharing/'.$order->id.'" target="_blank">
                   <i class="feather feather-compass me-3"></i>
                   <span>Отследить</span>
                 </a>
                </li>
                ';
            }

            if($actionsCount == 0){
                $actionsHtml .= '
                <li>
                 <a class="dropdown-item" href="#">
                   <i class="feather feather-alert-circle me-3"></i>
                   <span>Действия недоступны</span>
                 </a>
                </li>
                ';
            }

            $actionsHtml .='</ul>
            </div>
            </div>
            ';

            $data[] = [
                'id' => $order->id,
                'amount' => $order->amount,
                'seller_total' => number_format($sellerTotal, 2, '.', ' '),
                'status' => $statusName, // Используем имя статуса из statusInfo или исходное поле
                'status_color' => $statusColor, // Цвет из statusInfo или 'secondary' по умолчанию
                'created_at' => $order->created_at->format('d.m.Y H:i'),
                'items_html' => $itemsHtml, // Теперь всегда есть значение
                'status_name' => $order->statusInfo->name ?? $order->status,
                'actions' => $actionsHtml
            ];
        }

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $data
        ]);
    }

    public function getOrderForPacking(Request $request)
    {
        if(!Auth::user()->groupInfo()->hasPermission('processing_orders')) {
            abort(403, 'У вас нет прав для просмотра данного раздела!');
        }
        $orderId = $request->input('order_id', 1); // Статичный ID для тестирования24
        $order = Order::with(['items' => function ($query) {
            $query->where('seller_id', Auth::user()->getSellerId()); // ID продавца
        }])->find($orderId);

        if (!$order) {
            return Response::json([
                'success' => false,
                'error' => 'Заказ не найден'
            ]);
        }

        // Форматируем данные для клиента
        $formattedItems = $order->items->map(function ($item) {
            return [
                'item_id'=>$item->id,
                'product_id' => $item->product_id,
                'img' => $item->product->getMainImage(),
                'name' => $item->product->getProductName() ?? 'Товар без названия',
                'sku' => $item->product->productCode ?? 'Нет артикула',
                'quantity' => $item->quantity,
                'weight' => $item->product->productWeight ?? 1.0, // вес единицы товара
                'order_quantity' => $item->quantity,
                'seller_id'=>$item->seller_id// общее количество в заказе
            ];
        })->toArray();

        return Response::json([
            'success' => true,
            'data' => [
                'order_id' => $orderId,
                'items' => $formattedItems
            ]
        ]);
    }

    public function SentForDelivery(int $orderId)
    {
        if(!Auth::user()->groupInfo()->hasPermission('processing_orders')) {
            abort(403, 'У вас нет прав для просмотра данного раздела!');
        }
        try {
            $sellerId = Auth::user()->getFirstSeller()->id;

            // Получаем заказ с товарами конкретного продавца
            $order = Order::with([
                'items' => fn($query) => $query->where('seller_id', $sellerId)
            ])->find($orderId);

            if (!$order) {
                return Response::json([
                    'success' => false,
                    'error' => 'Заказ не найден',
                    'data' => null
                ], 404);
            }

            $sellerStatus = $order->getSellerStatus($sellerId);
            if ($sellerStatus->status != 'assembly') {
                return Response::json([
                    'success' => false,
                    'error' => 'Не подходящий статус заказа!',
                    'data' => null
                ]);
            }

            // Проверяем, есть ли товары продавца в заказе
            if ($order->items->isEmpty()) {
                return Response::json([
                    'success' => false,
                    'error' => 'В заказе нет товаров данного продавца',
                    'data' => null
                ]);
            }

            // Получаем продавца и точку выдачи
            $seller = Seller::where('id', $sellerId)->first();
            if (!$seller) {
                return Response::json([
                    'success' => false,
                    'error' => 'Продавец не найден',
                    'data' => null
                ], 404);
            }

            if (!isset($seller->SellerPvz->pvz)) {
                return Response::json([
                    'success' => false,
                    'error' => 'У продавца не настроена точка выдачи',
                    'data' => null
                ]);
            }

            // Получаем коробки заказа
            $orderBoxes = OrderBox::where('order_id', $orderId)
                ->where('seller_id', $sellerId)
                ->with('items.product') // Загружаем связанные продукты
                ->get();

            if ($orderBoxes->isEmpty()) {
                return Response::json([
                    'success' => false,
                    'error' => 'Для заказа не найдены коробки',
                    'data' => null
                ]);
            }

            // Формируем данные для доставки
            [$places, $items] = $this->prepareDeliveryData($orderBoxes, $sellerId, $orderId);

            // Создаём и отправляем запрос на создание предложения
            $connector = new YandexDeliveryConnector();
            $offerId = $this->createOffer($connector, $seller, $order, $places, $items);
            if (!$offerId) {
                return Response::json([
                    'success' => false,
                    'error' => 'Не удалось создать предложение доставки',
                    'data' => null
                ]);
            }

            // Подтверждаем предложение
            $requestId = $this->confirmOffer($connector, $offerId);
            if (!$requestId) {
                return Response::json([
                    'success' => false,
                    'error' => 'Не удалось подтвердить предложение доставки',
                    'data' => null
                ]);
            }

            // Сохраняем ID доставки для товаров
            $this->saveDeliveryIds($orderBoxes, $requestId);

            // Обновляем статус заказа
            $order->getSellerStatus($sellerId)->update([
                'status' => 'SentForDelivery'
            ]);

            return Response::json([
                'success' => true,
                'data' => [
                    'offer_id' => $offerId,
                    'request_id' => $requestId
                ],
                'error' => null
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Ошибка в SentForDelivery: ' . $e->getMessage(), [
                'order_id' => $orderId,
                'seller_id' => $sellerId,
                'trace' => $e->getTraceAsString()
            ]);

            return Response::json([
                'success' => false,
                'error' => 'Внутренняя ошибка сервера',
                'data' => null
            ]);
        }
    }

    /**
     * Подготовка данных для доставки
     */
    private function prepareDeliveryData($orderBoxes, int $sellerId, int $orderId): array
    {
        $places = [];
        $items = [];

        foreach ($orderBoxes as $orderBox) {
            $places[] = [
                'physical_dims' => [
                    'weight_gross' => (int)($orderBox->weight * 1000),
                    'dx' => (int)$orderBox->length,
                    'dy' => (int)$orderBox->height,
                    'dz' => (int)$orderBox->width,
                ],
                'barcode' => "{$sellerId}-{$orderId}-{$orderBox->id}"
            ];

            foreach ($orderBox->items as $item) {
                Log::info($item);
                Log::info($item->orderItem);
                $items[] = [
                    'count' => $item->quantity,
                    'name' => $item->product->getProductName(),
                    'article' => $item->product->productCode,
                    'billing_details' => [
                        'unit_price' => $item->orderItem->price * 100,
                        'assessed_unit_price' => $item->orderItem->price * 100
                    ],
                    'place_barcode' => "{$sellerId}-{$orderId}-{$orderBox->id}",
                    'photo_links' => [
                        "https://s3.ru1.storage.beget.cloud/d5833d93d74c-brauniartfiles/{$item->product->getMainImage()}"
                    ]
                ];
            }
        }

        return [$places, $items];
    }

    /**
     * Создание предложения доставки
     */
    private function createOffer($connector, $seller, $order, array $places, array $items): ?string
    {
        try {
            $request = new CreateOfferRequest(
                "{$seller->id}-{$order->id}-" . time(),
                $seller->SellerPvz->pvz,
                $order->PickUpPointInfo->pvz,
                $places,
                [
                    'first_name' => $order->user->name,
                    'phone' => '+'.$order->user->phone
                ],
                $items
            );

            $result = $connector->send($request)->json();

            if (isset($result['offers'][0]['offer_id'])) {
                return $result['offers'][0]['offer_id'];
            }

            \Log::warning('CreateOfferRequest вернул неожиданный ответ', ['response' => $result]);
            return null;
        } catch (\Exception $e) {
            \Log::error('Ошибка при создании предложения доставки: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Подтверждение предложения доставки
     */
    private function confirmOffer($connector, string $offerId): ?string
    {
        try {
            $request = new OffersConfirmRequest($offerId);
            $result = $connector->send($request)->json();

            if (isset($result['request_id'])) {
                return $result['request_id'];
            }

            \Log::warning('OffersConfirmRequest вернул неожиданный ответ', ['response' => $result]);
            return null;
        } catch (\Exception $e) {
            \Log::error('Ошибка при подтверждении предложения доставки: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Сохранение ID доставки для товаров
     */

    private function saveDeliveryIds($orderBoxes, string $requestId): void
    {
        foreach ($orderBoxes as $orderBox) {
            foreach ($orderBox->items as $item) {
                // Проверяем, существует ли уже запись для этого order_item_id
                $existing = OrderItemDeliveryMethod::where('order_item_id', $item->order_item_id)->first();

                if (!$existing) {
                    OrderItemDeliveryMethod::create([
                        'order_item_id' => $item->order_item_id,
                        'delivery_id' => $requestId
                    ]);
                }
            }
        }
    }

    public function getDeliveryLabeles(int $orderId)
    {
        if(!Auth::user()->groupInfo()->hasPermission('processing_orders')) {
            abort(403, 'У вас нет прав для просмотра данного раздела!');
        }
        try {
            $sellerId = Auth::user()->getFirstSeller()->id;

            // Получаем заказ с товарами конкретного продавца
            $order = Order::with([
                'items' => fn($query) => $query->where('seller_id', $sellerId)
            ])->find($orderId);


            if (!$order) {
                if (request()->expectsJson()) {
                    return Response::json([
                        'success' => false,
                        'error' => 'Заказ не найден',
                        'data' => null
                    ], 404);
                }
                return response('Заказ не найден', 404)
                    ->header('Content-Type', 'text/plain; charset=utf-8');
            }

            $sellerStatus = $order->getSellerStatus($sellerId);
            if ($sellerStatus->status != 'SentForDelivery') {
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

            // Проверяем, есть ли товары продавца в заказе
            if ($order->items->isEmpty()) {
                if (request()->expectsJson()) {
                    return Response::json([
                        'success' => false,
                        'error' => 'В заказе нет товаров данного продавца',
                        'data' => null
                    ], 400);
                }
                return response('В заказе нет товаров данного продавца', 400)
                    ->header('Content-Type', 'text/plain; charset=utf-8');
            }

            // Проверяем наличие связи OrderItemDeliveryMethod и корректность delivery_id
            $firstItem = $order->items->first();
            if (!isset($firstItem->OrderItemDeliveryMethod) || empty($firstItem->OrderItemDeliveryMethod->delivery_id)) {
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

            $deliveryId = $firstItem->OrderItemDeliveryMethod->delivery_id;

            // Создаём коннектор и запрос
            $connector = new YandexDeliveryConnector();
            $request = new GenerateLabelsRequest($deliveryId);

            // Отправляем запрос и получаем ответ
            $result = $connector->send($request);

            // Проверяем статус ответа от API
            if ($result->failed()) {
                \Log::error('Ошибка получения этикеток от API доставки', [
                    'order_id' => $orderId,
                    'seller_id' => $sellerId,
                    'delivery_id' => $deliveryId,
                    'response' => $result->body()
                ]);
                if (request()->expectsJson()) {
                    return Response::json([
                        'success' => false,
                        'error' => 'Не удалось получить этикетки от службы доставки',
                        'data' => null
                    ], 500);
                }
                return response('Не удалось получить этикетки от службы доставки', 500)
                    ->header('Content-Type', 'text/plain; charset=utf-8');
            }

            // Проверяем, что ответ содержит PDF-данные
            $body = $result->body();
            if (empty($body)) {
                \Log::error('Пустой ответ от API при запросе этикеток', [
                    'order_id' => $orderId,
                    'delivery_id' => $deliveryId
                ]);
                if (request()->expectsJson()) {
                    return Response::json([
                        'success' => false,
                        'error' => 'Получен пустой ответ от службы доставки',
                        'data' => null
                    ], 500);
                }
                return response('Получен пустой ответ от службы доставки', 500)
                    ->header('Content-Type', 'text/plain; charset=utf-8');
            }

            // Возвращаем PDF с корректными заголовками
            return response($body)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="delivery_labels.pdf"')
                ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0');

        } catch (\Illuminate\Http\Client\RequestException $e) {
            \Log::error('HTTP-ошибка при получении этикеток: ' . $e->getMessage(), [
                'order_id' => $orderId,
                'seller_id' => $sellerId,
                'exception' => $e->getTraceAsString()
            ]);
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
            \Log::error('Неожиданная ошибка в getDeliveryLabeles: ' . $e->getMessage(), [
                'order_id' => $orderId,
                'seller_id' => $sellerId,
                'trace' => $e->getTraceAsString()
            ]);
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

    public function sharing(int $orderId)
    {
        if(!Auth::user()->groupInfo()->hasPermission('processing_orders')) {
            abort(403, 'У вас нет прав для просмотра данного раздела!');
        }
        try {
            $sellerId = Auth::user()->getFirstSeller()->id;

            // Получаем заказ с товарами конкретного продавца
            $order = Order::with([
                'items' => fn($query) => $query->where('seller_id', $sellerId)
            ])->find($orderId);


            if (!$order) {
                if (request()->expectsJson()) {
                    return Response::json([
                        'success' => false,
                        'error' => 'Заказ не найден',
                        'data' => null
                    ], 404);
                }
                return response('Заказ не найден', 404)
                    ->header('Content-Type', 'text/plain; charset=utf-8');
            }

            $sellerStatus = $order->getSellerStatus($sellerId);
            if ($sellerStatus->status != 'OnTheWay') {
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

            // Проверяем, есть ли товары продавца в заказе
            if ($order->items->isEmpty()) {
                if (request()->expectsJson()) {
                    return Response::json([
                        'success' => false,
                        'error' => 'В заказе нет товаров данного продавца',
                        'data' => null
                    ], 400);
                }
                return response('В заказе нет товаров данного продавца', 400)
                    ->header('Content-Type', 'text/plain; charset=utf-8');
            }

            // Проверяем наличие связи OrderItemDeliveryMethod и корректность delivery_id
            $firstItem = $order->items->first();
            if (!isset($firstItem->OrderItemDeliveryMethod) || empty($firstItem->OrderItemDeliveryMethod->delivery_id)) {
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

            $deliveryId = $firstItem->OrderItemDeliveryMethod->delivery_id;

            $connector = new YandexDeliveryConnector();
            $request = new OrderInfoRequest($deliveryId);
            $result = $connector->send($request);

            // Проверяем статус ответа от API
            if ($result->failed()) {
                \Log::error('Ошибка получения информации о заказе от API доставки', [
                    'order_id' => $orderId,
                    'seller_id' => $sellerId,
                    'delivery_id' => $deliveryId,
                    'response' => $result->body()
                ]);
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
                \Log::error('В ответе API отсутствует sharing_url', [
                    'order_id' => $orderId,
                    'delivery_id' => $deliveryId,
                    'response' => $resultData
                ]);
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
            \Log::error('HTTP-ошибка при получении информации о заказе: ' . $e->getMessage(), [
                'order_id' => $orderId,
                'seller_id' => $sellerId,
                'exception' => $e->getTraceAsString()
            ]);
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
            \Log::error('Неожиданная ошибка в sharing: ' . $e->getMessage(), [
                'order_id' => $orderId,
                'seller_id' => $sellerId,
                'trace' => $e->getTraceAsString()
            ]);
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
