<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItemDeliveryMethod extends Model
{
    protected $table = 'order_item_delivery_info';
    protected $fillable = [
        'order_item_id',
        'delivery_id',
    ];

    public function orderItem(): BelongsTo {
        return $this->belongsTo(OrderItem::class, 'order_item_id', 'id');
    }
}
