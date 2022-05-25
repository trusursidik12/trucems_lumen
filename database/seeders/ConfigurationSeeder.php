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
            'a_default_zero_loop' => 7,
            'a_default_span_loop' => 7,
            'a_time_zero_loop' => 120,
            'a_time_span_loop' => 120,
            'a_max_span_ppm' => 1000,
            'm_default_zero_loop' => 7,
            'm_default_span_loop' => 7,
            'm_time_zero_loop' => 120,
            'm_time_span_loop' => 120,
            'm_max_span_ppm' => 1000,
        ]);
    }
}
