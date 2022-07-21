<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plc extends Model
{
    // use HasFactory;
    protected $table = "plcs";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'is_calibration', 'is_maintenance', 'is_cga', 'd_off', 'd0', 'd1', 'd2', 'd3', 'd4', 'd5', 'd6', 'd7', 'sleep_sampling', 'sleep_blowback', 'loop_sampling', 'loop_blowback', 'alarm'
    ];
}
