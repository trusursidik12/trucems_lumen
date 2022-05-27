<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Configuration;
use Exception;
use Illuminate\Http\Request;

class ConfigurationController extends Controller
{
    public function index()
    {
        try {
            $configurations = Configuration::find(1);
            return response()->json(['success' => true, 'data' => $configurations]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        $configurations = Configuration::find(1);
        try {
            $column = $this->validate($request, [
                "schedule_auto_calibration" => "string",
                "is_calibration" => "numeric",
                "is_calibration_history" => "numeric",
                "loop_count" => "numeric",
                "calibration_type" => "string",
                "a_default_zero_loop" => "numeric",
                "a_default_span_loop" => "numeric",
                "a_time_zero_loop" => "numeric",
                "a_time_span_loop" => "numeric",
                "a_max_span_ppm" => "numeric",
                "m_default_zero_loop" => "numeric",
                "m_default_span_loop" => "numeric",
                "m_time_zero_loop" => "numeric",
                "m_time_span_loop" => "numeric",
                "m_max_span_ppm" => "numeric",
                "date_and_time" => "nullable",
                "a_start_calibration_at" => "nullable",
                "m_start_calibration_at" => "nullable",
            ], [
                "schedule_auto_calibration.required" => "Schedule auto calibration cant be empty!",
                "is_calibration.required" => "Is Calibration cant be empty!",
                "calibration_type.required" => "Calibration Type cant be empty!",
                "a_default_zero_loop.required" => "Default zero loop cant be empty!",
                "a_default_span_loop.required" => "Default span loop cant be empty!",
                "a_time_zero_loop.required" => "Time Zero Loop cant be empty!",
                "a_time_span_loop.required" => "Time Span Loop cant be empty!",
                "a_max_span_ppm.required" => "MAX Span PPM cant be empty!",
                "m_default_zero_loop.required" => "Default zero loop cant be empty!",
                "m_default_span_loop.required" => "Default span loop cant be empty!",
                "m_time_zero_loop.required" => "Time Zero Loop cant be empty!",
                "m_time_span_loop.required" => "Time Span Loop cant be empty!",
                "m_max_span_ppm.required" => "MAX Span PPM cant be empty!",
            ]);

            $configurations->update($column);

            return response()->json(["success" => true, "message" => "Successfully update configurations!"]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(["success" => false, "errors" => $e->response->original]);
        }
    }
}
