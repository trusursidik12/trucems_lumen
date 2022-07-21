<?php

namespace App\Http\Controllers;

use App\Models\CalibrationLog;
use App\Models\Configuration;
use App\Models\Sensor;
use App\Models\SensorValue;

class CalibrationController extends Controller
{
    public function manual()
    {
        $config = Configuration::find(1);
        $calibrationLog = CalibrationLog::latest('id')->first();
        if ($config->is_calibration == 1 && $calibrationLog->result_value == null) {
            $type = ($calibrationLog->calibration_type == "2" ? "span" : "zero");
            return redirect(url("calibration/manual/" . $type . "/process"));
        }
        $sensors = Sensor::get();
        return view('calibration.manual', compact('config', 'sensors'));
    }
    public function logs()
    {
        return view('calibration.logs');
    }

    public function processCal($mode, $type)
    {
        $type = strtoupper($type);
        $mode = strtoupper($mode);
        $config = Configuration::find(1);
        $sensorValue = SensorValue::where(['sensor_id' => $config->sensor_id])->first();
        return view('calibration.process', compact('type', 'mode', 'sensorValue'));
    }
}
