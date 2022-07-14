<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CalibrationLog;
use App\Models\Configuration;
use App\Models\SensorValue;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class BlowbackController extends Controller
{
    /**
     * set Blowback function
     *
     * @param [string] $type is condition span or zero
     * @param Request $request
     * @return json
     */
    public function setBlowback(Request $request)
    {
        try {
            $configuration = Configuration::find(1);
            $configuration->update(['is_blowback' => 1]);
            return response()->json(["success" => true, "message" => "Start Blowback!"]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(["success" => false, "errors" => $e->response->original]);
        }
    }

    /**
     * check remaining blow back progress
     *
     * @param [string] $type is condition span or zero
     * @param Request $request
     * @return json
     */
    public function checkRemaining()
    {
        $config = Configuration::find(1);
        return response()->json([
            'success' => true,
            'is_blowback' => $config->is_blowback,
        ]);
    }

    public function finishBlowback()
    {
        try {
            $config = Configuration::find(1);
            $config->update([
                'is_relay_open' => 0,
                'end_blowback_at' => null,
                'start_blowback_at' => null,
            ]);
            return response()->json(['success' => true, 'message' => 'Blowback was update to finished!']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
