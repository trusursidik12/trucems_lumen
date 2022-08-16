<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sensor extends Model
{
    // use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'unit_id', 'code', 'name', 'unit_formula', 'read_formula', 'write_formula', 'quality_standard', 'analog_formula'
    ];

    public function Unit()
    {
        return $this->belongsTo(Unit::class);
    }
    public function SensorValues()
    {
        return $this->belongsTo(SensorValue::class);
    }
}
