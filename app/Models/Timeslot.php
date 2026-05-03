<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Timeslot extends Model
{
    protected $table = 'timeslots';
    protected $fillable = [
        'id',
        'quest_id',
        'day_of_week',
        'start_time',
        'price'
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function quest()
    {
        return $this->hasOne(Quest::class);
    }
}
