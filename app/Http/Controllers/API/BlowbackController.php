<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CalibrationLog;
use App\Models\Configuration;
use App\Models\SensorValue;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

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
            $column = $this->validate($request, [
                'blowback_duration' => 'required|numeric',
                'is_relay_open' => 'required|numeric',
            ],[
                'blowback_duration.required' => 'Blowback duration is required!',
                'blowback_duration.numeric' => 'Invalid type blow back duration!',
            ]);
            $now = Carbon::now();
            $endAt = $now->addSeconds($column['blowback_duration'] + 1);
            $endAt = $endAt->format("Y-m-d H:i:s");
            $configuration = Configuration::find(1);
            $configuration->update($column);
            return response()->json(["success" => true, "message" => "Blowback updated!"]);
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
    public function checkRemaining($type)
    {
        $config = Configuration::find(1);
        $now = Carbon::now();
        $endAt = Carbon::parse($config->end_blowback_at);
        $diff = $now->diffInSeconds($endAt, false);
        return response()->json([
            'success' => true,
            'end_at' => $endAt->format("Y-m-d H:i:s"),
            'remaining_time' => $diff,
        ]);
    }

}
