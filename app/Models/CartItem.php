<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    protected $fillable = [
        'cart_id',
        'product_id',
        'product_type',
        'seller_id',
        'quantity',
        'options'
    ];

    protected $casts = [
        'options' => 'array'
    ];

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

    public function getProduct()
    {
        $model = app($this->product_type);
        return $model->find($this->product_id);
    }
}
