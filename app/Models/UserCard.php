<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCard extends Model
{
    protected $table = 'UserCards';
    protected $fillable = [
      'RebildID',
        'CardID',
    ];
}
