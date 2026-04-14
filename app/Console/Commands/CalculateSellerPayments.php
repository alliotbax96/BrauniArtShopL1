<?php

namespace App\Console\Commands;

use App\Models\Comission;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Seller;
use App\Models\SellerPayment;
use Illuminate\Console\Command;

class CalculateSellerPayments extends Command
{
    protected $signature = 'payments:calculate';
    protected $description = 'Рассчитывает выплаты продавцам по завершённым заказам';

    public function handle(): void
    {
        // Получаем ставки из .env
        $acquiringCommission = (float) env('ACQUIRING_COMMISSION', 2.5); // %
        $logisticsPerKg = (float) env('LOGISTICS_PER_KG', 50); // руб/кг

        // Получаем всех продавцов
        $sellers = Seller::all();

        foreach ($sellers as $seller) {
            // Получаем заказы с позициями этого продавца, где статус продавца — нужный
            $orders = Order::whereHas('items', function ($query) use ($seller) {
                $query->where('seller_id', $seller->id);
            })->whereHas('sellerStatuses', function ($query) {
                $query->whereIn('status', ['OnTheWay', 'ReadyForIssue', 'finished']);
            })->get();

            foreach ($orders as $order) {
                // Считаем сумму выплат для этого продавца по этому заказу
                $totalPayment = 0;
                $orderItems = $order->items()->where('seller_id', $seller->id)->get();

                foreach ($orderItems as $item) {
                    // Получаем процент комиссии группы товара
                    $productGroupCommissionPercent = Comission::where('product_group_id', $item->product->productGroupId)
                        ->value('comission') ?? 0;

                    // Расчёт комиссий в рублях
                    $productGroupCommission = ($item->price * $productGroupCommissionPercent) / 100;
                    $acquiringFee = ($item->price * $acquiringCommission) / 100;
                    $logisticsFee = $item->product->productWeight * $logisticsPerKg;

                    // Общая комиссия на единицу
                    $totalCommissionPerUnit = $productGroupCommission + $acquiringFee + $logisticsFee;

                    // Сумма выплаты за единицу
                    $paymentPerUnit = max(0, $item->price - $totalCommissionPerUnit);

                    // Общая сумма за позицию
                    $positionPayment = $paymentPerUnit * $item->quantity;
                    $totalPayment += $positionPayment;
                }


                // Проверяем, нет ли уже записи о выплате
                $existingPayment = SellerPayment::where('order_id', $order->id)
                    ->where('seller_id', $seller->id)
                    ->first();

                if (!$existingPayment && $totalPayment > 0) {
                    SellerPayment::firstOrCreate(
                        [
                            'order_id' => $order->id,
                            'seller_id' => $seller->id,
                        ],
                        [
                            'amount' => $totalPayment,
                            'paid' => false,
                        ]
                    );

                    $this->info("Создана выплата для продавца {$seller->name} по заказу {$order->id}: {$totalPayment} руб.");
                } elseif ($existingPayment) {
                    $this->warn("Выплата для продавца {$seller->name} по заказу {$order->id} уже существует.");
                }
            }
        }

        $this->info('Расчёт выплат завершён.');
    }
}
