<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderSellerStatus extends Model
{
    protected $fillable = [
        'order_id',
        'seller_id',
        'status'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    public function statusInfo() {
        return $this->belongsTo(OrderStatus::class, 'status', 'slug');
    }
}
