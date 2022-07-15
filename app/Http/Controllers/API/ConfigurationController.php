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
                "is_calibration_history" => "numeric",
                "is_relay_open" => "numeric",
                "is_calibration" => "numeric",
                "loop_count" => "numeric",
                "calibration_type" => "string",
                "m_default_zero_loop" => "numeric",
                "m_default_span_loop" => "numeric",
                "m_time_zero_loop" => "numeric",
                "m_time_span_loop" => "numeric",
                "m_max_span_ppm" => "numeric",
                "date_and_time" => "nullable",
                "m_start_calibration_at" => "nullable",
            ], [
                "is_relay_open.required" => "Is Relay Open cant be empty!",
                "is_calibration.required" => "Is Calibration cant be empty!",
                "calibration_type.required" => "Calibration Type cant be empty!",
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
    public function setRelay(Request $request)
    {
        try {
            $column = $this->validate($request, [
                'is_relay_open' => 'required|numeric'
            ], [
                'is_relay_open.required' => 'Is Relay Open cant empty!',
                'is_relay_open.numeric' => 'Invalid Format Is Relay Open!',
            ]);
            $config = Configuration::find(1);
            $config->update($column);
            return response()->json(['success' => true, 'message' => 'Success! Relay is open']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->response->original]);
        }
    }
    public function isRelayOpen()
    {
        try {
            $config = Configuration::select('is_relay_open')->find(1);
            $isOpen = ($config->is_relay_open == 3 ? true : false);
            return response()->json(['success' => true, 'data' => ['is_open' => $isOpen, 'is_relay_open' => $config->is_relay_open]]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error : ' . $e->getMessage()]);
        }
    }
}
