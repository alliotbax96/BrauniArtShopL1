<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comission extends Model
{
    protected $table = 'comission';
    protected $fillable = [
        'id',
        'comission',
        'product_group_id',
    ];
}
