<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderBox extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'box_number',
        'length',
        'height',
        'width',
        'volume',
        'weight',
        'notes',
        'seller_id'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function items()
    {
        return $this->hasMany(BoxItem::class, 'box_id');
    }
}
