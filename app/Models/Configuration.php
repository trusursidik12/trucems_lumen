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
        'name',
        'default_zero_loop',
        'time_zero_loop',
        'default_span_loop',
        'time_span_loop',
        'max_span_ppm',
        'date_and_time',
        'schedule_auto_calibration',
        'is_calibration',
    ];
}
