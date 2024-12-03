<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class ObatDetailModel extends Model
{
    use HasFactory;

    protected $table = 'obat_detail';

    protected $fillable = ['id','stok','harga_beli_unit','tgl_kadaluwarsa','tgl_masuk','id_obat'];

    public $timestamps = true;

    public function Obat(){
        return $this->belongsTo(ObatModel::class, 'id_obat', 'id');
    }
}
