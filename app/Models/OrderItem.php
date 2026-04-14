<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price',
        'name',
        'seller_id',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function seller() {
        return $this->belongsTo(Seller::class);
    }

    public function OrderItemDeliveryMethod(){
        return $this->HasOne(OrderItemDeliveryMethod::class, 'order_item_id');
    }
}
