<?php

namespace Tests\Feature;

use App\Models\Aset;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;

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

    public function test_submit_kedua_ditolak_dan_data_pertama_bertahan(): void
    {
        $p1 = User::factory()->create(['kode_satker' => '000111', 'email' => 'p1@test.com']);
        $p2 = User::factory()->create(['kode_satker' => '000111', 'email' => 'p2@test.com']);
        $aset = Aset::create([
            'kode_satker' => '000111',
            'kode_barang' => '1.2.3.04.05.006',
            'nup' => 1,
            'nama_barang' => 'Laptop',
            'kondisi' => null,
        ]);

        $this->actingAs($p1)->post('/asets/' . $aset->id . '/opname', [
            'kondisi' => 'Baik',
            'status_penggunaan' => 'Digunakan Sendiri untuk Operasional',
        ])->assertSessionHas('success');

        $this->actingAs($p2)->post('/asets/' . $aset->id . '/opname', [
            'kondisi' => 'Rusak Berat',
            'status_penggunaan' => 'Tidak Digunakan / Idle',
        ])->assertSessionHas('error');

        $this->assertEquals('Baik', $aset->fresh()->kondisi);
    }

    public function test_celah4_baris_salah_dilewati_dilaporkan_dengan_nomor_baris(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'kode_satker' => '033041']);

        $baris = ['kode_satker,kode_barang,nup,nama_barang'];
        for ($i = 1; $i <= 50; $i++) {
            $baris[] = "033041,BRG{$i},{$i},Barang {$i}";
        }
        $baris[5]  = '033041,,5,Barang tanpa kode';
        $baris[10] = '033041,BRG10,ABC,Barang nup salah';
        $baris[15] = '033042,BRG15,15,Barang satker lain';
        $baris[20] = ',BRG20,20,Barang tanpa satker';
        $baris[25] = '033041,BRG1,1,Duplikat baris 2';

        $path = tempnam(sys_get_temp_dir(), 'impor') . '.csv';
        file_put_contents($path, implode("\n", $baris));

        $this->actingAs($admin)->post('/admin/impor', [
            'file_excel' => new UploadedFile($path, 'aset.csv', 'text/csv', null, true),
        ])->assertRedirect('/asets');

        $this->assertEquals(45, Aset::count());
        $gagal = session('impor_gagal');
        $this->assertCount(5, $gagal);
        $this->assertEquals(
            [6, 11, 16, 21, 26],
            collect($gagal)->pluck('baris')->sort()->values()->all()
        );

        @unlink($path);
    }

    public function test_celah4_impor_ulang_tidak_menggandakan_dan_tidak_menimpa_opname(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'kode_satker' => '033041']);
        $csv = "kode_satker,kode_barang,nup,nama_barang\n033041,BRG1,1,Meja\n033041,BRG2,2,Kursi";

        $kirim = function () use ($admin, $csv) {
            $path = tempnam(sys_get_temp_dir(), 'impor') . '.csv';
            file_put_contents($path, $csv);
            $r = $this->actingAs($admin)->post('/admin/impor', [
                'file_excel' => new UploadedFile($path, 'aset.csv', 'text/csv', null, true),
            ]);
            @unlink($path);
            return $r;
        };

        $kirim()->assertRedirect('/asets');
        $this->assertEquals(2, Aset::count());

        $aset = Aset::where('kode_barang', 'BRG1')->first();
        $aset->update(['kondisi' => 'Baik', 'status_penggunaan' => 'Digunakan Sendiri untuk Operasional']);

        $kirim()->assertRedirect('/asets');
        $this->assertEquals(2, Aset::count());
        $this->assertEquals('Baik', $aset->fresh()->kondisi);
    }

    public function test_selesaikan_opname_atomik_menolak_update_kedua_pada_objek_stale(): void
    {
        $aset = Aset::create([
            'kode_satker' => '000111',
            'kode_barang' => '1.2.3.04.05.006',
            'nup'         => 1,
            'nama_barang' => 'Laptop',
            'kondisi'     => null,
        ]);

        // Ambil baris yang sama dua kali sebagai dua objek terpisah di memori
        // (Keduanya sama-sama melihat kondisi === null pada saat diambil)
        $objekPetugas1 = Aset::find($aset->id);
        $objekPetugas2 = Aset::find($aset->id);

        $this->assertTrue($objekPetugas1->selesaikanOpname([
            'kondisi'           => 'Baik',
            'status_penggunaan' => 'Digunakan Sendiri untuk Operasional',
            'catatan'           => null,
        ]));

        $this->assertFalse($objekPetugas2->selesaikanOpname([
            'kondisi'           => 'Rusak Berat',
            'status_penggunaan' => 'Tidak Digunakan / Idle',
            'catatan'           => null,
        ]));

        $this->assertEquals('Baik', $aset->fresh()->kondisi);
    }
}
