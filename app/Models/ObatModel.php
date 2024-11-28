<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ObatModel extends Model
{
    use HasFactory;

    protected $table = 'obat';

    protected $fillable = [
        'id',
        'nama',
        'stok',
        'min_stok',
        'harga_jual',
        'deskripsi',
        'foto',
        'kategori_id',
        'id_vendor',
    ];

    public $timestamps = true;
}
