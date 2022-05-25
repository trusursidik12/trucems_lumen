<?php

namespace App\Http\Controllers;

use App\Models\SensorValue;

class CalibrationController extends Controller
{
    public function auto(){
        return view('calibration.auto');
    }
    public function manual(){
        return view('calibration.manual');
    }
    public function logs(){
        return view('calibration.logs');
    }

    public function processCal($mode, $type){
        $type = strtoupper($type);
        $mode = strtoupper($mode);
        switch ($type) {
            case 'MANUAL':
                break;
                
            case 'AUTO':
            default:
                break;
        }
        $sensorValues = SensorValue::limit(10)->get();
        return view('calibration.process', compact('type','mode','sensorValues'));

    }
}
