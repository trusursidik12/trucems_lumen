<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CalibrationAvgLog;
use App\Models\CalibrationLog;
use App\Models\Configuration;
use Exception;
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

    public function getLast()
    {
        try {
            $calibrationLogs = CalibrationLog::orderBy("id", "desc")->first();
            return response()->json(['success' => true, 'data' => $calibrationLogs]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
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
        $config = Configuration::find(1);
        $calibrationLogs = CalibrationLog::first();
        $sum = CalibrationLog::sum("value");
        $rowCount = CalibrationLog::get()->count();
        if ($sum == 0 && $rowCount == 0) {
            return response()->json([
                'success' => false,
                'message' => 'You have to calibrate first'
            ]);
        }
        $avg = ($sum / $rowCount);
        CalibrationAvgLog::create([
            'sensor_id' => $calibrationLogs->sensor_id,
            'row_count' => $rowCount,
            'cal_gas_ppm' => ($calibrationLogs->calibration_type == 2 ? $config->m_max_span_ppm : 0),
            'cal_duration' => ($calibrationLogs->calibration_type == 2 ? $config->m_time_span_loop : $config->m_time_zero_loop),
            'value' => round($avg, 3),
            'calibration_type' => $calibrationLogs->calibration_type,
        ]);
        CalibrationLog::truncate();
        return response()->json([
            'success' => true,
            'message' => 'Successfully averaging calibration'
        ]);
    }
}
