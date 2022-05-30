<?php

namespace Database\Seeders;

use App\Models\Runtime;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class RuntimeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        Runtime::truncate();
        Runtime::create([
            'days' => 0,
            'hours' => 0,
            'minutes' => 1,
        ]);
    }
}
