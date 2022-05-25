<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CalibrationAvgLog extends Model
{
    // use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sensor_id', 'value' , 'row_count', 'calibration_type'
    ];

    public function Sensor(){
        return $this->belongsTo(Sensor::class);
    }
}
