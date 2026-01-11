<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandPlots extends Model
{
    //
    protected $fillable = [
        'plot_name',
        'data_location',
        'data_content',
        'data_address',
        'user_id',
        'created_at',
        'modified_at'
    ];
}
