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
       try{
            $sensorValues = SensorValue::with(['sensor:id,unit_id,code,name', 'sensor.unit:id,name'])
            ->orderBy("id", "desc")->get();
            return response()->json([
                'success' => true,
                'sensor_values' => $sensorValues,
            ]);
       }catch(Exception $e){
            return response()->json([
                'success' => false,
                'sensor_values' => -1
            ]);
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
                CalibrationLog::create(['sensor_id' => 1, 'calibration_type' => $type, 'start_value' => $currentValue, 'target_value' => $targetValue, 'result_value' => null]);
                return response()->json(["success" => true, "message" => 'Target Value Has Been Saved']);
            }
            return response()->json(["success" => false, "error" => 'Saving Target Value Failed']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(["success" => false, "error" => "Target value cant empty"]);
        }
    }

    public function getLastRecord(Request $request)
    {
        $lastRow = CalibrationLog::latest('id')->first();
        $currentValue = (double) $request->current_value;
        if ($lastRow->result_value == null && isset($currentValue)) {
            $lastRow->update(['result_value' => $currentValue]);
        }
        return response()->json(["success" => true, "message" => 'Latest Value Has Been Saved']);
    }

    public function closeCalibration()
    {
        $config = Configuration::first();
        $config->update(['is_calibration' => 0, 'is_blowback' => 0, 'calibration_type' => 0]);
        return response()->json(["success" => true, "message" => 'Calibration Stoped']);
    }
}
