<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CalibrationAvgLog;
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
    public function setCalibration($mode, $type, Request $request){
        try {
            $fieldTimeLoop = substr($mode,0,1)."_time_".strtolower($type)."_loop";
            $initialMode = substr($mode,0,1); // is m_ or a_
            $column = $this->validate($request,[
                'm_default_zero_loop' => 'required|numeric',
                'm_time_zero_loop' => 'required|numeric',
                'm_default_span_loop' => 'required|numeric',
                'm_time_span_loop' => 'required|numeric',
                'm_max_span_ppm' => 'required|numeric',
            ],[
                $initialMode.'_default_zero_loop.required' => 'Default Zero Loop cant empty!',
                $initialMode.'_time_zero_loop.required' => 'Time Zero Loop cant empty!',
                $initialMode.'_default_span_loop.required' => 'Span Zero Loop cant empty!',
                $initialMode.'_time_span_loop.required' => 'Time Span Loop cant empty!',
                $initialMode.'_max_span_ppm.required' => 'Max PPM cant empty!',
                $initialMode.'_default_zero_loop.numeric' => 'Default Zero Loop must be numeric!',
                $initialMode.'_time_zero_loop.numeric' => 'Time Zero Loop must be numeric!',
                $initialMode.'_default_span_loop.numeric' => 'Span Zero Loop must be numeric!',
                $initialMode.'_time_span_loop.numeric' => 'Time Span Loop must be numeric!',
                $initialMode.'_max_span_ppm.numeric' => 'Max PPM must be numeric!',
            ]);
            $now = Carbon::now('Asia/Jakarta');
            $endAt = $now->addSeconds($column[$fieldTimeLoop]);
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

     /**
     * check remaining calibration progress function
     *
     * @param [string] $mode is condition calibration manual or auto
     * @param [string] $type is condition span or zero
     * @param Request $request
     * @return json
     */
    public function checkRemaining($mode,$type){
        $config = Configuration::find(1);
        $initialMode = substr($mode,0,1); // is m_ or a_
        $fieldEndAt = $initialMode."_end_calibration_at";
        $endAt = $config->$fieldEndAt;
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
            ->limit(3)->orderBy("id","desc")->get();
        return response()->json([
            'success' => true,
            'end_at' => $endAt->format("Y-m-d H:i:s"),
            'remaining_time' => $diff,
            'calibration_logs' => $calibrationLogs,
            'sensor_values' => $sensorValues,
        ]);
    }

    public function retryCalibration($mode, $type){
        try{
            $config = Configuration::find(1);
            $initialMode = substr($mode,0,1); // is m_ or a_
            $isCalibration = $config->is_calibration;
            $fieldTimeLoop = $initialMode."_time_".strtolower($type)."_loop";
            $fieldStartAt = $initialMode."_start_calibration_at";
            $fieldEndAt = $initialMode."_end_calibration_at";
            if($isCalibration == 1 || $isCalibration == 2){
                $now = Carbon::now('Asia/Jakarta');
                $endAt = $now->addSeconds($config->$fieldTimeLoop);
                $endAt = $endAt->addSeconds(1);
                $endAt = $endAt->format("Y-m-d H:i:s");
                $config->$fieldStartAt = $now->format("Y-m-d H:i:s");
                $config->$fieldEndAt = $endAt;
                $config->save();
                $retry = true;
            }else{
                $retry = false;
            }
            return response()->json(['success' => true, 'is_retry' => $retry]);
        }catch(Exception $e){
            return response()->json(['success' => false, 'is_retry' => false,'message' => $e->getMessage()]);
        }
    }

}
