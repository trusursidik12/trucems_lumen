<?php

namespace App\Console\Commands;

use App\Models\Configuration;
use App\Models\Plc;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

class PlcDemoCommand extends Command
{
    protected $signature = 'demo-plc';
    protected $description = 'Run simulation PLC';

    public $calibrationCounter = 0;
    public $maintenanceCounter = 0;
    public $blowbackCounter = 0;
    public function __construct()
    {
        parent::__construct();
    }


    public function handle()
    {
        Plc::find(1)->update(['is_calibration' => 0, 'is_maintenance' => 0, 'd_off' => 0]);
        Configuration::find(1)->update(['is_blowback' => 0]);
        $this->info("Demo PLC was running!" . PHP_EOL . "Open http://localhost/trucems/public/plc-simulation in your browser!");
        $this->info("Init Mode");
        $this->updateData($this->initMode());
        while (true) {
            $plc = Plc::select(['d_off', 'is_calibration' , 'is_maintenance'])->find(1);
            $config = Configuration::select('is_blowback')->find(1);
            // Normal Mode
            if($plc->is_maintenance == 0 && $plc->is_calibration == 0){
                Plc::find(1)->update(['d_off' => 0]);
                $this->updateData($this->normalMode());
                $this->info("Normal Mode");
                continue;
            }
            //check is cal or is maintenance
            if($plc->is_maintenance == 1){
                $this->runMaintenance();
                continue;
            } 
            if($plc->is_calibration == 1 && $plc->d_off == 0 && $config->is_blowback == 0){
                $this->runCalibration();
                continue;
            }           
            if($plc->is_calibration == 1 && $plc->d_off == 0 && $config->is_blowback == 1){
                $this->blowbackMode();
                continue;
            }
        }
    }
    public function runBlowback(){
        $plc = Plc::find(1);
        $this->info("Blowback Mode");
        $this->updateData($this->blowbackMode());
        $plc->update(['d_off' => 1]);
    }
    public function runMaintenance(){
        $plc = Plc::find(1);
        $plc->update(['d_off' => 1]);
        $this->updateData($this->maintenanceMode());
        $this->info("Maintenance Mode");
    }
    public function runCalibration(){
        $plc = Plc::find(1);
        $plc->update(['d_off' => 1]);
        $this->info("Calibration Mode");
        $this->updateData($this->calibrationMode());
    }

    public function updateData($steps){
        foreach ($steps as $step) {
            $columns = [];
            if($step['mode'] == "flipflop"){
                $columns[] = $this->flipflopMode($step['d'], $step['loop'])[0];
            }elseif($step['d'] == -1){
                for ($i=0; $i <= 7; $i++) { 
                    $columns[] = ['d' => $i, 'mode' => $step['mode']];
                }
            }else{
                $columns[] = $step;
            }
            // print_r($columns);
            foreach ($columns as $column) {
                $plc = Plc::find(1);
                $config = Configuration::find(1);
                if($plc->is_maintenance == 1){
                    $this->runMaintenance();
                    continue;
                } 
                if($plc->is_calibration == 1 && $plc->d_off == 0 && $config->is_blowback == 0){
                    $this->runCalibration();
                    continue;
                }           
                if($plc->is_calibration == 1 && $plc->d_off == 0 && $config->is_blowback == 1){
                    $this->blowbackMode();
                    continue;
                }
                $field = "d".$column['d'];
                $plc->update([$field => ($column['mode'] == "on" ? 1 : 0)]);
                sleep(1);
            }
        }
       
        
    }
    public function flipflopMode($d, $loop = 2)
    {
        $flipflopSteps = [];
        for ($i = 0; $i < $loop; $i++) {
            $flipflopSteps[] = ['d' => $d, 'mode' => 'on'];
            $flipflopSteps[] = ['d' => $d, 'mode' => 'off'];
        }
        return $flipflopSteps;
    }
    public function initMode()
    {
        return [
            ['d' => -1, 'mode' => 'off', 'sleep' => 2],
            // ['d' => -1, 'mode' => 'off', 'sleep' => 2],
            ['d' => 3, 'mode' => 'on', 'sleep' => 2],
        ];
    }
    public function normalMode()
    {
        return [
            ['d' => 7, 'mode' => 'off'],
            ['d' => 0, 'mode' => 'on'],
            ['d' => 2, 'mode' => 'on'],
            ['d' => 3, 'mode' => 'flipflop', 'loop' => 9],
            ['d' => -1, 'mode' => 'off'],
            ['d' => 5, 'mode' => 'flipflop', 'loop' => 2],
            ['d' => 3, 'mode' => 'on'],
            ['d' => 6, 'mode' => 'flipflop', 'loop' => 2],
            ['d' => -1, 'mode' => 'off'],
            ['d' => 0, 'mode' => 'on'],
            ['d' => 2, 'mode' => 'on'],
        ];
    }

    public function calibrationMode()
    {
        return [
            ['d' => -1, 'mode' => 'off'],
            ['d' => 7, 'mode' => 'on'],
        ];
    }

    public function maintenanceMode()
    {
        return [
            ['d' => -1, 'mode' => 'off'],
            ['d' => 1, 'mode' => 'on'],
            ['d' => 5, 'mode' => 'flipflop', 'loop' => 2],
            ['d' => 3, 'mode' => 'on'],
            ['d' => 6, 'mode' => 'flipflop', 'loop' => 2],
            ['d' => -1, 'mode' => 'off'],
        ];
    }
    public function blowbackMode()
    {
        return [
            ['d' => 1, 'mode' => 'FF00'],
            ['d' => 5, 'mode' => 'flipflop', 'loop' => 2],
            ['d' => 3, 'mode' => 'FF00'],
            ['d' => 6, 'mode' => 'flipflop', 'loop' => 2],
            ['d' => -1, 'mode' => '0000'],
            ['d' => 7, 'mode' => 'FF00'],
        ];
    }
}
