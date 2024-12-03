<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\TransaksiModel;
use App\Models\TransaksiItemModel;
use Barryvdh\DomPDF\Facade\Pdf;

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
            $data = TransaksiItemModel::find($id);
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

    public function updateStatusTransaksi( $id){
        try{
            $data = TransaksiModel::find($id);
            if(!$data){
                return response()->json([
                    'success' => false,
                    'message' => 'Data not found'
                ]);
            }

            $data->update([
                'status' => 'selesai'
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
    
        $transaksi = TransaksiModel::with(['TransaksiItem','TransaksiItem.ObatDetail', 'TransaksiItem.ObatDetail.Obat'])->where('tipe', 'pembelian')->where('id', $id)->get();
        
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

    public function generateInvoiceAll(Request $request){
        $transaksi = TransaksiModel::with(['TransaksiItem','TransaksiItem.ObatDetail', 'TransaksiItem.ObatDetail.Obat'])->where('status', 'selesai')->get();
        if($transaksi->isEmpty()){
            return response()->json([
                'success' => false,
                'message' => 'transaksi not found'
            ]);
        }
        $title = 'Transaksi Seluruh Obat Klinik';
        $formatFile = 'document-'.$title.'.pdf';
        $pdf = Pdf::loadView('pdf.invoiceAll',[
            'title' => $title,
            'transaksi' => $transaksi
        ]);
        return $pdf->download($formatFile);
    }
}
