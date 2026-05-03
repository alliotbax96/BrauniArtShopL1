<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $table = 'bookings';
    protected $fillable = [
        'quest_id',
        'timeslot_id',
        'date',
        'total_price',
        'selected_services',
        'player_count',
        'user_id',
        'customer_name',
        'customer_phone'
    ];

    public function timeslot()
    {
        return $this->hasOne(Timeslot::class, 'id', 'timeslot_id');
    }

    public function quest()
    {
        return $this->hasOne(Quest::class, 'id', 'quest_id');
    }


    public function getSelectedServicesAttribute()
    {
        $serviceIds = json_decode($this->attributes['selected_services'], true);

        if (empty($serviceIds)) {
            return collect();
        }

        return AdditionalService::whereIn('id', $serviceIds)->get();
    }
}
