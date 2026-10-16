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

    public function test_update_menolak_kondisi_di_luar_daftar_opsi(): void
    {
        $user = User::factory()->create(['kode_satker' => '000111']);
        $aset = Aset::create([
            'kode_satker' => '000111',
            'kode_barang' => '1.2.3.04.05.006',
            'nup' => 1,
            'nama_barang' => 'Laptop',
            'kondisi' => null,
        ]);

        $response = $this->actingAs($user)->post('/asets/' . $aset->id . '/opname', [
            'kondisi' => 'Hancur',
            'status_penggunaan' => 'Digunakan Sendiri untuk Operasional',
        ]);

        $response->assertSessionHasErrors('kondisi');
        $this->assertNull($aset->fresh()->kondisi); // DB tidak berubah
    }

    public function test_update_menolak_status_penggunaan_di_luar_daftar_opsi(): void
    {
        $user = User::factory()->create(['kode_satker' => '000111']);
        $aset = Aset::create([
            'kode_satker' => '000111',
            'kode_barang' => '1.2.3.04.05.006',
            'nup' => 1,
            'nama_barang' => 'Laptop',
            'kondisi' => null,
        ]);

        $response = $this->actingAs($user)->post('/asets/' . $aset->id . '/opname', [
            'kondisi' => 'Baik',
            'status_penggunaan' => 'Dipinjam Tetangga',
        ]);

        $response->assertSessionHasErrors('status_penggunaan');
        $this->assertNull($aset->fresh()->kondisi);
    }

    public function test_update_dengan_data_valid_tersimpan(): void
    {
        $user = User::factory()->create(['kode_satker' => '000111']);
        $aset = Aset::create([
            'kode_satker' => '000111',
            'kode_barang' => '1.2.3.04.05.006',
            'nup' => 1,
            'nama_barang' => 'Laptop',
            'kondisi' => null,
        ]);

        $response = $this->actingAs($user)->post('/asets/' . $aset->id . '/opname', [
            'kondisi' => 'Rusak Ringan',
            'status_penggunaan' => 'Tidak Digunakan / Idle',
            'catatan' => 'Kaki goyang',
        ]);

        $response->assertRedirect('/asets');
        $this->assertEquals('Rusak Ringan', $aset->fresh()->kondisi);
    }
}
