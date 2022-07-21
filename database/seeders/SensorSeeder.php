<?php

namespace Database\Seeders;

use App\Models\Sensor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class SensorSeeder extends Seeder
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
                'read_formula' => '17 00 00 00 00 00 55 00',
                'write_formula' => '60 00 00 00 00 00 7A 00',
                'quality_standard' => 20 // 20 m/g = Recovery, 40 m/g lime klin)
            ],
            [
                'unit_id' => 1,
                'code' => 'so2',
                'name' => 'SO<sub>2</sub>',
                'read_formula' => '17 01 00 00 00 00 55 00',
                'write_formula' => '60 01 00 00 00 00 7A 00',
                'quality_standard' => 20 // 20 m/g = Recovery, 40 m/g lime klin)
            ],
            [
                'unit_id' => 1,
                'code' => 'no2',
                'name' => 'NO<sub>2</sub>',
                'read_formula' => '0F 00 00 00 00 00 55 00',
                'write_formula' => '60 02 00 00 00 00 7A 00',
                'quality_standard' => 20 // 20 m/g = Recovery, 40 m/g lime klin)
            ]
        ]);
    }
}
