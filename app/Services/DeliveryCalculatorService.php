<?php

namespace App\Services;

use App\Http\Integrations\YandexDelivery\Requests\PricingCalculatorRequest;
use App\Http\Integrations\YandexDelivery\YandexDeliveryConnector;
use App\Models\SellerPvz;
use App\Models\UserPvz;
use Illuminate\Support\Facades\Auth;
use RuntimeException;
use InvalidArgumentException;

class DeliveryCalculatorService
{
    private function calculateForSeller(array $items, int $sellerId): float
    {
        // Получаем ПВЗ
        $pvz = SellerPvz::where('seller_id', $sellerId)->firstOrFail()->pvz;
        $userPvz = UserPvz::where('user_id', Auth::id())->firstOrFail()->pvz;

        // Рассчитываем общий вес с обработкой запятых в десятичных дробях
        $totalWeight = 0.0;
        foreach ($items as $item) {
            $weight = (float)str_replace(',', '.', $item['weight'] ?? 0);
            $quantity = $item['quantity'] ?? 0;

            // Проверка на корректность данных
            if (!is_numeric($weight) || !is_numeric($quantity) || $weight < 0 || $quantity < 0) {
                throw new InvalidArgumentException(
                    "Некорректные данные веса или количества для товара: вес={$weight}, количество={$quantity}"
                );
            }

            $totalWeight += $weight * $quantity;
        }

        // Ограничение калькулятора — 25 кг за один расчёт
        $maxWeightPerCalculation = 25.0;
        $connector = new YandexDeliveryConnector();
        $totalCost = 0.0;

        // Если общий вес превышает лимит, разбиваем на партии по 25 кг
        if ($totalWeight > $maxWeightPerCalculation) {
            $fullBatches = (int)floor($totalWeight / $maxWeightPerCalculation);
            $remainingWeight = fmod($totalWeight, $maxWeightPerCalculation);

            // Рассчитываем стоимость для полных партий по 25 кг
            for ($i = 0; $i < $fullBatches; $i++) {
                $totalCost += $this->calculateDeliveryCost($connector, $pvz, $userPvz, $maxWeightPerCalculation);
            }

            // Если остался остаток веса, рассчитываем для него отдельную партию
            if ($remainingWeight > 0) {
                $totalCost += $this->calculateDeliveryCost($connector, $pvz, $userPvz, $remainingWeight);
            }
        } else {
            // Общий вес не превышает лимит — один расчёт
            $totalCost = $this->calculateDeliveryCost($connector, $pvz, $userPvz, $totalWeight);
        }

        return round($totalCost, 2);
    }

    /**
     * Вспомогательный метод для расчёта стоимости доставки для заданного веса
     */
    private function calculateDeliveryCost(
        YandexDeliveryConnector $connector,
        string $pvz,
        string $userPvz,
        float $weight
    ): float {
        $request = new PricingCalculatorRequest($pvz, $userPvz, $weight);
        $result = $connector->send($request)->json();
        \Log::info($weight);
        \Log::info($result);
        if (!isset($result['pricing_total'])) {
            throw new RuntimeException('Ответ API не содержит поля pricing_total');
        }

        $cost = (float)preg_replace('/[^0-9.]/', '', $result['pricing_total']);

        if ($cost < 0) {
            throw new RuntimeException('Получена отрицательная стоимость доставки');
        }

        return $cost;
    }


    /**
     * Основной метод расчёта доставки
     *
     * @param array $cartItems Позиции корзины с информацией о продавце, весе и количестве
     * @return array ['total' => сумма, 'breakdown' => детализация по продавцам]
     */
    public function calculate($cartItems): array
    {
        // Группируем позиции по продавцу
        $groupsBySeller = [];
        foreach ($cartItems as $item) {
            $sellerId = $item->seller_id;
            $product = $item->getProduct();
            $weight = $product->productWeight ?? 0; // предполагаем, что у товара есть поле weight

            $groupsBySeller[$sellerId][] = [
                'weight' => (float)$weight,
                'quantity' => $item->quantity
            ];
        }

        $totalCost = 0;
        $breakdown = [];

        foreach ($groupsBySeller as $sellerId => $items) {
            $cost = $this->calculateForSeller($items, $sellerId);
            $totalCost += $cost;

            $breakdown[] = [
                'seller_id' => $sellerId,
                'delivery_cost' => $cost,
                'items_count' => count($items),
                'total_weight' => array_sum(array_column($items, 'weight'))
            ];
        }

        return [
            'total' => $totalCost,
            'breakdown' => $breakdown
        ];
    }
}
