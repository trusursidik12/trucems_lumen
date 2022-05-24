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
            'name' => 'TruCEMS v.1.0.1',
            'default_zero_loop' => 7,
            'default_span_loop' => 7,
            'time_zero_loop' => 120,
            'time_span_loop' => 120,
            'max_span_ppm' => 1000,
        ]);
    }
}
