<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\SensorValue;
use Exception;
use Illuminate\Http\Request;

class ValueLogsController extends Controller
{
    public function index(Request $request)
    {
        $sensorValues = SensorValue::with(['sensor:id,unit_id,code,name,quality_standard,unit_formula', 'sensor.unit:id,name'])
            ->orderBy("id", "asc")->get();
        // if(!empty(@$request->unit) && @$request->unit=="mg/m3"){
        //     foreach ($sensorValues as $key => $value) {
        //         try{
        //             $sensorValues[$key]->value = eval($value->sensor->unit_formula);
        //         }catch(Exception $e){
        //             if(env("APP_ENV") == "local"){
        //                 trigger_error("Invalid formula : ". $e->getMessage());
        //             }
        //         }
        //     }
        // }
        
        return response()->json(['success' => true, 'data' => $sensorValues]);
    }

    public function update($sensorId, Request $request)
    {
        $sensorValue = SensorValue::where(["sensor_id" => $sensorId])->first();
        try {
            $column = $this->validate($request, [
                'value' => 'required|numeric'
            ], [
                "value.required" => "Value cant be empty!",
                "value.numeric" => "Invalid data type, value must be numeric!"
            ]);

            $sensorValue->update($column);

            return response()->json(["success" => true, "message" => "Successfully update sensor value!"]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(["success" => false, "errors" => $e->response->original]);
        }
    }
}
