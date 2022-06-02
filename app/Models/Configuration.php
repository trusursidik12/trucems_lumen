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
        'is_calibration_history',
        'is_calibration',
        'calibration_type',
        'is_relay_open',
        'blowback_duration',
        'm_default_zero_loop',
        'm_default_span_loop',
        'm_time_zero_loop',
        'm_time_span_loop',
        'm_max_span_ppm',
        'loop_count',
        'm_start_calibration_at',
        'm_end_calibration_at',
        'start_blowback_at',
        'end_blowback_at',
        'date_and_time',
    ];
}
