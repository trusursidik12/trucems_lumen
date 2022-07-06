<?php

namespace App\Console\Commands;

use App\Helper\PhpSerialModbus;
use App\Models\Plc;
use Illuminate\Console\Command;

class PlcCommand extends Command
{
    /**
     * How to run function
     * php artisan runDemo
     * @var string
     */
    protected $signature = 'runPLC';
    protected $description = 'Command description';
    public $modbus;
    public $calibrationSteps;
    public $maintenanceSteps;
    public $timer = 5;
    public function __construct()
    {
        parent::__construct();
        $this->modbus = new PhpSerialModbus;
        // Initialize port
        $this->modbus->deviceInit('/dev/ttyUSB0',115200,'none',8,1,'none');
        // Open port
        $this->modbus->deviceOpen();
        // Enalbe debug
        $this->modbus->debug = false;
    }
    public function flipFlop($d,$timer=5, $loop=2, $check=true){
        for ($i=1; $i <= $loop; $i++) { 
            if($check && $this->checkIsMaintenanceAndCalibration()){
                return false;
            }
            $this->modbus->sendQuery(1,5,"000$d","FF00",true);
            sleep($timer);
            $this->modbus->sendQuery(1,5,"000$d","0000",true);
            sleep($timer);
        }
        return true;
    }
    public function switchAll($data){
        for ($i=0; $i <= 7; $i++) { 
            $this->modbus->sendQuery(1,5,"000$i",$data,true);
        }
        return true;
    }
    public function checkIsMaintenanceAndCalibration(){
        $plc = Plc::select(['is_calibration','is_maintenance', 'd_off'])->find(1);
        if($plc->is_calibration == 1 || $plc->is_maintenance == 1){
        // if($plc->is_maintenance == 1){
            return $this->calibrationAndMaintenance();
        }
        return false;
    }
    public function runPLC($steps, $check=true){
        foreach ($steps as $step) {
            var_dump([$step['d']." === -1", $step['d'] === -1]);
            if($step['d'] === -1){
                if($check && $this->checkIsMaintenanceAndCalibration()){
                    continue;
                }
                $this->switchAll($step['data']);
                echo "All".PHP_EOL;
            }else if($step['data'] == 'flipflop'){
                if($check && $this->checkIsMaintenanceAndCalibration()){
                    continue;
                }
                $this->flipFlop($step['d'], $step['sleep'], $step['loop'],$check);
            }else{
                if($check && $this->checkIsMaintenanceAndCalibration()){
                    continue;
                }
                sleep($step['sleep']);
                $this->modbus->sendQuery(1,5,"000".$step['d'],$step['data'],true);
                sleep($step['sleep']);
            }
        }
    }
    public function calibrationAndMaintenance(){
        $plc = Plc::select(['is_calibration', 'is_maintenance','d_off'])->find(1);
        if($plc->is_calibration == 1){
            if($plc->d_off == 0){
                $this->runPLC($this->calibrationSteps, false);
                Plc::find(1)->update(['d_off' => 1]);
                if($plc->is_calibration != 1){
                    return true;
                }
                // print("masuk sini");
                // return false;
            }   
            // print("masuk sini 2");
            return true;           
        }
        if($plc->is_maintenance == 1){
            if($plc->d_off == 0){
                $this->runPLC($this->maintenanceSteps, false);
                Plc::find(1)->update(['d_off' => 1]);
                return true;
                // return true;
            }        
            return true;
        }
        return false;
    }
    public function handle()
    {
        Plc::find(1)->update(['d_off' => 0]);
        $this->info('PLC Command is running... [Ctrl+C] to stop it');
        $timer = 2;
        $initStep = [
            ['d' => -1, 'data' => '0000', 'sleep' => $timer],
            ['d' => 3, 'data' => 'FF00', 'sleep' => $timer],
        ];
        $steps = [
            ['d' => 7, 'data' => '0000', 'sleep' => $timer],
            ['d' => 0, 'data' => 'FF00', 'sleep' => $timer],
            ['d' => 2, 'data' => 'FF00', 'sleep' => $timer],
            ['d' => 3, 'data' => 'flipflop', 'sleep' => 2, 'loop' => 9],
            ['d' => -1, 'data' => '0000', 'sleep' => $timer],
            ['d' => 1, 'data' => 'FF00', 'sleep' => $timer],
            ['d' => 5, 'data' => 'flipflop', 'sleep' => $timer,'loop' => 2],
            ['d' => 3, 'data' => 'FF00', 'sleep' => $timer],
            ['d' => 6, 'data' => 'flipflop', 'sleep' => $timer,'loop' => 2],
            ['d' => -1, 'data' => '0000', 'sleep' => $timer],
            ['d' => 0, 'data' => 'FF00', 'sleep' => $timer],
            ['d' => 2, 'data' => 'FF00', 'sleep' => $timer],
        ];
        $this->calibrationSteps = [
            ['d' => -1, 'data' => '0000', 'sleep' => $timer],
            ['d' => 7, 'data' => 'FF00', 'sleep' => $timer],
        ];
        $this->maintenanceSteps = [
            ['d' => -1, 'data' => '0000', 'sleep' => $timer],
            ['d' => 1, 'data' => 'FF00', 'sleep' => $timer],
            ['d' => 5, 'data' => 'flipflop', 'sleep' => $timer, 'loop' => 2],
            ['d' => 3, 'data' => 'FF00', 'sleep' => $timer],
            ['d' => 6, 'data' => 'flipflop', 'sleep' => $timer, 'loop' => 2],
            ['d' => -1, 'data' => '0000', 'sleep' => $timer],
        ];
        $this->runPLC($initStep);
        while (true) {
            if(!$this->calibrationAndMaintenance()){
                $this->runPLC($steps);
            }
        }
    }
}
