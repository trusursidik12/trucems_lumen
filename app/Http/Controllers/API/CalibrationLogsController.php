<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CalibrationAvgLog;
use App\Models\CalibrationLog;
use App\Models\Configuration;
use Illuminate\Http\Request;

class CalibrationLogsController extends Controller
{
    public function index()
    {
        $limit = request()->has('limit') ? request()->limit : 30;
        $calibrationLogs = CalibrationLog::with(['sensor:id,unit_id,code,name', 'sensor.unit:id,name'])
            ->orderBy("id", "desc")->limit($limit)->get();
        return response()->json(['success' => true, 'data' => $calibrationLogs]);
    }

    public function store(Request $request)
    {
        try {
            $config = Configuration::find(1);
            $column = $this->validate($request, [
                // 'sensor_id' => 'required|numeric',
                'value' => 'required|numeric',
            ], [
                // "sensor_id.required" => "Sensor cant be empty!",
                "value.required" => "Value cant be empty!",
                // "sensor_id.numeric" => "Invalid data type sensor_id!",
                "value.numeric" => "Value must be numeric format!"
            ]);
            $column['sensor_id'] = 1;
            $column['calibration_type'] = $config->calibration_type;
            CalibrationLog::create($column);

            return response()->json(["success" => true, "message" => "Successfully insert calibration logs!"]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(["success" => false, "errors" => $e->response->original]);
        }
    }

    public function destroy()
    {
        $calibrationLogs = CalibrationLog::first();
        $sum = CalibrationLog::sum("value");
        $rowCount = CalibrationLog::get()->count();
        if ($sum == 0 && $rowCount == 0) {
            return response()->json([
                'success' => false,
                'message' => 'Successfully averaging calibration'
            ]);
        }
        $avg = ($sum / $rowCount);
        CalibrationAvgLog::create([
            'sensor_id' => $calibrationLogs->sensor_id,
            'row_count' => $rowCount,
            'value' => $avg,
            'calibration_type' => $calibrationLogs->calibration_type,
        ]);
        $config = Configuration::find(1);
        $config->update([
            'is_calibration' => 0,
            'calibration_type' => 0,
        ]);
        //
        CalibrationLog::truncate();
        return response()->json([
            'success' => true,
            'message' => 'Successfully averaging calibration'
        ]);
    }
}
