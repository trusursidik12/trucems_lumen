<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Runtime extends Model
{
    // use HasFactory;

    protected $table = 'runtime';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'days', 'hours', 'minutes'
    ];

    public function Unit(){
        return $this->belongsTo(Unit::class);
    }
}
