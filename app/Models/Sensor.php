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
        'unit_id', 'code', 'name' , 'formula'
    ];

    public function Unit(){
        return $this->belongsTo(Unit::class);
    }
}
