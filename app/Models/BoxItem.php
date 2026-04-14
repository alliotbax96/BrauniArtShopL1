<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoxItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'box_id',
        'product_id',
        'quantity',
        'order_item_id',
    ];

    public function box()
    {
        return $this->belongsTo(OrderBox::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class, 'order_item_id');
    }
}
