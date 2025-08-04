<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;


class HallSeat extends Model
{
    protected $fillable = ['hall_id', 'row', 'number', 'x', 'y'];

    public function hall()
    {
        return $this->belongsTo(Hall::class);
    }
    public function reservations()
    {
        return $this->hasMany(ReservationSeat::class, 'hall_seat_id');
    }
}
