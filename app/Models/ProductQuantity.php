<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductQuantity extends Model
{
    protected $table = 'product_quantities';
    protected $fillable = ['product_id', 'product_type', 'warehouse_id', 'quantity'];

    public function Warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id', 'id');
    }

    public function scopeForProduct($query, $productId, $productType)
    {
        return $query->where('product_id', $productId)
            ->where('product_type', $productType);
    }

    public function getWarehouse(): Warehouse
    {
        return $this->Warehouse;
    }

}
