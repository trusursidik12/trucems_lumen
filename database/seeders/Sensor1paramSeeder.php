<?php

namespace Database\Seeders;

use App\Models\Sensor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class Sensor1ParamSeeder extends Seeder
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
                'code' => 'trs',
                'name' => 'TRS(H<sub>2</sub>S)',
                'unit_formula' => 'Math.round(value.value * (34/22.4) * 100) / 100', //convert to mg
                'read_formula' => '0F 00 00 00 00 00 55 00', //ch3
                'write_formula' => '60 02 AA BB CC DD 7A 00', //ch3
                'quality_standard' => 100 // 20 m/g = Recovery, 40 m/g lime klin)
            ],
        ]);
        $this->call(SensorValue1ParamSeeder::class);
    }
}
