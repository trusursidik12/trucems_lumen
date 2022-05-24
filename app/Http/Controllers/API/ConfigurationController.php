<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Configuration;
use Illuminate\Http\Request;

class ConfigurationController extends Controller
{
    public function index(){
        $configurations = Configuration::find(1);
        return response()->json(['success' => true, 'data' => $configurations]);
    }

    public function update(Request $request){
        $configurations = Configuration::find(1);
        try {
            $column = $this->validate($request,[
                "name" => "nullable",
                "schedule_auto_calibration" => "required|string",
                "default_zero_loop" => "required|numeric",
                "default_span_loop" => "required|numeric",
                "time_zero_loop" => "required|numeric",
                "time_span_loop" => "required|numeric",
                "max_span_ppm" => "required|numeric",
                "date_and_time" => "nullable",
            ],[
                "schedule_auto_calibration.required" => "Schedule auto calibration cant be empty!",
                "default_zero_loop.required" => "Default zero loop cant be empty!",
                "default_span_loop.required" => "Default span loop cant be empty!",
                "time_zero_loop.required" => "Time Zero Loop cant be empty!",
                "time_span_loop.required" => "Time Span Loop cant be empty!",
                "max_span_ppm.required" => "MAX Span PPM cant be empty!",
            ]);
            
            $configurations->update($column);

            return response()->json(["success" => true, "message" => "Successfully update configurations!"]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(["success" => false, "errors" => $e->response->original]);
        }
    }

}
