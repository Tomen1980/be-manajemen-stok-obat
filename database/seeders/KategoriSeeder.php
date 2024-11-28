<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\KategoriModel;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        KategoriModel::create([
            'nama' => 'Obat Bebas'
        ]);
        KategoriModel::create([
            'nama' => 'Obat Bebas Terbatas'
        ]);
        KategoriModel::create([
            'nama' => 'Obat Keras'
        ]);
        KategoriModel::create([
            'nama' => 'Obat Psikotropika dan Narkotika'
        ]);
    }
}
