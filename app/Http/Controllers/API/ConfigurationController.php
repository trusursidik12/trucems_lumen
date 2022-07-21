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
                "is_calibration" => "numeric",
                "is_blowback" => "numeric",
                "calibration_type" => "numeric",
                "sensor_id" => "string",
                "target_value" => "nullable",
            ]);
            $column['target_value'] = $column['target_value'] == -1 ? null : $column['target_value'];

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
