<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Screening extends Model
{
    protected $fillable = [
        'movie_id',
        'format',
        'audio_type',
        'screening_date',
        'screening_time',
        'hall_id',
        'status',
    ];

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }
     public function hall()
    {
        return $this->belongsTo(Hall::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

}