<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class CalibrationLog extends Model
{
    // use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sensor_id', 'calibration_type', 'start_value', 'target_value', 'result_value'
    ];

    public function Sensor()
    {
        return $this->belongsTo(Sensor::class);
    }
    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->timezone(env('APP_TIMEZONE'))->format("H:i:s");
    }
}
