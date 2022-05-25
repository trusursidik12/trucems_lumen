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
        'schedule_auto_calibration',
        'is_calibration',
        'calibration_type',
        'a_default_zero_loop',
        'a_default_span_loop',
        'a_time_zero_loop',
        'a_time_span_loop',
        'a_max_span_ppm',
        'm_default_zero_loop',
        'm_default_span_loop',
        'm_time_zero_loop',
        'm_time_span_loop',
        'm_max_span_ppm',
        'a_start_calibration_at',
        'm_start_calibration_at',
        'date_and_time',
    ];
}
