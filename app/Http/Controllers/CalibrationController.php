<?php

namespace App\Http\Controllers;

use App\Models\CalibrationAvgLog;

class CalibrationController extends Controller
{
    public function auto(){
        return view('calibration.auto');
    }
    public function manual(){
        return view('calibration.manual');
    }
    public function logs(){
        $calibrationAvgLogs = CalibrationAvgLog::orderBy("id","desc")->paginate(10);
        return view('calibration.logs', compact('calibrationAvgLogs'));
    }
}
