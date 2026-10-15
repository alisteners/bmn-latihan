<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Membuat Akun Admin
        User::factory()->create([
            'name' => 'Admin BBWS',
            'email' => 'admin@bmn.test',
            'role' => 'admin',
            'kode_satker' => '033041',
        ]);

        // 2. Membuat Akun Petugas
        User::factory()->create([
            'name' => 'Petugas Lapangan',
            'email' => 'petugas@bmn.test',
            'role' => 'petugas',
            'kode_satker' => '033041',
        ]);


        $this->call([
            AsetSeeder::class,
        ]);
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
