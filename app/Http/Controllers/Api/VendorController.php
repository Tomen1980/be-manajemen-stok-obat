<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\VendorModel;


class VendorController extends Controller
{
    public function index(Request $request)
    {
        try{
            if($request->get('search')){
                $search = $request->get('search');
                $data = VendorModel::where('nama', 'LIKE', '%'.$search.'%')->orWhere('no_telp', 'LIKE', '%'.$search.'%')->orWhere('alamat', 'LIKE', '%'.$search.'%')->paginate(12);
                
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

            $data = VendorModel::paginate(12);
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
            $data = VendorModel::find($id);
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
            'no_telp' => 'required|min:10|max:15',
            'alamat' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()
            ]);
        }
        try{
            $data = VendorModel::create([
                'nama' => $request->nama,
                'no_telp' => $request->no_telp,
                'alamat' => $request->alamat
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
            'no_telp' => 'required|min:10|max:15',
            'alamat' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()
            ]);
        }
        try{
            $data = VendorModel::find($id);
            if (!$data) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data not found'
                ]);
            }
            $data->update([
                'nama' => $request->nama,
                'no_telp' => $request->no_telp,
                'alamat' => $request->alamat
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
            $data = VendorModel::find($id);
            if(!$data){
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
