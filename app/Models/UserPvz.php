<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPvz extends Model
{
    protected $table = 'UserPvz';

    protected $fillable = [
        'user_id',
        'pvz',
        'pvz_name',
        'last'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
