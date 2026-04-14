<?php

namespace App\Console\Commands;

use App\Http\Integrations\YandexDelivery\Requests\OrderInfoRequest;
use App\Http\Integrations\YandexDelivery\YandexDeliveryConnector;
use App\Models\Order;
use Illuminate\Console\Command;
use Saloon\Exceptions\RequestException;

class UpdateOrderStatusFromYandexDelivery extends Command
{
    protected $signature = 'order:update-status-from-yandex-delivery';
    protected $description = 'Update order statuses from Yandex Delivery API';

    public function handle(): int
    {
        // Получаем все заказы, у которых есть delivery_id (т. е. подключена доставка)
        $orders = Order::whereHas('items.OrderItemDeliveryMethod')->get();

        $updatedCount = 0;

        foreach ($orders as $order) {
            if ($this->updateOrderStatus($order)) {
                $updatedCount++;
            }
        }

        $this->info("Order statuses updated successfully. Updated {$updatedCount} orders.");
        return Command::SUCCESS;
    }

    private function updateOrderStatus(Order $order): bool
    {
        try {
            // Получаем delivery_id для первого элемента заказа
            $deliveryId = $order->items->first()?->OrderItemDeliveryMethod?->delivery_id;

            if (!$deliveryId) {
                $this->warn("Order {$order->id} has no delivery_id");
                return false;
            }

            // Отправляем запрос к API Yandex Delivery
            $connector = new YandexDeliveryConnector();
            $request = new OrderInfoRequest($deliveryId);
            $response = $connector->send($request);

            if (!$response->json()['request_id']) {
                $this->error("Failed to get delivery info for order {$order->id}");
                return false;
            }

            $data = $response->json();

            // Извлекаем статус из ответа API
            $apiStatus = $data['state']['status'] ?? null;
            $apiStatus = 'SORTING_CENTER_AT_START';
            print_r($apiStatus);

            if (!$apiStatus) {
                $this->warn("No status found in API response for order {$order->id}");
                return false;
            }

            // Определяем новый статус продавца на основе статуса API
            $newSellerStatus = $this->mapApiStatusToSellerStatus($apiStatus);

            if ($newSellerStatus) {
                // Обновляем статусы для всех продавцов в заказе
                foreach ($order->sellers as $seller) {
                    $order->setSellerStatus($seller->id, $newSellerStatus);
                }

                $this->info("Updated status for order {$order->id}: {$apiStatus} -> {$newSellerStatus}");
                return true;
            }
        } catch (RequestException $e) {
            $this->error("API request failed for order {$order->id}: " . $e->getMessage());
        } catch (\Exception $e) {
            $this->error("Error processing order {$order->id}: " . $e->getMessage());
        }

        return false;
    }

    private function mapApiStatusToSellerStatus(string $apiStatus): ?string
    {
        $mapping = [
            'SORTING_CENTER_AT_START' => 'OnTheWay',
            'DELIVERY_ARRIVED_PICKUP_POINT' => 'ReadyForIssue',
            'DELIVERY_DELIVERED' => 'finished',
        ];

        // Возвращаем статус только если есть соответствие, иначе null (изменение не требуется)
        return $mapping[$apiStatus] ?? null;
    }
}
