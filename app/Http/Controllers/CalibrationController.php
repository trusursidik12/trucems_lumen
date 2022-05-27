<?php

namespace App\Http\Controllers;

use App\Models\CalibrationAvgLog;
use App\Models\Configuration;
use App\Models\SensorValue;

class CalibrationController extends Controller
{
    public function auto(){
        $config = Configuration::find(1);
        return view('calibration.auto', compact('config'));
    }
    public function manual(){
        $config = Configuration::find(1);
        return view('calibration.manual', compact('config'));
    }
    public function logs(){
        return view('calibration.logs');
    }

    public function processCal($mode, $type){
        $type = strtoupper($type);
        $mode = strtoupper($mode);
        $sensorValues = SensorValue::limit(10)->get();
        $lastAvg = CalibrationAvgLog::select("value")->orderBy("id","desc")->first();
        return view('calibration.process', compact('type','mode','sensorValues','lastAvg'));

    }
}
