<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalibrationAvgLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sensor_id', 'value' , 'row_count', 'calibration_type', 'cal_gas_ppm', 'cal_duration'
    ];

    public function Sensor(){
        return $this->belongsTo(Sensor::class);
    }

    public function getCreatedAtAttribute($value){
        return Carbon::parse($value)->timezone('Asia/Jakarta')->format("l, d F Y H:i:s");
    }
}
