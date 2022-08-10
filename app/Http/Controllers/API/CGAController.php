<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Configuration;
use App\Models\Plc;
use App\Models\SensorValue;
use Exception;
use Illuminate\Http\Request;

class CGAController extends Controller
{
    /**
     * set Blowback function
     *
     * @param [string] $type is condition span or zero
     * @param Request $request
     * @return json
     */
    public function setCGA(Request $request)
    {
        try {
            $plc = Plc::find(1);
            $plc->update(['is_calibration' => 0]);
            sleep(3);
            $plc->update(['is_cga' => 1, 'd_off' => 0, 'is_calibration' => 0]);
            return response()->json(["success" => true, "message" => "Start CGA!"]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(["success" => false, "errors" => $e->response->original]);
        }
    }

    /**
     * check remaining CGA progress
     *
     * @param [string] $type is condition span or zero
     * @param Request $request
     * @return json
     */
    public function checkRemaining()
    {
        $plc = Plc::find(1);
        return response()->json([
            'success' => true,
            'is_cga' => $plc->is_cga,
        ]);
    }

    public function finishCGA()
    {
        try {
            $plc = Plc::find(1);
            $plc->update(['is_cga' => 2]);
            return response()->json(['success' => true, 'message' => 'CGA was update to finished!']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function process()
    {
        $sensorValues = SensorValue::limit(10)->get();
        $count = $sensorValues->count();
        return view('cga.process', compact('sensorValues', 'count'));
    }
}
