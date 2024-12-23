<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\TransaksiModel;
use App\Models\ObatModel;
use App\Models\ObatDetailModel;
use App\Models\TransaksiItemModel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class GeneralActionTransaksiController extends Controller
{

    public function hapusTransaksi($id){
        try{
            $data = TransaksiModel::find($id);
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

    public function hapusTransaksiItem($id){
        try{
            $data = TransaksiItemModel::with(['ObatDetail','ObatDetail.Obat'])->find($id);
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

    public function hapusDetailObat($id){
        try{
            $data = ObatDetailModel::find($id);
            $obat = ObatModel::find($data->id_obat);
            $obat->stok = $obat->stok - $data->stok;
            $obat->save();
            $data->delete();
            return response()->json([
                'success' => true,
                'message' => 'Success delete data',
                'data' => $data
            ]);

            if(is_empty($data)){
                return response()->json([
                    'success' => false,
                    'message' => 'Data not found'
                ]);
            }
        }catch(Exception $e){
            return response()->json([
                'success' => false,
                'message' => $e
            ]);
        }
    }

    public function updateStatusTransaksi($id)
    {
        try {
            // Temukan transaksi dengan item terkait dan detail obat
            $data = TransaksiModel::with(['TransaksiItem', 'TransaksiItem.ObatDetail', 'TransaksiItem.ObatDetail.Obat'])->find($id);
    
            // Jika data tidak ditemukan, kirimkan respon error
            if (!$data) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data not found'
                ]);
            }

            if($data->status == 'selesai'){
                return response()->json([
                    'success' => false,
                    'message' => 'Transaksi sudah selesai'
                ]);
            }
            if($data->tipe == 'penjualan'){


                // kurangi stok obat pada detail obat
                foreach ($data->TransaksiItem as $item) {
                    $detailObat = ObatDetailModel::find($item->id_obat_detail);
                    if($detailObat->stok < $item->jumlah){
                        return response()->json([
                            'success' => false,
                            'message' => 'Stok obat tidak mencukupi'
                        ]);
                    }
                    $detailObat->stok = $detailObat->stok - $item->jumlah;
                    $detailObat->save();
                    $obat = ObatModel::find($item->ObatDetail->id_obat);
                    $obat->stok = $obat->stok - $item->jumlah;
                    $obat->save();

                        // Perbarui status transaksi menjadi selesai
                    $data->update([
                        'status' => 'selesai'
                    ]);
                }
    
                return response()->json([
                    'success' => true,
                    'message' => 'Success update transaction pembelian',
                ]);
            }

           

            // Iterasi setiap item transaksi
            foreach ($data->TransaksiItem as $item) {
                $detailObat = ObatDetailModel::find($item->id_obat_detail);
                $detailObat->status = 'lunas';
                $detailObat->save();
                $obat = ObatModel::find($item->ObatDetail->id_obat);
                $obat->stok = $obat->stok + $item->jumlah;
                $obat->save();
            }
    
            // Perbarui status transaksi menjadi selesai
            $data->update([
                'status' => 'selesai'
            ]);
    
            return response()->json([
                'success' => true,
                'message' => 'Success update transaction and update stock',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    

    public function generateInvoiceById(Request $request){

    $validator = Validator::make($request->all(), [
        'id_transaksi' => 'required|numeric',
    ]);
    try{
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()
            ]);
        }
        $id = $request->id_transaksi;
    
        $transaksi = TransaksiModel::with(['TransaksiItem','TransaksiItem.ObatDetail', 'TransaksiItem.ObatDetail.Obat'])->where('id', $id)->get();
        if($transaksi[0]->status !== 'selesai'){
            return response()->json([
                'success' => false,
                'message' => 'Selesaikan transaksi terlebih dahulu'
            ]);
        }

        if($transaksi->isEmpty()){
            return response()->json([
                'success' => false,
                'message' => 'transaksi not found'
            ]);
        }
    
        if(!empty($transaksi[0]->pasien)){
            $title = 'Transaksi Pembelian Obat '.$transaksi[0]->pasien->nama;
        }else{
            $title = 'Transaksi Pembelian Obat Klinik';
        }
    
        $formatFile = 'document-'.$title.'-'.$transaksi[0]->tanggal.'.pdf';
    
        $pdf = Pdf::loadView('pdf.invoiceById',[
            'title' => $title,
            'transaksi' => $transaksi[0]
        ]);

        return $pdf->download($formatFile);
        
    }catch(Exception $e){
        return response()->json([
            'success' => false,
            'message' => $e
        ]);
    }
    
}


    public function generateInvoiceAll(Request $request)
{
    try {
        $query = TransaksiModel::with(['TransaksiItem', 'TransaksiItem.ObatDetail', 'TransaksiItem.ObatDetail.Obat'])
            ->where('status', 'selesai');

        // Filter kategori pembelian/penjualan
        if ($request->has('kategori') && !empty($request->kategori)) {
            $query->where('tipe', $request->kategori); // 'tipe' adalah kolom untuk kategori
        }

        // Filter berdasarkan tanggal
        if ($request->has('tanggal_awal') && $request->has('tanggal_akhir')) {
            $tanggalAwal = $request->tanggal_awal;
            $tanggalAkhir = $request->tanggal_akhir;

            if ($tanggalAwal && $tanggalAkhir) {
                $query->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir]);
            }
        }

        // Ambil hasil query
        $transaksi = $query->get();

        // Validasi jika data tidak ditemukan
        if ($transaksi->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi not found',
            ]);
        }

        // Generate PDF
        $title = 'Transaksi Seluruh Obat Klinik';
        $formatFile = 'document-' . $title . '.pdf';

        $pdf = Pdf::loadView('pdf.invoiceAll', [
            'title' => $title,
            'transaksi' => $transaksi,
        ]);

        return $pdf->download($formatFile);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
        ]);
    }
}


    public function getTransaksi(Request $request){
            try{

                if($request->get('search') || $request->get('kategori') || $request->get('status')){
                    $search = $request->get('search');
                    $kategori = $request->get('kategori');  
                    $status = $request->get('status');
                    
                    $transaksi = TransaksiModel::with(['TransaksiItem', 'User', 'Pasien', 'TransaksiItem.ObatDetail', 'TransaksiItem.ObatDetail.Obat'])
                    ->when($kategori, function ($query, $kategori) {
                        return $query->where('tipe', $kategori);
                    })
                    ->when($status, function ($query, $status) {
                        return $query->where('status', $status);
                    })
                    ->when($search, function ($query, $search) {
                        return $query->where(function ($query) use ($search) {
                            $query->where('deskripsi', 'LIKE', '%' . $search . '%')
                                  ->orWhere('tanggal', 'LIKE', '%' . $search . '%');
                        });
                    })
                    ->get();
                   

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

                $transaksi = TransaksiModel::with(['TransaksiItem', 'TransaksiItem.ObatDetail', 'TransaksiItem.ObatDetail.Obat'])->get();
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
    }
