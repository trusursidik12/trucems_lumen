<?php

namespace App\Http\Controllers;

use App\Models\Plc;
use App\Models\SensorValue;

class DashboardController extends Controller
{
    public function index(){
        $plc = Plc::find(1);
        $sensorValues = SensorValue::limit(10)->get();
        return view('dashboard.dashboard', compact('sensorValues', 'plc'));
    }
    public function qualityStandard(){
        return view('quality-standard.index');
    }
}
