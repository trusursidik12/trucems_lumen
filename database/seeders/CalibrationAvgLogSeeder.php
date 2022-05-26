<?php

namespace Database\Seeders;

use App\Models\CalibrationAvgLog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class CalibrationAvgLogSeeder extends Seeder
{
    /**
     * Demo Calibration Avg Log
     * php artisan db:seed CalibrationAvgLogSeeder
     * to run this function
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        CalibrationAvgLog::truncate();
        CalibrationAvgLog::factory()->count(rand(10,30))->create();
    }
}
