<?php

namespace App\Models;

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
        'sensor_id', 'value', 'calibration_type'
    ];

    public function Sensor(){
        return $this->belongsTo(Sensor::class);
    }
}
