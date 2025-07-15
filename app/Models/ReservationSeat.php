<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ReservationSeat extends Pivot
{
    protected $table = 'reservation_seat';
    protected $fillable = ['reservation_id', 'seat_id', 'created_at', 'updated_at'];
    public $timestamps = true;

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }
    public function seat()
    {
        return $this->belongsTo(Seat::class);
    }
}