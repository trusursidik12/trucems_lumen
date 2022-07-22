<?php

namespace Database\Seeders;

use App\Models\Sensor;
use App\Models\SensorValue;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class SensorValue3ParamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        SensorValue::truncate();
        SensorValue::insert([
            [
                'sensor_id' => 1,
                'value' => 0.01,
            ],
            [
                'sensor_id' => 2,
                'value' => 0.02,
            ],
            [
                'sensor_id' => 3,
                'value' => 0.03,
            ]
        ]);
    }
}
