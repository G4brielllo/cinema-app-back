<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seat extends Model
{
    protected $fillable = [
        'screening_id',
        'row',
        'number',
        'is_booked',
    ];

    public function hall()
    {
        return $this->belongsTo(Hall::class);
    }
    public function reservations(){
        return $this->belongsToMany(Reservation::class, 'reservation_seat');
    }
}
