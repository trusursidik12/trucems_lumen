<?php

namespace App\Http\Controllers;


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
}
