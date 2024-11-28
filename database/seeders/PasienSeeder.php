<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PasienModel;

class PasienSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PasienModel::create([
            'nama' => 'Budi',
            'tgl_lahir' => '1990-01-01',
            'no_telp' => '08123456789'
        ]);

        PasienModel::create([
            'nama' => 'Andi',
            'tgl_lahir' => '1991-02-02',
            'no_telp' => '08123456790'
        ]);

        PasienModel::create([
            'nama' => 'Cindy',
            'tgl_lahir' => '1992-03-03',
            'no_telp' => '08123456791'
        ]);

        PasienModel::create([
            'nama' => 'Dewi',
            'tgl_lahir' => '1993-04-04',
            'no_telp' => '08123456792'
        ]);
    }
}
