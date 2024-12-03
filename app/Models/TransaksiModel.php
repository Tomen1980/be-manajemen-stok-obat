<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class TransaksiModel extends Model
{
    use HasFactory;

    protected $table = 'transaksi';

    protected $fillable = ['id','tipe', 'status', 'deskripsi', 'total_harga', 'tanggal', 'id_pasien', 'id_user'];

    public $timestamps = true;

    public function TransaksiItem(){
        return $this->hasMany(TransaksiItemModel::class, 'id_transaksi', 'id');
    }

    public function User(){
        return $this->belongsTo(User::class, 'id_user', 'id');
    }

    public function Pasien(){
        return $this->belongsTo(PasienModel::class, 'id_pasien', 'id');
    }
}
