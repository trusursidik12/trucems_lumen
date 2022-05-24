<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        Unit::truncate();
        Unit::insert([
            [
                'name'              => 'ppm',
            ],
            [
                'name'              => 'μg/m3',
            ],
            [
                'name'              => 'mg/m3',
            ],
            [
                'name'              => 'l/min',
            ],
            [
                'name'              => 'm3/min',
            ],
            [
                'name'              => 'minutes',
            ],
            [
                'name'              => 'ton',
            ],
            [
                'name'              => '%',
            ],
            [
                'name'              => 'm/sec',
            ],
        ]);
    }
}
