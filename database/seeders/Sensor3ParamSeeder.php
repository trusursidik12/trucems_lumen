<?php

namespace Database\Seeders;

use App\Models\Sensor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class Sensor3ParamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        Sensor::truncate();
        Sensor::insert([
            [
                'unit_id' => 1,
                'code' => 'no',
                'name' => 'NO',
                'read_formula' => '17 00 00 00 00 00 55 00', //ch1
                'write_formula' => '60 00 AA BB CC DD 7A 00', //ch1
                'quality_standard' => 100 // 20 m/g = Recovery, 40 m/g lime klin)
            ],
            [
                'unit_id' => 1,
                'code' => 'so2',
                'name' => 'SO<sub>2</sub>',
                'read_formula' => '0F 00 00 00 00 00 55 00', //ch3
                'write_formula' => '60 02 AA BB CC DD 7A 00', //ch3
                'quality_standard' => 100 // 20 m/g = Recovery, 40 m/g lime klin)
            ],
            [
                'unit_id' => 1,
                'code' => 'no2',
                'name' => 'NO<sub>2</sub>',
                'read_formula' => '0F 01 00 00 00 00 55 00', //ch4
                'write_formula' => '60 03 AA BB CC DD 7A 00', //ch4
                'quality_standard' => 100 // 20 m/g = Recovery, 40 m/g lime klin)
            ]
        ]);
        $this->call(SensorValue3ParamSeeder::class);
    }
}
