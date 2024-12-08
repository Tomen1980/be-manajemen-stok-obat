<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\TransaksiModel;
use App\Models\TransaksiItemModel;
use App\Models\ObatModel;
use App\Models\ObatDetailModel;

class TransaksiPenjualanController extends Controller
{

    public function index(Request $request)
{
    try {
        // Ambil parameter search dari request
        $search = $request->get('search');

        if ($search) {
            $transaksi = TransaksiModel::with([
                'TransaksiItem',
                'Pasien' => function ($query) use ($search) { // Pastikan $search tersedia dalam closure
                    $query->select('id', 'nama')
                          ->where('id', 'LIKE', '%' . $search . '%')
                          ->orWhere('nama', 'LIKE', '%' . $search . '%');
                },
                'TransaksiItem.ObatDetail' => function ($query) {
                    $query->select('id', 'tgl_kadaluwarsa', 'id_obat');
                },
                'TransaksiItem.ObatDetail.Obat' => function ($query) {
                    $query->select('id', 'nama', 'harga_jual');
                }
            ])
            ->where('tipe', 'penjualan')
            ->where(function ($query) use ($search) {
                $query->where('deskripsi', 'LIKE', '%' . $search . '%')
                      ->orWhere('tanggal', 'LIKE', '%' . $search . '%');
            })
            ->paginate(12);

            if ($transaksi->isEmpty()) {
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

        // Jika tidak ada parameter search
        $transaksi = TransaksiModel::with([
            'TransaksiItem',
            'Pasien' => function ($query) {
                $query->select('id', 'nama');
            },
            'TransaksiItem.ObatDetail' => function ($query) {
                $query->select('id', 'tgl_kadaluwarsa', 'id_obat');
            },
            'TransaksiItem.ObatDetail.Obat' => function ($query) {
                $query->select('id', 'nama', 'harga_jual','stok');
            }
        ])
        ->where('tipe', 'penjualan')
        ->paginate(12);

        return response()->json([
            'success' => true,
            'message' => 'Success get data',
            'data' => $transaksi
        ]);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}



    public function show($id){
        try{
            $transaksi = TransaksiModel::with([
                'TransaksiItem',
                'TransaksiItem.ObatDetail' => function ($query) {
                    $query->select('id','tgl_kadaluwarsa','id_obat');
                }, 
                'TransaksiItem.ObatDetail.Obat' => function ($query) {
                    $query->select('id','nama','harga_jual');
                }
            ])->where('tipe', 'penjualan')->find($id);
    
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
            'id_pasien' => 'required',
            'deskripsi' => 'required|min:3|max:255',
            'tanggal' => 'required|date',
            'id_detail_obat' => 'required',
            'jumlah' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()
            ]);
        }
        
        $detailObat = ObatDetailModel::with('obat')->find($request->id_detail_obat);
        if(!$detailObat) {
            return response()->json([
                'success' => false,
                'message' => 'Detail obat tidak ditemukan'
            ]);
        }
        if($detailObat->stok < $request->jumlah || $detailObat->Obat->stok == 0){
            return response()->json([
                'success' => false,
                'message' => 'Stok obat tidak mencukupi'
            ]);
        }
        
// membuat transaksi
        $transaksi = TransaksiModel::create([
            'tipe' => 'penjualan',
            'status' => 'proses',
            'deskripsi' => $request->deskripsi,
            'jumlah' => $request->jumlah,
            'tanggal' => $request->tanggal,
            'id_pasien' => $request->id_pasien,
            'total_harga' => 0,
            'id_user' => auth()->user()->id,
        ]);
        // transaksi item

        $transaksiItem = TransaksiItemModel::create([
            'id_transaksi' => $transaksi->id,
            'id_obat_detail' => $request->id_detail_obat,
            'jumlah' => $request->jumlah,
            'total_harga' => $request->jumlah * $detailObat->Obat->harga_jual
        ]);

        // tambahkan jumlah transaksi yang sekarang
        $transaksi = TransaksiModel::find($transaksi->id);
        $transaksi->total_harga = $transaksi->total_harga + $transaksiItem->total_harga;
        $transaksi->save();

        return response()->json([
            'success' => true,
            'message' => 'Transaksi berhasil dibuat',
            'data' => [
                'transaksi' => $transaksi,
                'transaksi_item' => $transaksiItem,
                'obat_detail' => $detailObat
            ]
        ]);            
    }

    public function buatTransaksiPenjualan(Request $request){
        $validator = Validator::make($request->all(), [
            'id_pasien' => 'required',
            'deskripsi' => 'required|min:3|max:255',
            'tanggal' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()
            ]);
        }

        try{
            $transaksi = new TransaksiModel();
            $transaksi->tipe = 'penjualan';
            $transaksi->status = 'proses';
            $transaksi->deskripsi = $request->deskripsi;
            $transaksi->tanggal= $request->tanggal;
            $transaksi->total_harga = 0;
            $transaksi->id_pasien = $request->id_pasien;
            $transaksi->id_user = auth()->user()->id;
            $transaksi->save();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil dibuat',
                'data' => $transaksi
            ]);
        }catch(\Exception $e){
            return response()->json([
                'success' => false,
                'message' => $e
            ]);
        }
    }

    public function tambahTransaksiPenjualanObat(Request $request){
        $validator = Validator::make($request->all(), [
            "id_transaksi" => "required|numeric",
            "id_detail_obat" => "required",
            "jumlah" => "required|numeric",
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()
            ]);
        }
        try{
            
            $transaksi = TransaksiModel::find($request->id_transaksi);
            if($transaksi->status == 'selesai'){
                return response()->json([
                    'success' => false,
                    'message' => 'Transaksi sudah selesai'
                ]);
            }

            $detailObat = ObatDetailModel::with('obat')->find($request->id_detail_obat);
            if($detailObat->stok < $request->jumlah || $detailObat->Obat->stok == 0){
                return response()->json([
                    'success' => false,
                    'message' => 'Stok obat tidak mencukupi'
                ]);
            }
            $transaksiItem = TransaksiItemModel::create([
                'id_transaksi' => $request->id_transaksi,
                'id_obat_detail' => $request->id_detail_obat,
                'jumlah' => $request->jumlah,
                'total_harga' => $request->jumlah * $detailObat->Obat->harga_jual
            ]);
            // tambahkan jumlah transaksi yang sekarang
            $transaksi->total_harga = $transaksi->total_harga + $transaksiItem->total_harga;
            $transaksi->save();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil dibuat',
                'data' => [
                    'transaksi' => $transaksi,
                    'transaksi_item' => $transaksiItem,
                    'obat_detail' => $detailObat
                ]
            ]);

        }catch(\Exception $e){
            return response()->json([
                'success' => false,
                'message' => $e
            ]);
        }
       
    }

    public function updateTransaksiPenjualanObat(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'jumlah' => 'required|numeric',
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

            $transaksi = TransaksiModel::find($item->id_transaksi);
            if($transaksi->status == 'selesai'){
                return response()->json([
                    'success' => false,
                    'message' => 'Transaksi sudah selesai'
                ]);
            }

            $detailObat = ObatDetailModel::with('obat')->find($item->id_obat_detail);

            if($detailObat->stok < $request->jumlah || $detailObat->Obat->stok == 0){
                return response()->json([
                    'success' => false,
                    'message' => 'Stok obat tidak mencukupi'
                ]);
            }

            // Kurangi total harga dengan item lama
            $transaksi->total_harga = $transaksi->total_harga - $item->total_harga;
            $transaksi->save();

             // Update Item baru
             $item->total_harga =  $request->jumlah * $detailObat->Obat->harga_jual;
             $item->jumlah = $request->jumlah;
             $item->save();

             // tambahkan jumlah transaksi yang sekarang
             $transaksi->total_harga = $transaksi->total_harga + $item->total_harga;
             $transaksi->save();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil diupdate',
                'data' => [
                    'transaksi' => $transaksi,
                    'transaksi_item' => $item,
                    'obat_detail' => $detailObat
                ]
            ]);

        }catch(\Exception $e){
            return response()->json([
                'success' => false,
                'message' => $e
            ]);
        }
    }

}
