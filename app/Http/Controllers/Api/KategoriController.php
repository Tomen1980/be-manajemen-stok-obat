<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\KategoriModel;

class KategoriController extends Controller
{
   public function index(){
        try{
            $data = KategoriModel::get();
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

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()
            ]);
        }
        try{
            $data = KategoriModel::create([
                'nama' => $request->nama,
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

    public function show($id){
        try{
            $data = KategoriModel::find($id);

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

    public function update(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()
            ]);
        }
        try{
           $data = KategoriModel::find($id);
           if(!$data){
            return response()->json([
                'success' => false,
                'message' => 'Data not found'
            ]);
           }
           $data->nama = $request->nama;
           $data->save();

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
            $data = KategoriModel::find($id);
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
