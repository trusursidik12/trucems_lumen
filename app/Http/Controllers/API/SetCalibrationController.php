<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Configuration;
use Illuminate\Http\Request;

class SetCalibrationController extends Controller
{
    public function setManualCal($type, Request $request){
        try {
            $column = $this->validate($request,[
                'm_default_zero_loop' => 'required|numeric',
                'm_time_zero_loop' => 'required|numeric',
                'm_span_loop' => 'required|numeric',
                'm_time_span_loop' => 'required|numeric',
                'm_max_span_ppm' => 'required|numeric',
            ],[
                'm_default_zero_loop.required' => 'Default Zero Loop cant empty!',
                'm_time_zero_loop.required' => 'Time Zero Loop cant empty!',
                'm_span_loop.required' => 'Span Zero Loop cant empty!',
                'm_time_span_loop.required' => 'Time Span Loop cant empty!',
                'm_max_span_ppm.required' => 'Max PPM cant empty!',
                'm_default_zero_loop.numeric' => 'Default Zero Loop must be numeric!',
                'm_time_zero_loop.numeric' => 'Time Zero Loop must be numeric!',
                'm_span_loop.numeric' => 'Span Zero Loop must be numeric!',
                'm_time_span_loop.numeric' => 'Time Span Loop must be numeric!',
                'm_max_span_ppm.numeric' => 'Max PPM must be numeric!',
            ]);
            $column['is_calibration'] = 2; // 2 = Manual Cal
            $column['calibration_type'] = ($type == "span" ? 2 : ($type == "zero" ? 1 : 0));
            $column['m_start_calibration_at'] = date('Y-m-d H:i:s');
            $configuration = Configuration::find(1);
            $configuration->update($column);
            return response()->json(["success" => true, "message" => "Successfully update!"]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(["success" => false, "errors" => $e->response->original]);
        }
    }

    public function checkRemaining($type){
        
    }

}
