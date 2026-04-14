<?php

namespace App\Services;

use App\Http\Integrations\OrangeData\OrangeDataConnector;
use App\Http\Integrations\OrangeData\Requests\CreateOrderRequest;
use App\Http\Integrations\OrangeData\Requests\AddPositionRequest;
use App\Http\Integrations\OrangeData\Requests\AddPaymentRequest;
use App\Http\Integrations\OrangeData\Requests\SendOrderRequest;
use App\Models\Order;
use App\Services\DeliveryCalculatorService;
use Illuminate\Support\Facades\DB;

class OrangeDataService
{
    private OrangeDataConnector $connector;
    private DeliveryCalculatorService $deliveryCalculator;

    public function __construct(DeliveryCalculatorService $deliveryCalculator)
    {
        $this->connector = new OrangeDataConnector(
            inn: '7751222627',
            apiUrl: 'https://api.orangedata.ru:12003',
            signPkeyPath: base_path('controllers/libs/OrangeData/sert/private_key.pem'),
            sslClientKeyPath: base_path('controllers/libs/OrangeData/sert/7751222627_37957.key'),
            sslClientCrtPath: base_path('controllers/libs/OrangeData/sert/7751222627_37957.crt'),
            sslCaCertPath: base_path('controllers/libs/OrangeData/sert/cacert.pem'),
            sslClientCrtPass: '1234'
        );
        $this->deliveryCalculator = $deliveryCalculator;
    }

    /**
     * Основной метод обработки заказа для фискализации
     */
    public function processOrder(int $orderId): array
    {
        $order = Order::with(['user', 'items.seller', 'items.product'])->find($orderId);

        if (!$order) {
            http_response_code(404);
            exit();
        }

        // Преобразуем позиции заказа в формат, понятный калькулятору доставки
        $cartItems = $this->prepareCartItemsForDelivery($order->items);

        // Расчёт доставки через новый сервис
        $deliveryData = $this->deliveryCalculator->calculate($cartItems);
        $deliveryPrice = $deliveryData['total'] ?? 0;

        // Создаём заказ в OrangeData
        $cOrder = [
            'id' => $order->id . time(),
            'type' => 1,
            'customerContact' => $order->user->email,
            'taxationSystem' => 2,
            'key' => '7751222627_37957',
            'ffdVersion' => 2
        ];

        $response = $this->connector->send(new CreateOrderRequest($cOrder));
        $orangeOrderId = $response->json()['id'];

        // Добавляем позиции из заказа
        foreach ($order->items as $item) {
            $position = [
                'quantity' => $item->quantity,
                'price' => $item->price,
                'tax' => 6,
                'text' => $item->name,
                'paymentMethodType' => 1,
                'paymentSubjectType' => 1,
                'supplierInfo' => [
                    'phoneNumbers' => [$item->seller->phone ?? ''],
                    'name' => $item->seller->name ?? '',
                ],
                'supplierINN' => $item->seller->inn ?? '',
            ];

            $this->connector->send(new AddPositionRequest($orangeOrderId, $position));
        }

        // Добавляем доставку как позицию, если есть стоимость
        if ($deliveryPrice > 0) {
            $deliveryPosition = [
                'quantity' => '1',
                'price' => $deliveryPrice,
                'tax' => 6,
                'text' => 'Доставка',
                'paymentMethodType' => 1,
                'paymentSubjectType' => 4,
            ];
            $this->connector->send(new AddPositionRequest($orangeOrderId, $deliveryPosition));
        }

        // Добавляем платёж — итоговая сумма = товары + доставка
        $totalAmount = $order->getSellerTotalAmountAttribute() + $deliveryPrice;
        $payment = [
            'type' => 2,
            'amount' => $totalAmount,
        ];
        $this->connector->send(new AddPaymentRequest($orangeOrderId, $payment));

        // Отправляем заказ в OrangeData
        $result = $this->connector->send(new SendOrderRequest($orangeOrderId));

        return $result->json();
    }

    /**
     * Преобразует позиции заказа в формат для калькулятора доставки
     */
    private function prepareCartItemsForDelivery(array $orderItems): array
    {
        $cartItems = [];

        foreach ($orderItems as $item) {
            $cartItems[] = [
                'product_id' => $item->product_id,
                'seller_id' => $item->seller_id,
                'quantity' => $item->quantity,
                'weight' => $item->product->weight ?? 0,
                'pvz_id' => $item->seller->pvz_id ?? null,
            ];
        }

        return $cartItems;
    }
}
