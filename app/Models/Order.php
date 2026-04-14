<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'payment_method',
        'status',
        'selected_items',
        'PickUpPoint',
    ];

    protected $casts = [
        'selected_items' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusInfo(){
        return $this->belongsTo(OrderStatus::class, 'status', 'slug');
    }

    public function PickUpPointInfo(){
        return $this->belongsTo(UserPvz::class, 'PickUpPoint', 'id');
    }

    public function getSellerTotalAmountAttribute()
    {
        return $this->items->sum(function ($item) {
            return $item->quantity * $item->price;
        });
    }

    public function sellerStatuses()
    {
        return $this->hasMany(OrderSellerStatus::class);
    }

    public function sellers()
    {
        return $this->belongsToMany(Seller::class, 'order_seller_statuses');
    }

    /**
     * Получить статус для конкретного продавца
     */
    public function getSellerStatus($sellerId)
    {
        return $this->sellerStatuses()
            ->where('seller_id', $sellerId)
            ->first();
    }

    /**
     * Установить статус для продавца и обновить основной статус заказа
     */
    public function setSellerStatus($sellerId, $status)
    {
        // Обновляем статус для конкретного продавца
        $sellerStatus = $this->getSellerStatus($sellerId);

        if ($sellerStatus) {
            $sellerStatus->update(['status' => $status]);
        } else {
            // Создаём запись, если её нет
            $this->sellerStatuses()->create([
                'seller_id' => $sellerId,
                'status' => $status
            ]);
        }

        // Обновляем основной статус заказа на основе статусов всех продавцов
        $this->updateMainStatus();
    }

    /**
     * Обновить основной статус заказа на основе статусов продавцов
     */
    public function updateMainStatus()
    {
        $sellerStatuses = $this->sellerStatuses;

        if ($sellerStatuses->isEmpty()) {
            return;
        }

        $allStatuses = $sellerStatuses->pluck('status')->toArray();
        $uniqueStatuses = array_unique($allStatuses);

        // Если все продавцы имеют одинаковый статус — ставим его как основной
        if (count($uniqueStatuses) === 1) {
            $this->update(['status' => reset($uniqueStatuses)]);
        } else {
            // Логика определения основного статуса при разных статусах
            // Приоритет: assembly > paid > pending
            $priority = ['assembly' => 3, 'paid' => 2, 'pending' => 1];
            $maxPriority = 0;
            $mainStatus = 'pending';

            foreach ($uniqueStatuses as $status) {
                if (isset($priority[$status]) && $priority[$status] > $maxPriority) {
                    $maxPriority = $priority[$status];
                    $mainStatus = $status;
                }
            }

            $this->update(['status' => $mainStatus]);
        }
    }
}
