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

    protected $casts = [
        'selected_seats_json' => 'array',
    ];

    public function screening()
    {
        return $this->belongsTo(Screening::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    // public function seat()
    // {
    //     return $this->belongsTo(Seat::class);
    // }
    public function seats()
    {
        return $this->belongsToMany(HallSeat::class, 'reservation_seat', 'reservation_id', 'hall_seat_id')
            ->withPivot('screening_id')
            ->withTimestamps();
    }
    public function reservationSeats()
    {
        return $this->hasMany(ReservationSeat::class, 'reservation_id');
    }
}


