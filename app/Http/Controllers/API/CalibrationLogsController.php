<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CalibrationLog;
use Illuminate\Http\Request;

class CalibrationLogsController extends Controller
{
    public function index(){
        $limit = request()->has('limit') ? request()->limit : 30;
        $calibrationLogs = CalibrationLog::with(['sensor:id,unit_id,code,name','sensor.unit:id,name'])
        ->orderBy("id","desc")->limit($limit)->get();
        return response()->json(['success' => true, 'data' => $calibrationLogs]);
    }

    public function store(Request $request){
        try {
            $column = $this->validate($request,[
                'sensor_id' => 'required|numeric',
                'value' => 'required|numeric',
            ],[
                "sensor_id.required" => "Sensor cant be empty!",
                "value.required" => "Value cant be empty!",
                "sensor_id.numeric" => "Invalid data type sensor_id!",
                "value.numeric" => "Value must be numeric format!"
            ]);
            CalibrationLog::create($column);

            return response()->json(["success" => true, "message" => "Successfully insert calibration logs!"]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(["success" => false, "errors" => $e->response->original]);
        }
    }

    public function destroy(){
        
    }

}
