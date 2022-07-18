<?php

namespace App\Console\Commands;

use App\Helper\PhpSerialModbus;
use App\Models\Configuration;
use App\Models\Plc;
use Exception;
use Illuminate\Console\Command;

class PlcRunCommand extends Command
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
    public $blowback;
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
    public function flipFlop($d, $timer = 5, $loop = 2)
    {
        for ($i = 1; $i <= $loop; $i++) {
            if ($this->runCalAndMaintenance() == false) {
                $this->sendQuery($d, "FF00");
                sleep($timer);
                $this->sendQuery($d, "0000");
                sleep($timer);
            }
        }
        // return true;
    }
    public function flipFlopCalMt($d, $timer = 5, $loop = 2)
    {
        for ($i = 1; $i <= $loop; $i++) {
            $this->sendQuery($d, "FF00");
            sleep($timer);
            $this->sendQuery($d, "0000");
            sleep($timer);
        }
        // return true;
    }
    public function switchAll($data)
    {
        for ($i = 0; $i <= 7; $i++) {
            $this->sendQuery($i, $data);
        }
        // return true;
    }
    // public function checkIsMaintenanceAndCalibration()
    // {
    //     $plc = Plc::select(['is_calibration', 'is_maintenance', 'd_off'])->find(1);
    //     if ($plc->is_calibration == 1 || $plc->is_maintenance == 1) {
    //         return $this->calibrationAndMaintenance();
    //     }
    //     return false;
    // }
    public function sendQuery($d, $data)
    {
        $connect = $this->modbus->sendQuery(1, 5, "000$d", $data, true);
        $this->isConnect = $connect;
        if (!$this->isConnect) {
            $this->connectDevice();
        }
    }

    public function runCalAndMaintenance()
    {
        try {
            $plc = Plc::first();
            if ($plc->is_calibration == 1) {
                $plc = Plc::first();
                $config = Configuration::first();
                if ($plc->d_off == 0) {
                    foreach ($this->calibrationSteps as $step) {
                        if (@$step['type'] == "sampling" || @$step['type'] == "blowback") { // Check is sampling or blowback
                            if ($step['type'] == "sampling") {
                                $sleep = @$plc->sleep_sampling;
                                $loop = @$plc->loop_sampling;
                            } else {
                                $sleep = @$plc->sleep_blowback;
                                $loop = @$plc->loop_blowback;
                            }
                        } else {
                            $sleep = @$plc->sleep_default;
                            $loop = @$step['loop'];
                        }
                        if ($step['d'] === -1) {
                            $this->switchAll($step['data']);
                        } else if ($step['data'] == 'flipflop') {
                            $this->flipFlopCalMt($step['d'], $sleep, $loop);
                        } else {
                            sleep($sleep);
                            $this->sendQuery($step['d'], $step['data']);
                        }
                    }
                    $plc->update(['d_off' => 1]);
                }
                if ($config->is_blowback == 1) {
                    foreach ($this->blowback as $step) {
                        if (@$step['type'] == "sampling" || @$step['type'] == "blowback") { // Check is sampling or blowback
                            if ($step['type'] == "sampling") {
                                $sleep = $plc->sleep_sampling;
                                $loop = $plc->loop_sampling;
                            } else {
                                $sleep = $plc->sleep_blowback;
                                $loop = $plc->loop_blowback;
                            }
                        } else {
                            $sleep = @$plc->sleep_default;
                            $loop = @$step['loop'];
                        }
                        if ($step['d'] === -1) {
                            $this->switchAll($step['data']);
                        } else if ($step['data'] == 'flipflop') {
                            $this->flipFlopCalMt($step['d'], $sleep, $loop);
                        } else {
                            sleep($sleep);
                            $this->sendQuery($step['d'], $step['data']);
                        }
                        print_r($step);
                    }
                    $config->update(['is_blowback' => 0]);
                }
                return true;
            } else if ($plc->is_maintenance == 1) {
                if ($plc->d_off == 0) {
                    foreach ($this->maintenanceSteps as $step) {
                        if (@$step['type'] == "sampling" || @$step['type'] == "blowback") { // Check is sampling or blowback
                            if ($step['type'] == "sampling") {
                                $sleep = $plc->sleep_sampling;
                                $loop = $plc->loop_sampling;
                            } else {
                                $sleep = $plc->sleep_blowback;
                                $loop = $plc->loop_blowback;
                            }
                        } else {
                            $sleep = @$plc->sleep_default;
                            $loop = @$step['loop'];
                        }
                        if ($step['d'] == -1) {
                            $this->switchAll($step['data']);
                        } elseif ($step['data'] == 'flipflop') {
                            $this->flipFlopCalMt($step['d'], $sleep, $loop);
                        } else {
                            $this->sendQuery($step['d'], $step['data']);
                            sleep($sleep);
                        }
                    }
                    $plc->update(['d_off' => 1]);
                }

                for ($i = 0; $i <= 7; $i++) {
                    $field = "d$i";
                    $d = $plc->$field;
                    $step = ['d' => $i, 'data' => ($d == 1 ? 'FF00' : '0000'), 'sleep' => 1];
                    $this->sendQuery($step['d'], $step['data']);
                }
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            echo "error => $e";
        }
    }

    public function runPLC($steps)
    {
        foreach ($steps as $step) {
            $plc = Plc::first();
            if (@$step['type'] == "sampling" || @$step['type'] == "blowback") { // Check is sampling or blowback
                if ($step['type'] == "sampling") {
                    $sleep = $plc->sleep_sampling;
                    $loop = $plc->loop_sampling;
                } else {
                    $sleep = $plc->sleep_blowback;
                    $loop = $plc->loop_blowback;
                }
            } else {
                $sleep = @$plc->sleep_default;
                $loop = @$step['loop'];
            }
            if ($step['d'] === -1) { // All D. D0, D1, D2, D3, D4, D5, D6, D7
                if ($this->runCalAndMaintenance() == false) {
                    $this->switchAll($step['data']);
                }
            } else if ($step['data'] == 'flipflop') {
                if ($this->runCalAndMaintenance() == false) {
                    $this->flipFlop($step['d'], $sleep, $loop);
                }
            } else {
                if ($this->runCalAndMaintenance() == false) {
                    sleep($sleep);
                    $this->sendQuery($step['d'], $step['data']);
                }
            }
        }
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
        $this->blowback = [
            ['d' => -1, 'data' => '0000', 'sleep' => $timer],
            ['d' => 1, 'data' => 'FF00', 'sleep' => $timer],
            ['d' => 5, 'data' => 'flipflop', 'sleep' => $timer, 'loop' => 2, 'type' => 'blowback'], //blowback
            ['d' => 3, 'data' => 'FF00', 'sleep' => $timer],
            ['d' => 6, 'data' => 'flipflop', 'sleep' => $timer, 'loop' => 2, 'type' => 'blowback'], //blowback
            ['d' => -1, 'data' => '0000', 'sleep' => $timer],
            ['d' => 7, 'data' => 'FF00', 'sleep' => $timer],
        ];
        $this->runPLC($initStep);
        sleep(5);
        $this->runPLC($startStep);
        while (true) {
            $this->runPLC($steps);
        }
    }
}
