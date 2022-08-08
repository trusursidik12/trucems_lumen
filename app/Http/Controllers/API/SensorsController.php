<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Sensor;
use App\Models\SensorValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SensorsController extends Controller
{
    public function index()
    {
        $sensors = Sensor::get();
        return response()->json($sensors);
    }
    public function getById($sensorId)
    {
        $values = SensorValue::with('sensor:id,code')->where('sensor_id', $sensorId)->first();
        return response()->json($values);
    }
    public function getNOx()
    {
        $values = SensorValue::whereIn('sensor_id', [1,3])->sum("value");
        return response()->json(["nox" => $values]);
    }
    
}
