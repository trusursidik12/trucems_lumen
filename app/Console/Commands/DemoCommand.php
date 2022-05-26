<?php
namespace App\Console\Commands;

use App\Models\SensorValue;
use Illuminate\Console\Command;


class DemoCommand extends Command{
    /**
     * How to run function
     * php artisan runDemo
     * @var string
     */
    protected $signature = 'runDemo';
    protected $description = 'Command description';
    public function __construct(){
        parent::__construct();
    }
    public function handle()
    {
        $this->info('Demo Sensor Value is running... [Ctrl+C] to stop it');
            while (true) {
                $values = SensorValue::limit(10)->get();
                foreach ($values as $value) {
                    $value->value = rand(-2,55);
                    $value->save();
                }
                sleep(1);
            }
    }
}