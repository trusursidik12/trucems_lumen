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
                'code' => 'trs',
                'name' => 'TRS (H<sub>2</sub>S)',
                'formula' => null,
                'quality_standard' => 2 // PPM
            ]
        ]);
    }
}
