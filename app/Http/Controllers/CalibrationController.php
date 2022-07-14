<?php

namespace App\Http\Controllers;

use App\Models\CalibrationLog;
use App\Models\Configuration;
use App\Models\SensorValue;

class CalibrationController extends Controller
{
    public function auto()
    {
        $config = Configuration::find(1);
        return view('calibration.auto', compact('config'));
    }
    public function manual()
    {
        $config = Configuration::find(1);
        $calibrationLog = CalibrationLog::latest('id')->first();
        if ($config->is_calibration == 1 && $calibrationLog->result_value == null) {
            $type = ($calibrationLog->calibration_type == "2" ? "span" : "zero");
            return redirect(url("calibration/manual/" . $type . "/process"));
        }
        return view('calibration.manual', compact('config'));
    }
    public function logs()
    {
        return view('calibration.logs');
    }

    public function processCal($mode, $type)
    {
        $type = strtoupper($type);
        $mode = strtoupper($mode);
        $sensorValues = SensorValue::limit(10)->get();
        $calibrationLog = CalibrationLog::latest('id')->first();
        $calibrationType = ($type == "SPAN" ? 2 : 1);
        // $lastAvg = CalibrationAvgLog::select("value")->where('calibration_type', $calibrationType)->orderBy("id", "desc")->first();
        $lastAvg = 1;
        return view('calibration.process', compact('type', 'mode', 'sensorValues', 'lastAvg', 'calibrationLog'));
    }
}
