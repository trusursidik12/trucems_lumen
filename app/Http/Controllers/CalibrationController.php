<?php

namespace App\Http\Controllers;


class CalibrationController extends Controller
{
    public function manual(){
        return view('calibration.manual');
    }
}
