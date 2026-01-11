<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TiePoints extends Model
{
    //
    protected $fillable = [
        'location',
        'city_municipality',
        'point_of_reference',
        'latitude',
        'longitude'
    ];
}
