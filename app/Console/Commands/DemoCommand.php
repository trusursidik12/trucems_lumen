<?php
namespace App\Console\Commands;

use App\Models\CalibrationAvgLog;
use App\Models\CalibrationLog;
use App\Models\Configuration;
use App\Models\SensorValue;
use Carbon\Carbon;
use Illuminate\Console\Command;


class DemoCommand extends Command{
    /**
     * How to run function
     * php artisan runDemo
     * @var string
     */
    protected $signature = 'runDemo';
    protected $description = 'Command description';
    public function __construct(){
        parent::__construct();
    }
    public function handle()
    {
        $this->info('Demo Sensor Value is running... [Ctrl+C] to stop it');
            while (true) {
                $config = Configuration::find(1);
                $values = SensorValue::limit(10)->get();
                foreach ($values as $value) {
                    $value->value = rand(-2,55);
                    $value->save();
                    if($config->is_calibration > 0 ){ //
                        // When Analyzer calibration
                        $now = Carbon::now();
                        $endAt = ($config->is_calibration == 1 ? $config->a_end_calibration : $config->a_end_calibration);
                        // Is Auto / Manual
                        $endAt = Carbon::parse($endAt);
                        $diff = $now->diffInSeconds($endAt);
                        CalibrationLog::create([
                            'sensor_id' => $value->sensor_id,
                            'value' => $value->value,
                            'calibration_type' => $config->calibration_type // Span / Zero
                        ]);
                        if($diff <= 0){
                            $calibrationLogs = CalibrationLog::first();
                            $sum = CalibrationLog::sum("value");
                            $rowCount = CalibrationLog::get()->count();
                            $avg = ($sum / $rowCount);
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