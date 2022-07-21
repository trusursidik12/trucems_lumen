<?php

namespace App\Http\Controllers;

use App\Models\Plc;
use App\Models\SensorValue;

class DashboardController extends Controller
{
    public function index(){
        $plc = Plc::find(1);
        $sensorValues = SensorValue::limit(10)->get();
        $count = $sensorValues->count();
        return view('dashboard.dashboard', compact('sensorValues', 'plc', 'count'));
    }
    public function qualityStandard(){
        return view('quality-standard.index');
    }
}
