<?php

namespace Database\Seeders;

use App\Models\OrderStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderStatuses extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        OrderStatus::create([
            'name' => 'Не оплачен',
            'slug' => 'pending',
            'color' => 'danger'
        ]);

        OrderStatus::create([
            'name'=>'Новый',
            'slug'=>'paid',
            'color'=>'warning'
        ]);

        OrderStatus::create([
            'name'=>'В обработке',
            'slug'=>'processing',
            'color'=>'info'
        ]);

        OrderStatus::create([
            'name'=>'В сборке',
            'slug'=>'assembly',
            'color'=>'info'
        ]);

        OrderStatus::create([
            'name'=>'Передается в доставку',
            'slug'=>'SentForDelivery',
            'color'=>'secondary'
        ]);

        OrderStatus::create([
            'name'=>'В пути',
            'slug'=>'OnTheWay',
            'color'=>'success'
        ]);

        OrderStatus::create([
            'name'=>'Готов к выдаче',
            'slug'=>'ReadyForIssue',
            'color'=>'success'
        ]);

        OrderStatus::create([
            'name'=>'Получен',
            'slug'=>'SentForDelivery',
            'color'=>'secondary'
        ]);

        OrderStatus::create([
            'name'=>'Отменен',
            'slug'=>'canceled',
            'color'=>'danger'
        ]);
    }
}
