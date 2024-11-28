<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorModel extends Model
{
    use HasFactory;

    protected $table = 'vendor';

    protected $fillable = ['id','nama','alamat','no_telp'];

    public $timestamps = true;
}
