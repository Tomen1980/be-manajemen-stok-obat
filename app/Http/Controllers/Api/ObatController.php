<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\ObatModel;
use App\Models\ObatDetailModel;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ObatController extends Controller
{
    public function index(Request $request)
    {
        try{
            if($request->get('search') ||$request->get('kategori')){

                $search = $request->get('search');
                $kategori = $request->get('kategori');

                $data = ObatModel::with(['kategori', 'vendor']) // Memuat relasi kategori dan vendor
                    ->when($search, function ($query, $search) {
                        $query->where(function ($query) use ($search) {
                            $query->where('nama', 'LIKE', '%' . $search . '%') // Pencarian di kolom obat.nama
                                ->orWhereHas('vendor', function ($query) use ($search) { // Pencarian di relasi vendor.nama
                                    $query->where('nama', 'LIKE', '%' . $search . '%');
                                });
                        });
                    })
                    ->when($kategori, function ($query, $kategori) {
                        $query->where(function ($query) use ($kategori) {
                            // Periksa apakah $kategori adalah angka
                            if (is_numeric($kategori)) {
                                $query->where('kategori_id', $kategori); // Filter berdasarkan kategori_id
                            } else {
                                $query->orWhereHas('kategori', function ($query) use ($kategori) { // Filter berdasarkan nama kategori
                                    $query->where('nama', 'LIKE', '%' . $kategori . '%');
                                });
                            }
                        });
                    })

                    ->orderBy('id', 'desc') // Mengurutkan berdasarkan ID
                    ->paginate(12); 
                
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

            // $data = ObatModel::all();
            $data = ObatModel::with(['kategori', 'vendor'])->paginate(12);
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
            $data = ObatModel::with(['kategori', 'vendor'])->find($id);
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
            'nama' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'foto' => 'required|string',
            'kategori_id' => 'required|numeric',
            'id_vendor' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()
            ]);
        }

        try{
            $data = ObatModel::create([
                'nama' => $request->nama,
                'deskripsi' => $request->deskripsi,
                'foto' => $request->foto,
                'kategori_id' => $request->kategori_id, 
                'id_vendor' => $request->id_vendor
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
            'nama' => 'required|string|max:255',
            'min_stok' => 'required|numeric',
            'harga_jual' => 'required|numeric',
            'deskripsi' => 'required|string',
            'foto' => 'required|string',
            'kategori_id' => 'required|numeric',
            'id_vendor' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()
            ]);
        }

        try{
            $data = ObatModel::find($id);
            if (!$data) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data not found'
                ]);
            }
            $data->update([
                'nama' => $request->nama,
                'min_stok' => $request->min_stok,
                'harga_jual' => $request->harga_jual,
                'deskripsi' => $request->deskripsi,
                'foto' => $request->foto,
                'kategori_id' => $request->kategori_id, 
                'id_vendor' => $request->id_vendor
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
            $data = ObatModel::find($id);
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
    
    public function cekRestokObat(){
        try{
           $data = ObatModel::whereColumn('stok', '<=', 'min_stok')->paginate(12);
            if(!$data){
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

    // public function detailObat($id){
    //     try{
    //         $data = ObatDetailModel::where('id_obat', $id)->get();
    //         if (!$data) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Data not found'
    //             ]);
    //         }
    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Success get data',
    //             'data' => $data
    //         ]);
    //     }catch(Exception $e){
    //         return response()->json([
    //             'success' => false,
    //             'message' => $e
    //         ]);
    //     }
    // }

    
    public function detailObat($id){
        try {
            $data = ObatDetailModel::where('id_obat', $id)->where('status', 'lunas')->get();
    
            if ($data->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data not found',
                ]);
            }
    
            // Tambahkan status ke setiap item
            $data = $data->map(function ($item) {
                $currentDate = Carbon::now();
                $expiryDate = Carbon::parse($item->tgl_kadaluwarsa);
    
                $differenceInMonths = $currentDate->diffInMonths($expiryDate, false);
    
                if ($differenceInMonths >= 12) {
                    $item->info = 'Masih baik';
                } elseif ($differenceInMonths >= 6) {
                    $item->info = 'Segera dijual';
                } elseif ($differenceInMonths >= 3) {
                    $item->info = 'Hampir kadaluarsa, tidak disarankan dijual';
                } elseif ($differenceInMonths < 0) {
                    $item->info = 'Segera dimusnahkan';
                } else {
                    $item->info = 'Tidak layak jual';
                }
    
                return $item;
            });
    
            return response()->json([
                'success' => true,
                'message' => 'Success get data',
                'data' => $data,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

}
