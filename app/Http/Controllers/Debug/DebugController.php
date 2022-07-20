<?php

namespace App\Http\Controllers\Debug;
use App\Http\Controllers\Controller;
use App\Models\Configuration;
use App\Models\Plc;

class DebugController extends Controller
{
    public function plc(){
        return view('debug.plc');
    }
    public function getPLC(){
        $plc = Plc::find(1);
        $config = Configuration::select('is_blowback')->find(1);
        $plc->is_blowback = $config->is_blowback;
        return response()->json(['success' => true, 'data' => $plc]);
    }
}
