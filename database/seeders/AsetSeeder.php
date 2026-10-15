<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Aset;

class AsetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Siapkan array pilihan
        $satkerList = ['033041', '033042', '033043'];
        $kondisiList = ['Baik', 'Rusak Ringan', 'Rusak Berat', null];
        $barangList = ['Laptop Operasional', 'Printer Epson', 'Motor Dinas', 'Pompa Air', 'Drone Pemetaan'];

        // 2. Lakukan perulangan sebanyak 25 kali
        for ($i = 1; $i <= 25; $i++) {
            Aset::create([
                // Mengambil nilai acak dari array menggunakan Faker bawaan Laravel
                'kode_satker' => fake()->randomElement($satkerList),

                // Membuat kode barang dummy berupa angka acak (misal: 30201234)
                'kode_barang' => fake()->numerify('3020####'),

                // Menggunakan nilai $i (1 sampai 25) sebagai NUP
                'nup' => $i,
                'nama_barang' => fake()->randomElement($barangList),
                'kondisi' => fake()->randomElement($kondisiList),
            ]);
        }
    }
}
