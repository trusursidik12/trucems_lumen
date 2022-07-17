<?php

namespace App\Console\Commands;

use App\Models\Configuration;
use App\Models\Plc;
use Illuminate\Console\Command;

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
        Plc::find(1)->update(['is_calibration' => 0, 'is_maintenance' => 0]);
        $this->info("Demo PLC was running!".PHP_EOL."Open http://localhost/trucems/public/plc-simulation in your browser!");
        $this->runPLC($this->initMode(),"init", false);
        while (true) {
            $plc = Plc::select(['is_calibration','is_maintenance'])->find(1);
            $config = Configuration::select("is_blowback")->find(1);
            if($plc->is_calibration == 1 || $plc->is_maintenance == 1 || $config->is_blowback == 1){
                echo "Skipped...".PHP_EOL;
                continue;
            }
            $this->runPLC($this->normalMode());
        }
    }

    public function initMode(){
        return [
            ['d' => -1, 'mode' => 'off', 'sleep' => 2],
            // ['d' => -1, 'mode' => 'off', 'sleep' => 2],
            ['d' => 3, 'mode' => 'on', 'sleep' => 2],
        ];
    }

    /**
     * run PLC function
     * if return was true, its run normal
     * if return was false, its run interuppted and must delayed until return was true
     * @param [type] $steps
     * @param string $type
     * @param boolean $enableInterrupt
     * @return boolean
     */
    public function runPLC($steps, $type="normal", $enableInterrupt = true, $enableLoop = true){
        if($enableLoop){
            echo "Running {$type} mode".PHP_EOL;
            foreach ($steps as $step) {
                echo $step['d']." : ".$step['mode'].PHP_EOL;
                $plc = Plc::find(1);
                $config = Configuration::select('is_blowback')->find(1);
                $this->calibrationCounter = ($plc->is_calibration == 0 ? 0 : $this->calibrationCounter);
                $this->maintenanceCounter = ($plc->is_maintenance == 0 ? 0 : $this->maintenanceCounter);
                $this->blowbackCounter = ($config->is_blowback == 0 ? 0 : $this->blowbackCounter);
                if($enableInterrupt){ // Check if has interrupt = true, must add enableLoop ?
                    $config = Configuration::find(1);
                    if($plc->is_calibration == 1 && $this->calibrationCounter == 0){
                        $this->calibrationCounter = 1;
                        $this->runPLC($this->calibrationMode(),"calibration", false, true);
                        // $plc->update(['is_calibration' => 0]);
                        continue; // false cause interrupted
                    }
                    if($plc->is_maintenance == 1 && $this->maintenanceCounter == 0){
                        $this->maintenanceCounter = 1;
                        $this->runPLC($this->maintenanceMode(),"maintenance", false, true);
                        // $plc->update(['is_maintenance' => 0]);
                        continue; // false cause interrupted
                    }
                    if($config->is_blowback == 1 && $this->blowbackCounter == 0){
                        $this->blowbackCounter = 1;
                        $this->runPLC($this->blowbackMode(),"blowback", false, true);
                        // $config->update(['is_blowback' => 0]);
                        continue; // false cause interrupted
                    }
                    if($this->calibrationCounter > 0 || $this->maintenanceCounter > 0 || $this->blowbackCounter > 0){
                        continue;
                    }
                }
                if($step['mode'] == "flipflop"){
                    $this->runFlipflop($step['d'],$step['loop']);
                    continue;
                }
                $mode = ($step['mode'] == "on" ? 1 : 0);
                if($step['d'] == -1){
                    for ($i=0; $i <= 7; $i++) { 
                        $field = "d$i";
                        $plc->update([$field => $mode]);
                        sleep(1);
                    }
                }else{
                    sleep(2);
                    $plc->update(["d".$step['d'] => $mode]);
                }
            }
        }
        
        // return true; // true cause not interrupter, run normal
    }

    public function runFlipflop($d, $loop = 2){
        $flipflopSteps = [];
        for ($i=0; $i < $loop; $i++) { 
            $flipflopSteps[] = ['d' => $d, 'mode' => 'on'];
            $flipflopSteps[] = ['d' => $d, 'mode' => 'off'];
        }
        $this->runPLC($flipflopSteps, "flipflop");
    }

    public function normalMode(){
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

    public function calibrationMode(){
        return [
            ['d' => -1, 'mode' => 'off'],
            ['d' => 7, 'mode' => 'on'],
        ];
    }

    public function maintenanceMode(){
        return [
            ['d' => -1, 'mode' => 'off'],
            ['d' => 1, 'mode' => 'on'],
            ['d' => 5, 'mode' => 'flipflop', 'loop' => 2],
            ['d' => 3, 'mode' => 'on'],
            ['d' => 6, 'mode' => 'flipflop', 'loop' => 2],
            ['d' => -1, 'mode' => 'off'],
        ];
    }
    public function blowbackMode(){
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
