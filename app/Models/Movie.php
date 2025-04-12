<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    protected $fillable = [
        'title',
        'description',
        'category',
        'show_time',
        'duration',
        'release_date',
        'image',
        'direction',
        'script',
        'production_year',
        'cast',
    ];
}
