<?php

namespace App\Console\Commands;

use App\Models\CalibrationAvgLog;
use App\Models\CalibrationLog;
use App\Models\Configuration;
use App\Models\SensorValue;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DemoCommand extends Command
{
    /**
     * How to run function
     * php artisan runDemo
     * @var string
     */
    protected $signature = 'runDemo';
    protected $description = 'Command description';
    public function __construct()
    {
        parent::__construct();
    }
    public function handle()
    {
        $this->info('Demo Sensor Value is running... [Ctrl+C] to stop it');
        while (true) {
            $values = SensorValue::limit(10)->get();
            foreach ($values as $value) {
                $value->value = round(rand(2, 20) / (rand(1, 10) * 10), 2);
                $value->save();
            }
            sleep(1);
        }
    }
}
