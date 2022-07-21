<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuration extends Model
{
    // use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'is_blowback',
        'is_calibration',
        'calibration_type',
        'sensor_id',
        'target_value',
        'date_and_time',
    ];
}
