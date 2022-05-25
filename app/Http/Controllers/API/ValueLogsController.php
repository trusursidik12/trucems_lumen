<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\SensorValue;
use Illuminate\Http\Request;

class ValueLogsController extends Controller
{
    public function index()
    {
        $sensorValues = SensorValue::with(['sensor:id,unit_id,code,name', 'sensor.unit:id,name'])
            ->orderBy("id", "desc")->get();
        return response()->json(['success' => true, 'data' => $sensorValues]);
    }

    public function update($sensorId, Request $request)
    {
        $sensorValue = SensorValue::where(["sensor_id" => $sensorId])->first();
        try {
            $column = $this->validate($request, [
                'value' => 'required'
            ], [
                "value.required" => "Value cant be empty!",
                // "value.numeric" => "Invalid data type, value must be numeric!"
            ]);

            $sensorValue->update($column);

            return response()->json(["success" => true, "message" => "Successfully update sensor value!"]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(["success" => false, "errors" => $e->response->original]);
        }
    }
}
