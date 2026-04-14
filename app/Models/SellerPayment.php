<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellerPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'seller_id',
        'amount',
        'paid',
        'paid_at',
    ];

    protected $casts = [
        'paid' => 'boolean',
        'paid_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }
}
