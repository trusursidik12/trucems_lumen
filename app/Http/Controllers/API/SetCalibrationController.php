<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CalibrationAvgLog;
use App\Models\CalibrationLog;
use App\Models\Configuration;
use App\Models\SensorValue;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SetCalibrationController extends Controller
{
    public function setManualCal($type, Request $request)
    {
        try {
            $column = $this->validate($request, [
                'm_default_zero_loop' => 'required|numeric',
                'm_time_zero_loop' => 'required|numeric',
                'm_default_span_loop' => 'required|numeric',
                'm_time_span_loop' => 'required|numeric',
                'm_max_span_ppm' => 'required|numeric',
            ], [
                'm_default_zero_loop.required' => 'Default Zero Loop cant empty!',
                'm_time_zero_loop.required' => 'Time Zero Loop cant empty!',
                'm_default_span_loop.required' => 'Span Zero Loop cant empty!',
                'm_time_span_loop.required' => 'Time Span Loop cant empty!',
                'm_max_span_ppm.required' => 'Max PPM cant empty!',
                'm_default_zero_loop.numeric' => 'Default Zero Loop must be numeric!',
                'm_time_zero_loop.numeric' => 'Time Zero Loop must be numeric!',
                'm_default_span_loop.numeric' => 'Span Zero Loop must be numeric!',
                'm_time_span_loop.numeric' => 'Time Span Loop must be numeric!',
                'm_max_span_ppm.numeric' => 'Max PPM must be numeric!',
            ]);
            $now = Carbon::now('Asia/Jakarta');
            $endAt = $now->addSeconds(($type == "span" ? $column['m_time_span_loop'] : $column['m_time_zero_loop']));
            $endAt = $endAt->addSeconds(1);
            $endAt = $endAt->format("Y-m-d H:i:s");
            $column['is_calibration'] = 2; // 2 = Manual Cal
            $column['is_calibration_history'] = 2; // 2 = Manual Cal
            $column['calibration_type'] = ($type == "span" ? 2 : ($type == "zero" ? 1 : 0));
            $column['m_start_calibration_at'] = $now->format("Y-m-d H:i:s");
            $column['m_end_calibration_at'] = $endAt;
            $configuration = Configuration::find(1);
            $configuration->update($column);
            return response()->json(["success" => true, "message" => "Successfully update!"]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(["success" => false, "errors" => $e->response->original]);
        }
    }

    public function checkRemaining($mode, $type)
    {
        $config = Configuration::find(1);
        switch ($mode) {
            case 'manual':
                $endAt = $config->m_end_calibration_at;
                break;

            case 'auto':
            default:
                $endAt = $config->a_end_calibration_at;
                break;
        }
        // if(empty($startAt)){
        //     return response()->json(['success' => false, 'message' => 'Calibration is not started!']);
        // }
        $now = Carbon::now();
        $endAt = Carbon::parse($endAt);
        $diff = $now->diffInSeconds($endAt, false);
        if ($diff <= 0) {
            $config->update(['is_calibration' => 3]);
        }
        $sensorValues = SensorValue::with(['sensor:id,unit_id,code,name', 'sensor.unit:id,name'])
            ->orderBy("id", "desc")->get();
        $calibrationLogs = CalibrationLog::with(['sensor:id,unit_id,code,name', 'sensor.unit:id,name'])
            ->withCasts(["created_at" => "datetime:H:i:s"])
            ->limit(3)->orderBy("id", "desc")->get();
        // $lastCalibrationAvg = CalibrationAvgLog::orderBy("id","desc")->first();
        return response()->json([
            'success' => true,
            'end_at' => $endAt->format("Y-m-d H:i:s"),
            'remaining_time' => $diff,
            'calibration_logs' => $calibrationLogs,
            'sensor_values' => $sensorValues,
        ]);
    }
}
