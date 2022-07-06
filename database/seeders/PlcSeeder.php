<?php

namespace Database\Seeders;

use App\Models\Plc;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class PlcSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        Plc::truncate();
        Plc::create([
            'is_calibration' => 0,
            'is_maintenance' => 0,
            'd0' => 0,
            'd1' => 0,
            'd2' => 0,
            'd3' => 0,
            'd4' => 0,
            'd5' => 0,
            'd6' => 0,
            'd7' => 0,
        ]);
    }
}
