<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TransaksiItemModel extends Model
{
    use HasFactory;

    protected $table = 'transaksi_item';

    protected $fillable = ['id','id_obat_detail', 'id_transaksi', 'jumlah', 'total_harga'];

    public $timestamps = true;

    public function ObatDetail(): HasOne
    {
        return $this->hasOne(ObatDetailModel::class, 'id', 'id_obat_detail');
    }

    public function Transaksi(): HasOne
    {
        return $this->hasOne(TransaksiModel::class, 'id', 'id_transaksi');
    }
}
