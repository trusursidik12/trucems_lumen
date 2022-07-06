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
    
    public function updatePLC(Request $request){
        try{
            $status = $request->status;
            Plc::find(1)->update(['is_maintenance'=> $status, 'd_off' => 0]);
            $data = Plc::find(1);
            return response()->json(['success' => true, 'message' => 'Success update!' ,'data' => $data]);
        }catch(Exception $e){
            return response()->json(['success' => true, 'message' => $e->getMessage(),'data' => $data]);
        }
    }
    public function updateCal(Request $request){
        try{
            $status = $request->status;
            Plc::find(1)->update(['is_calibration'=> $status, 'd_off' => 0]);
            $data = Plc::find(1);
            return response()->json(['success' => true, 'message' => 'Success update!' ,'data' => $data]);
        }catch(Exception $e){
            return response()->json(['success' => true, 'message' => $e->getMessage(),'data' => $data]);
        }
    }

}
