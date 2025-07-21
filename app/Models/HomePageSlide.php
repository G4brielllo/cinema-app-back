<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomePageSlide extends Model
{
    protected $table = 'homepage_slides';
    protected $fillable = [
        'title',
        'image_url',
        'trailer_url',
    ];
}
