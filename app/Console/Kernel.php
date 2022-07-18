<?php

namespace App\Console;

use App\Console\Commands\DemoCommand;
use App\Console\Commands\PlcCommand;
use App\Console\Commands\PlcDemoCommand;
use App\Console\Commands\PlcRunCommand;
use App\Console\Commands\StopAppCommand;
use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        StopAppCommand::class,
        DemoCommand::class,
        PlcCommand::class,
        PlcRunCommand::class,
        PlcDemoCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
    }
}
