<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdditionalService extends Model
{
    protected $table = 'additional_services';
    protected $fillable = [
        'quest_id',
        'name',
        'price'
    ];

    public function quest()
    {
        return $this->hasOne(Quest::class, 'id', 'quest_id');
    }
}
