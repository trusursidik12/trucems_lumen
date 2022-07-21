<?php

namespace App\Http\Controllers;

use App\Models\Sensor;
use Illuminate\Http\Request;

class SensorController extends Controller
{
    public function index(){
        $sensors = Sensor::limit(10)->orderBy("id","desc")->get();
        return view('sensors.index', compact('sensors'));
    }
    public function edit($sensorId){
        $sensor = Sensor::find($sensorId);
        return view('sensors.edit', compact('sensor'));
    }
    public function update($sensorId, Request $request){
        try{
            $sensor = Sensor::find($sensorId);
            $column = $this->validate($request, [
                'name' => 'required',
                'code' => 'required',
                'quality_standard' => 'required|numeric',
            ]);
            $sensor->update($column);
            return response()->json(['success'=>true, 'message' => 'Sensor was updated!']);
        }catch(\Illuminate\Validation\ValidationException $e){
            return response()->json(['success'=>false, 'message' => $e->getMessage()]);
        }
    }
}
