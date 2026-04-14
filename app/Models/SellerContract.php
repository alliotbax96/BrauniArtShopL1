<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SellerContract extends Model
{
    protected $table = 'seller_contract';
    protected $fillable = [
        'id',
        'seller_id',
        'status',
        'signed_status',
        'seller_legal_details_id'
    ];

    public function seller(){
        return $this->belongsTo(Seller::class, 'seller_id');
    }

}
