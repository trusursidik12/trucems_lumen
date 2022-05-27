<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CalibrationAvgLog;
use Illuminate\Http\Request;

class CalibrationAvgLogsController extends Controller
{
    public function index(){
        $calibrationLogs = CalibrationAvgLog::with(['sensor:id,unit_id,code,name','sensor.unit:id,name'])
        ->orderBy("id","desc")->limit(30)->get();
        return response()->json(['success' => true, 'data' => $calibrationLogs]);
    }

    public function store(Request $request){
        try {
            $column = $this->validate($request,[
                'sensor_id' => 'required|numeric',
                'value' => 'required|numeric',
                'row_count' => 'required|numeric'
            ],[
                "sensor_id.required" => "Sensor cant be empty!",
                "value.required" => "Value cant be empty!",
                "row_count.required" => "Total ROW cant be empty!",
                "sensor_id.numeric" => "Invalid data type sensor_id!",
                "value.numeric" => "Value must be numeric format!",
                "row_count.numeric" => "Total row must be numeric format!",
            ]);
            CalibrationAvgLog::create($column);
            return response()->json(["success" => true, "message" => "Successfully insert calibration logs!"]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(["success" => false, "errors" => $e->response->original]);
        }
    }

    public function logs(){
        $calibrationAvgLogs = CalibrationAvgLog::with(["sensor:id,unit_id,name","sensor.unit:id,name"])
        ->withCasts(["created_at" => "datetime:j F Y H:i:s"])
        ->orderBy("id","desc")->paginate(10);
        return $calibrationAvgLogs;
    }

    public function export(){
        $now = date("Y-m-d")."-".rand(99,999);
        $fileName = $now."-calibration-averaging-logs.csv";
        $calibrationLogs = CalibrationAvgLog::orderBy("id","desc")->limit(500)->get();
        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('Date Time', 'Parameter', 'Concentrate', 'Row Count', 'Unit');

        $callback = function() use($calibrationLogs, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($calibrationLogs as $log) {
                $row['DateTime']  = $log->created_at;
                $row['Parameter']    = strtoupper($log->sensor->code);
                $row['Concentrate']    = $log->value;
                $row['Row Count']    = $log->row_count;
                $row['Unit']  = $log->sensor->unit->name;

                fputcsv($file, array($row['DateTime'], $row['Parameter'], $row['Concentrate'], $row['Row Count'], $row['Unit']));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }


}
