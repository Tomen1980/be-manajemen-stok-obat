<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    protected $appends = ['status'];

   

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriModel::class, 'kategori_id', 'id');
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(VendorModel::class, 'id_vendor', 'id');
    }

    public function ObatDetail(){
        return $this->hasMany(ObatDetailModel::class, 'id_obat', 'id');
    }


    public function getStatusAttribute(){
        if ($this->stok <= $this->min_stok) {
            return 'stok perlu di restock';
        } else {
            return 'stok aman';
        }
    }
}
