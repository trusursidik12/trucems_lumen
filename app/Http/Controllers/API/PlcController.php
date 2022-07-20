<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Configuration;
use App\Models\Plc;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class PlcController extends Controller
{
    public function index()
    {
        $plc = Plc::find(1);
        return response()->json(['success' => true, 'data' => $plc]);
    }
    public function updatePLC(Request $request)
    {
        try {
            $status = $request->status;
            if ($status == 1) {
                $plc = Plc::find(1);
                if ($plc->is_calibration == 1) {
                    return response()->json(['success' => false, 'message' => 'You need to stop calibration first!']);
                }
                Plc::find(1)->update(['is_maintenance' => $status, 'd_off' => 0]);
            } else {
                Plc::find(1)->update(['is_maintenance' => 2, 'd_off' => 0, 'd0' => 0, 'd1' => 0, 'd2' => 0, 'd3' => 0, 'd4' => 0, 'd5' => 0, 'd6' => 0, 'd7' => 0]);
            }
            $data = Plc::find(1);
            return response()->json(['success' => true, 'message' => 'Success update!', 'data' => $data]);
        } catch (Exception $e) {
            return response()->json(['success' => true, 'message' => $e->getMessage(), 'data' => $data]);
        }
    }
    public function updateCal(Request $request)
    {
        try {
            $status = $request->status;
            if ($status == 1) {
                $plc = Plc::find(1);
                if ($plc->is_maintenance == 1) {
                    return response()->json(['success' => false, 'message' => 'You need to start cems first!']);
                }
            }
            Plc::find(1)->update(['is_calibration' => ($status == 0 ? 2 : $status), 'd_off' => 0]);
            $data = Plc::find(1);
            return response()->json(['success' => true, 'message' => 'Success update!', 'data' => $data]);
        } catch (Exception $e) {
            return response()->json(['success' => true, 'message' => $e->getMessage(), 'data' => $data]);
        }
    }
    public function updateAlarm(Request $request)
    {
        try {
            $alarm = $request->alarm;
            Plc::find(1)->update(['alarm' => $alarm]);
            $data = Plc::find(1);
            return response()->json(['success' => true, 'message' => 'Success update!', 'data' => $data]);
        } catch (Exception $e) {
            return response()->json(['success' => true, 'message' => $e->getMessage(), 'data' => $data]);
        }
    }
}
