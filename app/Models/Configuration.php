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
        'is_calibration_history',
        'is_calibration',
        'is_relay_open',
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
        'loop_count',
        'a_start_calibration_at',
        'm_start_calibration_at',
        'a_end_calibration_at',
        'm_end_calibration_at',
        'date_and_time',
    ];
}
