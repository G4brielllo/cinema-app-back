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
        'cast',
        'age_group',
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
