<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ObatModel;

class ObatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ObatModel::create([
            'nama' => 'Paracetamol',
            'harga_jual' => 5000,
            'stok' => 10,
            'min_stok' => 5,
            'foto' => 'https://images.unsplash.com/photo-1455970022149-a8f26b6902dd?q=80&w=2608&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
            'deskripsi' => 'Obat untuk mengatasi demam',
            'kategori_id' => 1,
            'id_vendor' => 1
        ]);

        ObatModel::create([
            'nama' => 'Aspirin',
            'harga_jual' => 5000,
            'stok' => 10,
            'min_stok' => 5,
            'foto' => 'https://images.unsplash.com/photo-1455970022149-a8f26b6902dd?q=80&w=2608&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
            'deskripsi' => 'Obat untuk mengatasi demam',
            'kategori_id' => 2,
            'id_vendor' => 2
        ]);

        ObatModel::create([
            'nama' => 'Antihistamin',
            'harga_jual' => 5000,
            'stok' => 10,
            'min_stok' => 5,
            'foto' => 'https://images.unsplash.com/photo-1455970022149-a8f26b6902dd?q=80&w=2608&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
            'deskripsi' => 'Obat untuk mengatasi demam',
            'kategori_id' => 3,
            'id_vendor' => 3
        ]);

        ObatModel::create([
            'nama' => 'detoks',
            'harga_jual' => 5000,
            'stok' => 10,
            'min_stok' => 5,
            'foto' => 'https://images.unsplash.com/photo-1455970022149-a8f26b6902dd?q=80&w=2608&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
            'deskripsi' => 'Obat untuk mengatasi demam',
            'kategori_id' => 4,
            'id_vendor' => 4
        ]);
    }
}
