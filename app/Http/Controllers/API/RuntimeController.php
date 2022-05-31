<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CalibrationAvgLog;
use App\Models\Runtime;
use Illuminate\Http\Request;

class RuntimeController extends Controller
{
    public function index(){
        $runtime = Runtime::find(1);
        return response()->json(['success' => true, 'data' => $runtime]);
    }

    public function store(Request $request){
        try {
            $column = $this->validate($request,[
                'days' => 'required|numeric',
                'hours' => 'required|numeric',
                'minutes' => 'required|numeric'
            ],[
                "days.required" => "Days cant be empty!",
                "hours.required" => "Hours cant be empty!",
                "minutes.required" => "Minutes cant be empty!",
                "days.numeric" => "Invalid data type Days!",
                "hours.numeric" => "Hours must be numeric format!",
                "minutes.numeric" => "Minutes row must be numeric format!",
            ]);
            $runtime = Runtime::find(1);
            $runtime->update($column);
            return response()->json(["success" => true, "message" => "Successfully update runtime!"]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(["success" => false, "errors" => $e->response->original]);
        }
    }


}
