<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Screening extends Model
{
    protected $fillable = [
        'movie_id',
        'screening_date',
        'screening_time',
        'hall_id',
    ];

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }

}