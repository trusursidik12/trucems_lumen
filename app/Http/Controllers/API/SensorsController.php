<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Sensor;
use Illuminate\Http\Request;

class SensorsController extends Controller
{
    public function index()
    {
        $sensors = Sensor::get();
        return response()->json($sensors);
    }
}
