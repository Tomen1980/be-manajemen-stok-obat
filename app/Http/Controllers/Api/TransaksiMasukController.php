<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\TransaksiModel;
use App\Models\TransaksiItemModel;
use App\Models\ObatDetailModel;

class TransaksiMasukController extends Controller
{


    public function index(Request $request){
        try{
            if($request->get('search')) {
                $search = $request->get('search');
                $transaksi = TransaksiModel::with(['TransaksiItem','User','Pasien', 'TransaksiItem.ObatDetail', 'TransaksiItem.ObatDetail.Obat'])
                ->where('tipe', 'pembelian')
                ->Where('deskripsi', 'LIKE', '%'.$search.'%')
                ->orWhere('tanggal', 'LIKE', '%'.$search.'%')
                ->paginate(12);

                if($transaksi->isEmpty()){
                    return response()->json([
                        'success' => false,
                        'message' => 'Data not found'
                    ]);
                }
                    return response()->json([
                        'success' => true,
                        'message' => 'Success get data',
                        'data' => $transaksi
                    ]);
                
            }
            $transaksi = TransaksiModel::with(['TransaksiItem','User','Pasien','TransaksiItem.ObatDetail', 'TransaksiItem.ObatDetail.Obat'])->where('tipe', 'pembelian')->paginate(12);
            return response()->json([
                'success' => true,
                'message' => 'Success get data',
                'data' => $transaksi
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
            $transaksi = TransaksiModel::with(['TransaksiItem','TransaksiItem.ObatDetail', 'TransaksiItem.ObatDetail.Obat'])->where('tipe', 'pembelian')->where('id', $id)->get();
            if( $transaksi->isEmpty() ) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data not found'
                ]);
            }
            return response()->json([
                'success' => true,
                'message' => 'Success get data',
                'data' => $transaksi
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
            "deskripsi" => "required|string",
            "tanggal_transaksi" => "required|date",
            "id_obat" => "required|numeric",
            "stok" => "required|numeric",
            "harga_beli_unit" => "required|numeric",
            "tgl_kadaluwarsa" => "required|date",
            "tgl_masuk" => "required|date",
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()
            ]);
        }

