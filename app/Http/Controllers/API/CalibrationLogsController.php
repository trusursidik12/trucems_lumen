<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CalibrationLog;
use Illuminate\Http\Request;

class CalibrationLogsController extends Controller
{
    public function logs(Request $request)
    {
        $whereRaw = "1=1";
        if($request->has('calibration_type')){
            $filter = $request->calibration_type;
            if($filter != "all"){
                $whereRaw.=" AND calibration_type = '{$filter}'";
            }
        }
        $calibrationLogs = CalibrationLog::with(["sensor:id,unit_id,name", "sensor.unit:id,name"])
            ->whereRaw($whereRaw)
            ->orderBy("id", "desc")->paginate(10);
        return $calibrationLogs;
    }

    public function export()
    {
        $now = date("Y-m-d") . "-" . rand(99, 999);
        $fileName = $now . "-calibration-averaging-logs.csv";
        $calibrationLogs = CalibrationLog::orderBy("id", "desc")->limit(500)->get();
        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('Date Time', 'Parameter', 'Calibration Type', 'Concentrate', 'Row Count', 'Unit');

        $callback = function () use ($calibrationLogs, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($calibrationLogs as $log) {
                $row['DateTime']  = $log->created_at;
                $row['Parameter']    = strtoupper($log->sensor->code);
                $row['Calibration Type']    = $log->calibration_type  == 1 ? 'Zero' : 'Span';
                $row['Concentrate']    = "{$log->value} / {$log->cal_gas_ppm}";
                $row['Row Count']    = $log->row_count;
                $row['Unit']  = $log->sensor->unit->name;

                fputcsv($file, array($row['DateTime'], $row['Parameter'], $row['Calibration Type'], $row['Concentrate'], $row['Row Count'], $row['Unit']));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
