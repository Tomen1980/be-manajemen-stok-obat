<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PasienModel extends Model
{
    use HasFactory;

    protected $table = 'pasien';

    protected $fillable = ['id','nama','tgl_lahir','no_telp'];

    public $timestamps = true;


    public function transaksi(){
        return $this->hasMany(TransaksiModel::class, 'id_pasien', 'id');
    }
}
