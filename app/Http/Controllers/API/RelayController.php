<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Plc;
use Exception;
use Illuminate\Http\Request;

class RelayController extends Controller
{
    public function index()
    {
        $plc = Plc::find(1);
        return view('relay.index', compact('plc'));
    }

    public function setRelay(Request $request)
    {
        try {
            $config = Plc::first();
            $request = $request->relay_d;
            $field = "d$request";
            $data_old = $config->$field;
            $data = [$field => ($data_old == 1 ? 0 : 1)];
            $config->update($data);
            return response()->json(['success' => true, 'message' => 'Relay Tested']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->response->original]);
        }
    }
}
