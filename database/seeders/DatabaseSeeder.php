<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call('UsersTableSeeder');
        $this->call(RuntimeSeeder::class);
        $this->call(ConfigurationSeeder::class);
        $this->call(UnitSeeder::class);
        $this->call(SensorSeeder::class);
        $this->call(SensorValueSeeder::class);
    }
}
