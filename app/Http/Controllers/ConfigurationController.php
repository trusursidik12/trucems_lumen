<?php

namespace App\Http\Controllers;

use App\Models\CalibrationAvgLog;
use App\Models\Configuration;
use App\Models\Plc;
use App\Models\Sensor;
use App\Models\SensorValue;
use Illuminate\Http\Request;

class ConfigurationController extends Controller
{
    public function index()
    {
        $plc = Plc::find(1);
        return view('configuration.index', compact('plc'));
    }
    public function update(Request $request)
    {
        try {
            $plc = Plc::find(1);
            $column = $this->validate($request, [
                'sleep_sampling' => 'required|numeric',
                'sleep_blowback' => 'required|numeric',
                'loop_sampling' => 'required|numeric',
                'loop_blowback' => 'required|numeric',
                'sleep_default' => 'required|numeric',
            ], [
                "sleep_sampling.required" => "Sleep sampling cant be empty!",
                "sleep_blowback.required" => "Sleep blowback cant be empty!",
                "loop_sampling.required" => "Sampling loop cant be empty!",
                "loop_blowback.required" => "Blowback loop cant be empty!",
                "sleep_default.required" => "Sleep default cant be empty!",
            ]);
            $plc->update($column);

            $column = $this->validate($request,[
                'quality_standard' => 'required|numeric'
            ], [
                'quality_standard.required' => 'Quality standard cant be empty!' 
            ]);
            Sensor::find(1)->update($column);
            return response()->json(["success" => true, "message" => "Successfully update configuration!"]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(["success" => false, "errors" => $e->response->original]);
        }
    }
}
