<?php

namespace App\Console\Commands;

use App\Models\CalibrationAvgLog;
use App\Models\CalibrationLog;
use App\Models\Configuration;
use App\Models\Plc;
use App\Models\SensorValue;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PlcCommand extends Command
{
    /**
     * How to run function
     * php artisan runDemo
     * @var string
     */
    protected $signature = 'runPLC';
    protected $description = 'Command description';
    public function __construct()
    {
        parent::__construct();
    }
    public function handle()
    {
        $this->info('Demo Sensor Value is running... [Ctrl+C] to stop it');
        while (true) {
            $config = Plc::find(1);
            if($config->d0 == 0){
                
            }

            $values = SensorValue::limit(10)->get();
            foreach ($values as $value) {
                $value->value = rand(2, 20) / (rand(1, 10) * 10);
                $value->save();
                if ($config->is_calibration == 1 || $config->is_calibration == 2) {
                    CalibrationLog::create([
                        'sensor_id' => $value->sensor_id,
                        'value' => $value->value,
                        'calibration_type' => $config->calibration_type // Span / Zero
                    ]);
                }
                if ($config->is_calibration > 2) { //
                    // When Analyzer done for calibration
                    if ($config->loop_count >= 1) {
                        $column = [
                            'loop_count' => ($config->loop_count - 1),
                            'is_calibration' => $config->is_calibration_history,
                        ];
                    } else {
                        $column = [
                            'loop_count' => 0,
                            'is_calibration' => 0,
                        ];
                    }
                    $config->update($column);

                    $calibrationLogs = CalibrationLog::first();
                    if (!empty($calibrationLogs)) {
                        $sum = CalibrationLog::sum("value");
                        $rowCount = CalibrationLog::get()->count();
                        $avg = $rowCount > 0 ? ($sum / $rowCount) : 0;
                        CalibrationAvgLog::create([
                            'sensor_id' => $calibrationLogs->sensor_id,
                            'row_count' => $rowCount,
                            'value' => $avg,
                            'calibration_type' => $calibrationLogs->calibration_type,
                        ]);
                        CalibrationLog::truncate();
                    }
                }
            }
            sleep(1);
        }
    }
}
