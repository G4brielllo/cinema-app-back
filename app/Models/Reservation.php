<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = [
        'user_id',
        'screening_id',
        'reservation_code',
        'payu_order_id',
        'status',
        'total_amount',
        'reservation_time',
        'selected_seats_json',
    ];

    public function screening()
    {
        return $this->belongsTo(Screening::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function seat()
    {
        return $this->belongsTo(Seat::class);
    }
    public function seats()
    {
        return $this->belongsToMany(Seat::class, 'reservation_seat', 'reservation_id', 'seat_id')->withTimestamps();
    }
    public function reservationSeats()
    {
        return $this->hasMany(ReservationSeat::class, 'reservation_id');
    }
}


