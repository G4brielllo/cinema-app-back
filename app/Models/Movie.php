<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    protected $fillable = [
        'title',
        'description',
        'category',
        'playing_from',
        'playing_until',
        'duration',
        'image',
        'trailer',
        'direction',
        'script',
        'production_year',
        'cast',
        'status',
    ];
    protected $attributes = [
        'status' => 'movie',
    ];
    public function screenings()
    {
        return $this->hasMany(Screening::class);
    }

}
