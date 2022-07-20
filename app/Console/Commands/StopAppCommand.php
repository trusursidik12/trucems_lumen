<?php

namespace App\Console\Commands;

use App\Helper\PhpSerialModbus;
use App\Models\Configuration;
use App\Models\Plc;
use Exception;
use Illuminate\Console\Command;

class StopAppCommand extends Command
{
    protected $signature = 'stop-app';
    protected $description = 'Command for app closed';

    public $modbus;
    public $isConnect;

    public function __construct()
    {
        parent::__construct();
        $this->connectDevice();
    }


    public function handle()
    {
        Plc::find(1)->update([
            'is_calibration' => 0,
            'is_maintenance' => 0,
            'd_off' => 0,
            'd0' => 0,
            'd1' => 0,
            'd2' => 0,
            'd3' => 0,
            'd4' => 0,
            'd5' => 0,
            'd6' => 0,
            'd7' => 0,
        ]);
        Configuration::find(1)->update([
            'is_calibration' => 0,
            'is_blowback' => 0,
            'calibration_type' => 0,
            'target_value' => null
        ]);
        for ($i=0; $i <= 7; $i++) { 
            $this->sendQuery($i,"0000");
        }

    }
    public function sendQuery($d, $data)
    {
        $connect = $this->modbus->sendQuery(1, 5, "000$d", $data, true);
        $this->isConnect = $connect;
        if (!$this->isConnect) {
            $this->connectDevice();
        }
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
}
