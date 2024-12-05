<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\TransaksiModel;

class TransaksiPenjualanController extends Controller
{
    
    
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'id_pasien' => 'required',
            'id_vendor' => 'required',
            'id_user' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()
            ]);
        }
    }
}
