<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;


class ReservationSeat extends Model
{
    protected $table = 'reservation_seat';
    protected $fillable = ['reservation_id', 'hall_seat_id', 'screening_id', 'created_at', 'updated_at'];
    public $timestamps = true;

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }
    public function hallSeat()
    {
        return $this->belongsTo(HallSeat::class);
    }
}