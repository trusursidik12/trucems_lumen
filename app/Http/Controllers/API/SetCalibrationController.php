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
     * get realtime value on calibration process
     *
     * @param Request $request
     * @return json
     */
    public function getRealtimeValue()
    {
        try {
            $config = Configuration::select('sensor_id')->find(1);
            $sensorValues = SensorValue::where(['sensor_id' => $config->sensor_id])
                ->with(['sensor:id,unit_id,code,name', 'sensor.unit:id,name'])
                ->orderBy("id", "desc")->get();
            return response()->json([
                'success' => true,
                'sensor_values' => $sensorValues,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'sensor_values' => -1
            ]);
        }
    }

    public function calibrationStart($sensorId, Request $request)
    {
        $calibrationType = $request->calibration_type;
        $config = Configuration::find(1);
        $config->update(['is_calibration' => 1, 'sensor_id' => $sensorId, 'calibration_type' => $calibrationType]);
        return response()->json(["success" => true, "message" => 'Calibration Start']);
    }

    public function offsetAndGain(Request $request, $type)
    {
        try {
            $this->validate($request, [
                'current_value' => 'required|numeric',
                'target_value' => 'required|numeric',
            ], [
                'current_value.required' => 'Current value cant empty!',
                'target_value.required' => 'Target value cant empty!',
                'current_value.numeric' => 'Current value is not numeric!',
                'target_value.numeric' => 'Target value is not numeric!'
            ]);
            $targetValue = $request->target_value;
            $currentValue = $request->current_value;
            if (isset($targetValue)) {
                $config = Configuration::find(1);
                $config->update(['calibration_type' => $type, 'target_value' => $targetValue]);
                CalibrationLog::create(['sensor_id' => 1, 'calibration_type' => $type, 'start_value' => $currentValue, 'target_value' => $targetValue, 'result_value' => ($type == 1 ? $currentValue + 0 : $currentValue / $targetValue)]);
                return response()->json(["success" => true, "message" => 'Target Value Has Been Saved']);
            }
            return response()->json(["success" => false, "error" => 'Saving Target Value Failed']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(["success" => false, "error" => "Target value cant empty"]);
        }
    }

    public function closeCalibration()
    {
        $config = Configuration::first();
        $config->update(['is_calibration' => 0, 'is_blowback' => 0, 'calibration_type' => 0, 'sensor_id' => null]);
        return response()->json(["success" => true, "message" => 'Calibration Stoped']);
    }
}
