<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\VendorModel;

class VendorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        VendorModel::create([
            'nama' => 'Kimia Farma',
            'alamat' => 'Jl. Kediri No. 10',
            'no_telp' => '08123456789'
        ]);
        
        VendorModel::create([
            'nama' => 'Astra',
            'alamat' => 'Jl. Surabaya No. 20',
            'no_telp' => '08123456789'
        ]);
       
        VendorModel::create([
            'nama' => 'Okta',
            'alamat' => 'Jl. Malang No. 30',
            'no_telp' => '08123456789'
        ]);
        VendorModel::create([
            'nama' => 'sinergi',
            'alamat' => 'Jl. Semarang No. 40',
            'no_telp' => '08123456789'
        ]);
    }
}
