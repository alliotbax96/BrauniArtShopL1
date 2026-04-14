<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seller extends Model
{
    protected $table = 'sellers';

    protected $fillable = [
        'name',
        'phone',
    ];

    // Связь с пользователями
    public function users(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class, 'seller_user')
            ->withTimestamps();
    }

    // Связь с реквизитами
    public function legalDetails(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(LegalEntityDetail::class, 'seller_legal_details', 'seller_id', 'legal_entity_detail_id')->withTimestamps()->limit(1);
    }

    public function legalDetail() {
        return $this->legalDetails()->first();
    }

    public function SellerPvz()
    {
        return $this->hasOne(SellerPvz::class);
    }

    public function contacts()
    {
        return $this->HasMany(SellerContract::class,'seller_id', 'id');
    }
}
