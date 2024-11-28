<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\PasienModel;

class PasienController extends Controller
{
    public function index(Request $request)
    {
        try{
            if($request->get('search')){
                $search = $request->get('search');
                $data = PasienModel::where('nama', 'ILIKE', '%'.$search.'%')->orWhere('no_telp', 'ILIKE', '%'.$search.'%')->orWhere('tgl_lahir', 'ILIKE', '%'.$search.'%')->paginate(12);
                
                if($data->isEmpty()){
                    return response()->json([
                        'success' => false,
                        'message' => 'Data not found'
                    ]);
                }
                return response()->json([
                    'success' => true,
                    'message' => 'Success get all data',
                    'data' => $data
                ]);
            }

            $data = PasienModel::paginate(12);
            return response()->json([
                'success' => true,
                'message' => 'Success get all data',
                'data' => $data
            ]);
        }catch(Exception $e){
            return response()->json([
                'success' => false,
                'message' => $e
            ]);
        }
    }

    public function show($id){
        try{
            $data = PasienModel::find($id);
            if (!$data) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data not found'
                ]);
            }
            return response()->json([
                'success' => true,
                'message' => 'Success get data',
                'data' => $data
            ]);
        }catch(Exception $e){
            return response()->json([
                'success' => false,
                'message' => $e
            ]);
        }
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'nama' => 'required|min:3|max:255',
            'tgl_lahir' => 'required|date',
            'no_telp' => 'required|min:10|max:15',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()
            ]);
        }
        try{
            $data = PasienModel::create([
                'nama' => $request->nama,
                'tgl_lahir' => $request->tgl_lahir,
                'no_telp' => $request->no_telp
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Success create data',
                'data' => $data
            ]);
        }catch(Exception $e){
            return response()->json([
                'success' => false,
                'message' => $e
            ]);
        }
    }

    public function update(Request $request, $id){

        $validator = Validator::make($request->all(), [
            'nama' => 'required|min:3|max:255',
            'tgl_lahir' => 'required|date',
            'no_telp' => 'required|min:10|max:15',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()
            ]);
        }

        try{
            $data = PasienModel::find($id);
            if (!$data) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data not found'
                ]);
            }
            $data->update([
                'nama' => $request->nama,
                'tgl_lahir' => $request->tgl_lahir,
                'no_telp' => $request->no_telp
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Success update data',
                'data' => $data
            ]);
        }catch(Exception $e){
            return response()->json([
                'success' => false,
                'message' => $e
            ]);
        }
    }

    public function destroy($id){
        try{
            $data = PasienModel::find($id);
            if (!$data) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data not found'
                ]);
            }
            $data->delete();
            return response()->json([
                'success' => true,
                'message' => 'Success delete data',
                'data' => $data
            ]);
        }catch(Exception $e){
            return response()->json([
                'success' => false,
                'message' => $e
            ]);
        }
    }


}
