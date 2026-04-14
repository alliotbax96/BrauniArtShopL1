<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SellerPvz extends Model
{
    protected $table = 'seller_pvzs';

    protected $fillable = [
        'seller_id',
        'pvz',
        'pvz_name',
        'last'
    ];

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }
}
