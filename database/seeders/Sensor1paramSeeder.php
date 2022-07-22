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
                'read_formula' => '0F 00 00 00 00 00 55 00',
                'write_formula' => '60 02 AA BB CC DD 7A 00',
                'quality_standard' => 100 // 20 m/g = Recovery, 40 m/g lime klin)
            ],
        ]);
    }
}
