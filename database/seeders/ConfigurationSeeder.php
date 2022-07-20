<?php

namespace Database\Seeders;

use App\Models\Configuration;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class ConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        Configuration::truncate();
        Configuration::create([
            'is_calibration' => 0,
            'is_blowback' => 0,
            'calibration_type' => 0,
        ]);
    }
}
