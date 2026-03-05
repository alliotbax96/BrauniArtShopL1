<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductQuantity extends Model
{
    protected $fillable = ['product_id', 'product_type', 'warehouse_id', 'quantity'];

    public function scopeForProduct($query, $productId, $productType)
    {
        return $query->where('product_id', $productId)
            ->where('product_type', $productType);
    }
}
