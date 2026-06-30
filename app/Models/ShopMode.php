<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopMode extends Model
{
    protected $table = 'ShopMode';
    protected $fillable = [
        'id',
        'ShopModeName',
        'Model',
        'status'
    ];
}
