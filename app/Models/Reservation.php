<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = [
        'user_id',
        'screening_id',
        'seat_id',
        'reservation_code',
        'status',
        'reservation_time',
    ];

    public function screening()
    {
        return $this->belongsTo(Screening::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
