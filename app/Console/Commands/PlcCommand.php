<?php

namespace App\Console\Commands;

use App\Helper\PhpSerialModbus;
use App\Models\Configuration;
use App\Models\Plc;
use Exception;
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
    public $isConnect;
    public function __construct()
    {
        parent::__construct();
        $this->connectDevice();
    }
    public function connectDevice()
    {
        try {
            $this->modbus = new PhpSerialModbus;
            // Initialize port
            $this->modbus->deviceInit('/dev/ttyPLC', 115200, 'none', 8, 1, 'none');
            // Open port
            $this->modbus->deviceOpen();
            // Enalbe debug
            $this->modbus->debug = false;
            $this->isConnect = true;
        } catch (Exception $e) {
            $this->isConnect = false;
        }
    }
    public function flipFlop($d, $timer = 5, $loop = 2, $check = true)
    {
        for ($i = 1; $i <= $loop; $i++) {
            if ($check && $this->checkIsMaintenanceAndCalibration()) {
                return false;
            }
            $this->sendQuery($d, "FF00");
            sleep($timer);
            $this->sendQuery($d, "0000");
            sleep($timer);
        }
        return true;
    }
    public function switchAll($data)
    {
        for ($i = 0; $i <= 7; $i++) {
            $this->sendQuery($i, $data);
        }
        return true;
    }
    public function checkIsMaintenanceAndCalibration()
    {
        $plc = Plc::select(['is_calibration', 'is_maintenance', 'd_off'])->find(1);
        if ($plc->is_calibration == 1 || $plc->is_maintenance == 1) {
            return $this->calibrationAndMaintenance();
        }
        return false;
    }
    public function sendQuery($d, $data)
    {
        $connect = $this->modbus->sendQuery(1, 5, "000$d", $data, true);
        $this->isConnect = $connect;
        if (!$this->isConnect) {
            $this->connectDevice();
        }
    }
    public function runPLC($steps, $check = true)
    {
        foreach ($steps as $step) {
            if (@$step['type'] == "sampling" || @$step['type'] == "blowback") { // Check is sampling or blowback
                $plc = Plc::find(1); // Get data from db
                if ($step['type'] == "sampling") {
                    $sleep = $plc->sleep_sampling;
                    $loop = $plc->loop_sampling;
                } else {
                    $sleep = $plc->sleep_blowback;
                    $loop = $plc->loop_blowback;
                }
            } else {
                $sleep = @$step['sleep'];
                $loop = @$step['loop'];
            }
            if ($step['d'] === -1) { // All D. D0, D1, D2, D3, D4, D5, D6, D7
                if ($check && $this->checkIsMaintenanceAndCalibration()) {
                    continue;
                }
                $this->switchAll($step['data']);
            } else if ($step['data'] == 'flipflop') {
                if ($check && $this->checkIsMaintenanceAndCalibration()) {
                    continue;
                }
                $this->flipFlop($step['d'], $sleep, $loop, $check);
            } else {
                if ($check && $this->checkIsMaintenanceAndCalibration()) {
                    continue;
                }
                sleep($sleep);
                $this->sendQuery($step['d'], $step['data']);
                sleep($sleep);
            }
        }
    }
    public function calibrationAndMaintenance()
    {
        $timer = 3;
        $plc = Plc::find(1);
        if ($plc->is_calibration == 1) {
            if ($plc->d_off == 0) {
                $this->runPLC($this->calibrationSteps, false);
                Plc::find(1)->update(['d_off' => 1]);
                return true;
            }
            if (Configuration::find(1)->is_blowback == 1) {
                $steps = [
                    ['d' => 1, 'data' => 'FF00', 'sleep' => $timer],
                    ['d' => 5, 'data' => 'flipflop', 'sleep' => $timer, 'loop' => 2, 'type' => 'blowback'], //blowback
                    ['d' => 3, 'data' => 'FF00', 'sleep' => $timer],
                    ['d' => 6, 'data' => 'flipflop', 'sleep' => $timer, 'loop' => 2, 'type' => 'blowback'], //blowback
                    ['d' => -1, 'data' => '0000', 'sleep' => $timer],
                    ['d' => 7, 'data' => 'FF00', 'sleep' => $timer],
                ];
                $this->runPLC($steps);
                Configuration::find(1)->update(['is_blowback' => 0]);
            }
            return true;
        }
        if ($plc->is_maintenance == 1) {
            if ($plc->d_off == 0) {
                $this->runPLC($this->maintenanceSteps, false);
                Plc::find(1)->update(['d_off' => 1]);
                return true;
            }
            $steps = [];
            for ($i = 0; $i <= 7; $i++) {
                $field = "d$i";
                $d = $plc->$field;
                $steps[] = ['d' => $i, 'data' => ($d == 1 ? 'FF00' : '0000'), 'sleep' => 1];
            }
            $this->runPLC($steps);
            return true;
        }
        return false;
    }
    public function handle()
    {
        Plc::find(1)->update(['is_calibration' => 0, 'is_maintenance' => 0, 'd_off' => 0, 'd0' => 0, 'd1' => 0, 'd2' => 0, 'd3' => 0, 'd4' => 0, 'd5' => 0, 'd6' => 0, 'd7' => 0]);
        Configuration::find(1)->update(['is_calibration' => 0, 'is_blowback' => 0, 'calibration_type' => 0]);
        // $this->info('PLC Command is running... [Ctrl+C] to stop it');
        $timer = 3;
        $initStep = [
            ['d' => -1, 'data' => '0000', 'sleep' => $timer],
        ];
        $startStep = [
            ['d' => -1, 'data' => '0000', 'sleep' => $timer],
            ['d' => 3, 'data' => 'FF00', 'sleep' => $timer],
        ];
        $steps = [
            ['d' => 7, 'data' => '0000', 'sleep' => $timer],
            ['d' => 0, 'data' => 'FF00', 'sleep' => $timer],
            ['d' => 2, 'data' => 'FF00', 'sleep' => $timer],
            ['d' => 3, 'data' => 'flipflop', 'sleep' => 2, 'loop' => 9, 'type' => 'sampling'], //sampling
            ['d' => -1, 'data' => '0000', 'sleep' => $timer],
            ['d' => 1, 'data' => 'FF00', 'sleep' => $timer],
            ['d' => 5, 'data' => 'flipflop', 'sleep' => $timer, 'loop' => 2, 'type' => 'blowback'], //blowback
            ['d' => 3, 'data' => 'FF00', 'sleep' => $timer],
            ['d' => 6, 'data' => 'flipflop', 'sleep' => $timer, 'loop' => 2, 'type' => 'blowback'], //blowback
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
        sleep(5);
        $this->runPLC($startStep);
        while (true) {
            if (!$this->calibrationAndMaintenance()) {
                $this->runPLC($steps);
            }
        }
    }
}