        try{
            // Membuat Transaksi
            $transaksi = new TransaksiModel();
            $transaksi->tipe = 'pembelian';
            $transaksi->status = 'proses';
            $transaksi->deskripsi = $request->deskripsi;
            $transaksi->tanggal= $request->tanggal_transaksi;
            $transaksi->total_harga = 0;
            $transaksi->id_user = auth()->user()->id;
            $transaksi->save();

            // Memilih transaksi Obat
            $obat = new ObatDetailModel();
            $obat->id_obat = $request->id_obat;
            $obat->stok = $request->stok;
            $obat->harga_beli_unit = $request->harga_beli_unit;
            $obat->tgl_kadaluwarsa = $request->tgl_kadaluwarsa;
            $obat->tgl_masuk = $request->tgl_masuk;
            $obat->save();

            // Masuk ke Item Transaksi
            $item = new TransaksiItemModel();
            $item->id_transaksi = $transaksi->id;
            $item->id_obat_detail = $obat->id;
            $item->jumlah = $request->stok;
            $item->total_harga = $obat->harga_beli_unit * $request->stok;
            $item->save();

            // update transaksi
            $transaksi->total_harga = $transaksi->total_harga + $item->total_harga;
            $transaksi->save();


            return response()->json([
                'success' => true,
                'message' => 'Success create data',
                'data' => [
                    "transaksi" => $transaksi,
                    "Transaksi_Item" => $item,
                    "Obat_Detail" => $obat
                ]
            ]);


        }catch(Exception $e){
            return response()->json([
                'success' => false,
                'message' => $e
            ]);
        }
    }

    public function buatTransaksiMasuk(Request $request){
        $validator = Validator::make($request->all(), [
            "deskripsi" => "required|string",
            "tanggal_transaksi" => "required|date",
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()
            ]);
        }


        try{
            // Membuat Transaksi
            $transaksi = new TransaksiModel();
            $transaksi->tipe = 'pembelian';
            $transaksi->status = 'proses';
            $transaksi->deskripsi = $request->deskripsi;
            $transaksi->tanggal= $request->tanggal_transaksi;
            $transaksi->total_harga = 0;
            $transaksi->id_user = auth()->user()->id;
            $transaksi->save();

            return response()->json([
                'success' => true,
                'message' => 'Success create data',
                'data' => [
                    "transaksi" => $transaksi,
                ]
            ]);


        }catch(Exception $e){
            return response()->json([
                'success' => false,
                'message' => $e
            ]);
        }
       
    }

    public function tambahTransaksiMasukObat(Request $request){
        $validator = Validator::make($request->all(), [
            "id_transaksi" => "required|numeric",
            "id_obat" => "required|numeric",
            "stok" => "required|numeric",
            "harga_beli_unit" => "required|numeric",
            "tgl_kadaluwarsa" => "required|date",
            "tgl_masuk" => "required|date",
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()
            ]);
        }

        try{
            // Memilih transaksi Obat
            $obat = new ObatDetailModel();
            $obat->id_obat = $request->id_obat;
            $obat->stok = $request->stok;
            $obat->harga_beli_unit = $request->harga_beli_unit;
            $obat->tgl_kadaluwarsa = $request->tgl_kadaluwarsa;
            $obat->tgl_masuk = $request->tgl_masuk;
            $obat->save();

            // Masuk ke Item Transaksi
            $item = new TransaksiItemModel();
            $item->id_transaksi = $request->id_transaksi;
            $item->id_obat_detail = $obat->id;
            $item->jumlah = $request->stok;
            $item->total_harga = $obat->harga_beli_unit * $request->stok;
            $item->save();

            // update transaksi
            $transaksi = TransaksiModel::find($request->id_transaksi);
            if(!$transaksi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaksi tidak ditemukan'
                ]);
            }
            $transaksi->total_harga = $transaksi->total_harga + $item->total_harga;
            $transaksi->save();

            return response()->json([
                'success' => true,
                'message' => 'Success create data',
                'data' => [
                    "transaksi" => $transaksi,
                    "Transaksi_Item" => $item,
                    "Obat_Detail" => $obat
                ]
            ]);


        }catch(Exception $e){
            return response()->json([
                'success' => false,
                'message' => $e
            ]);
        }
    }

    public function updateTransaksiMasukObat(Request $request , $id){
        $validator = Validator::make($request->all(), [
            "stok" => "required|numeric",
            "harga_beli_unit" => "required|numeric",
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()
            ]);
        }

        try{
            $item = TransaksiItemModel::find($id);
            if(!$item) {
                return response()->json([
                    'success' => false,
                    'message' => 'Item tidak ditemukan'
                ]);
            }
            // Kurangi total harga dengan item lama
            $transaksi = TransaksiModel::find($item->id_transaksi);
            if($transaksi->status == 'selesai'){
                return response()->json([
                    'success' => false,
                    'message' => 'Transaksi sudah selesai'
                ]);
            }
            $transaksi->total_harga = $transaksi->total_harga - $item->total_harga;
            $transaksi->save();

            $obat = ObatDetailModel::find($item->id_obat_detail);
            $obat->stok = $request->stok;
            $obat->harga_beli_unit = $request->harga_beli_unit;
            $obat->save();

            // Update Item baru
            $item->total_harga =  $request->stok * $request->harga_beli_unit;
            $item->jumlah = $request->stok;
            $item->save();

            // Update transaksi dengan harga baru
            $transaksi->total_harga = $transaksi->total_harga + $item->total_harga;
            $transaksi->save();

            return response()->json([
                'success' => true,
                'message' => 'Success update data',
                'data' => [
                    "Transaksi" => $transaksi,
                    "Transaksi_Item" => $item,
                ]
            ]);
        }catch(Exception $e){
            return response()->json([
                'success' => false,
                'message' => $e
            ]);
        }
        
    }
}
