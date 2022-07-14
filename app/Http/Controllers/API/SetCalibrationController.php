<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CalibrationLog;
use App\Models\Configuration;
use App\Models\SensorValue;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class SetCalibrationController extends Controller
{
    /**
     * set Calibration function
     *
     * @param [string] $mode is condition calibration manual or auto
     * @param [string] $type is condition span or zero
     * @param Request $request
     * @return json
     */
    public function setCalibration($mode, $type, Request $request)
    {
        try {
            $fieldTimeLoop = substr($mode, 0, 1) . "_time_" . strtolower($type) . "_loop";
            $fieldLoop = substr($mode, 0, 1) . "_default_" . strtolower($type) . "_loop";
            $initialMode = substr($mode, 0, 1); // is m_ or a_
            $column = $this->validate($request, [
                $initialMode . '_default_zero_loop' => 'required|numeric|min:1',
                $initialMode . '_time_zero_loop' => 'required|numeric|min:1',
                $initialMode . '_default_span_loop' => 'required|numeric|min:1',
                $initialMode . '_time_span_loop' => 'required|numeric|min:1',
                $initialMode . '_max_span_ppm' => 'required|numeric|min:1',
            ], [
                $initialMode . '_default_zero_loop.required' => 'Default Zero Loop cant empty!',
                $initialMode . '_time_zero_loop.required' => 'Time Zero Loop cant empty!',
                $initialMode . '_default_span_loop.required' => 'Span Zero Loop cant empty!',
                $initialMode . '_time_span_loop.required' => 'Time Span Loop cant empty!',
                $initialMode . '_max_span_ppm.required' => 'Max PPM cant empty!',
                $initialMode . '_default_zero_loop.numeric' => 'Default Zero Loop must be numeric!',
                $initialMode . '_time_zero_loop.numeric' => 'Time Zero Loop must be numeric!',
                $initialMode . '_default_span_loop.numeric' => 'Span Zero Loop must be numeric!',
                $initialMode . '_time_span_loop.numeric' => 'Time Span Loop must be numeric!',
                $initialMode . '_max_span_ppm.numeric' => 'Max PPM must be numeric!',
                $initialMode . '_default_zero_loop.min' => 'Default Zero Loop must be at least 1',
                $initialMode . '_time_zero_loop.min' => 'Time Zero Loop must be at least 1',
                $initialMode . '_default_span_loop.min' => 'Span Zero Loop must be at least 1',
                $initialMode . '_time_span_loop.min' => 'Time Span Loop must be at least 1',
                $initialMode . '_max_span_ppm.min' => 'Max PPM must be at least 1',
            ]);
            $now = Carbon::now();
            $endAt = $now->addSeconds($column[$fieldTimeLoop] + 1);
            $endAt = $endAt->format("Y-m-d H:i:s");
            $column['is_calibration'] = 2; // 2 = Manual Cal
            $column['is_calibration_history'] = 2; // 2 = Manual Cal
            $column['calibration_type'] = ($type == "span" ? 2 : ($type == "zero" ? 1 : 0));
            $column['m_start_calibration_at'] = date('Y-m-d H:i:s');
            $column['m_end_calibration_at'] = $endAt;
            $column['loop_count'] = ($column[$fieldLoop] - 1);
            $configuration = Configuration::find(1);
            $configuration->update($column);
            return response()->json(["success" => true, "message" => "Successfully update!"]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(["success" => false, "errors" => $e->response->original]);
        }
    }

    /**
     * check remaining calibration progress function
     *
     * @param [string] $mode is condition calibration manual or auto
     * @param [string] $type is condition span or zero
     * @param Request $request
     * @return json
     */
    public function checkRemaining($mode, $type)
    {
        $config = Configuration::find(1);
        $initialMode = substr($mode, 0, 1); // is m_ or a_
        $fieldEndAt = $initialMode . "_end_calibration_at";
        $calibrationType = ($type == "span" ? 2 : 1);
        $endAt = $config->$fieldEndAt;
        $now = Carbon::now();
        $endAt = Carbon::parse($endAt);
        $diff = $now->diffInSeconds($endAt, false);
        $sensorValues = SensorValue::with(['sensor:id,unit_id,code,name', 'sensor.unit:id,name'])
            ->orderBy("id", "desc")->get();
        $calibrationLogs = CalibrationLog::where('calibration_type', $calibrationType)
            ->with(['sensor:id,unit_id,code,name', 'sensor.unit:id,name'])
            ->limit(3)->orderBy("id", "desc")->get();
        return response()->json([
            'success' => true,
            'end_at' => $endAt->format("Y-m-d H:i:s"),
            'remaining_time' => $diff,
            'calibration_logs' => $calibrationLogs,
            'sensor_values' => $sensorValues,
        ]);
    }

    public function updateStatusCalibration($mode, $type)
    {
        try {
            $is_retry = $this->isRetry($mode, $type);
            $config = Configuration::find(1);
            $config->update(['is_calibration' => 3]);
            return response()->json([
                'success' => true,
                'is_retry' => $is_retry,
                'message' => 'Update Status Calibration Success!'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'is_retry' => $is_retry,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function updateTimeCalibration($mode, $type)
    {
        try {
            $config = Configuration::find(1);
            $initialMode = substr($mode, 0, 1); // is m_ or a_
            $fieldLoop = substr($mode, 0, 1) . "_time_" . strtolower($type) . "_loop";
            $fieldStartAt = $initialMode . "_start_calibration_at";
            $fieldEndAt = $initialMode . "_end_calibration_at";
            $startAt = date('Y-m-d H:i:s');
            $endAt = Carbon::now()->addSeconds($config->$fieldLoop + 1)
                ->format('Y-m-d H:i:s');
            $config->update([
                $fieldStartAt => $startAt,
                $fieldEndAt => $endAt,
            ]);
            return response()->json(["success" => true, "message" => 'Success update time']);
        } catch (Exception $e) {
            return response()->json(["success" => false, "errors" => $e->getMessage()]);
        }
    }

    public function isRetry($mode, $type)
    {
        try {
            $config = Configuration::find(1);
            if ($config->loop_count <= 0) {
                $retry = false;
            } else {
                $retry = true;
            }
            return $retry;
        } catch (Exception $e) {
            return false;
        }
    }

    public function calibrationStart()
    {
        $config = Configuration::find(1);
        $config->update(['is_calibration' => 1]);
        return response()->json(["success" => true, "message" => 'Calibration Start']);
    }

    public function offsetAndGain(Request $request, $type)
    {
        try {
            $this->validate($request, ['target_value' => 'required', ['target_value.required' => 'Target value cant empty!']]);
            $value = $request->target_value;
            if (isset($value)) {
                $config = Configuration::find(1);
                $config->update(['calibration_type' => $type, 'target_value' => $value]);
                CalibrationLog::create(['sensor_id' => 1, 'calibration_type' => $type, 'start_value' => SensorValue::find(1)->value, 'target_value' => $value, 'result_value' => null]);
                return response()->json(["success" => true, "message" => 'Target Value Has Been Saved']);
            }
            return response()->json(["success" => false, "error" => 'Saving Target Value Failed']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(["success" => false, "error" => "Target value cant empty"]);
        }
    }

    public function getLastRecord()
    {
        $lastRow = CalibrationLog::latest('id')->first();
        $lastRow->update(['result_value' => SensorValue::find(1)->value]);
        return response()->json(["success" => true, "message" => 'Latest Value Has Been Saved']);
    }

    public function closeCalibration()
    {
        $config = Configuration::first();
        $config->update(['is_calibration' => 0, 'is_blowback' => 0, 'calibration_type' => 0]);
        return response()->json(["success" => true, "message" => 'Calibration Stoped']);
    }
}
