<?php

namespace Tests\Feature;

use App\Models\Aset;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AsetOpnameTest extends TestCase
{
    use RefreshDatabase;

    public function test_petugas_tidak_bisa_buka_form_aset_satker_lain(): void
    {
        $user = User::factory()->create(['kode_satker' => '000111']);
        $asetOrangLain = Aset::create([
            'kode_satker' => '000222', // satker lain
            'kode_barang' => '1.2.3.04.05.006',
            'nup' => 1,
            'nama_barang' => 'Laptop',
            'kondisi' => null,
        ]);

        $response = $this->actingAs($user)
            ->get('/asets/' . $asetOrangLain->id . '/opname');

        $response->assertStatus(403);
    }

    public function test_petugas_tidak_bisa_buka_form_aset_yang_sudah_diopname(): void
    {
        $user = User::factory()->create(['kode_satker' => '000111']);
        $asetSelesai = Aset::create([
            'kode_satker' => '000111',
            'kode_barang' => '1.2.3.04.05.006',
            'nup' => 1,
            'nama_barang' => 'Laptop',
            'kondisi' => 'Baik',
        ]);

        $response = $this->actingAs($user)
            ->get('/asets/' . $asetSelesai->id . '/opname');

        $response->assertRedirect('/asets');
        $response->assertSessionHas('error');
    }
}
