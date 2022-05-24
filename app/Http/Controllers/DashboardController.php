<?php

namespace App\Http\Controllers;

use App\Models\SensorValue;

class DashboardController extends Controller
{
    public function index(){
        $sensorValues = SensorValue::limit(10)->get();
        return view('dashboard.dashboard', compact('sensorValues'));
    }
}
