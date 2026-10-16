<?php

namespace App\Http\Controllers;

use App\Imports\AsetsImport;
use App\Models\Aset;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AsetController extends Controller
{
    public function index(Request $request)
    {
        $query = Aset::query();

        // Mengunci data (Isolasi) berdasarkan kode_satker petugas yang sedang login
        $query->where('kode_satker', $request->user()->kode_satker);

        // Jika pengguna memilih filter satker
        if ($request->filled('satker')) {
            $query->where('kode_satker', $request->satker);
        }

        // Jika pengguna memilih filter kondisi
        if ($request->filled('kondisi')) {
            $query->where('kondisi', $request->kondisi);
        }

        // Ambil data dengan pagination (tetap bawa parameter filternya saat pindah halaman)
        $asets = $query->paginate(10)->withQueryString();

        return view('asets.index', [
            'asets' => $asets
        ]);
    }

    // 1. Menampilkan halaman form upload
    public function formImpor()
    {
        return view('asets.impor');
    }

    // 2. Memproses file Excel yang diupload Admin
    public function prosesImpor(Request $request)
    {
        // Validasi agar yang diupload benar-benar file Excel/CSV
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv'
        ]);

        // Jalankan proses impor menggunakan AsetsImport buatanmu
        Excel::import(new AsetsImport, $request->file('file_excel'));

        // Setelah selesai, arahkan kembali ke halaman daftar aset
        return redirect('/asets');
    }

    public function edit($id)
    {
        // Mencari barang spesifik berdasarkan ID, atau memunculkan halaman 404 jika tidak ada
        $aset = Aset::findOrFail($id);

        return view('asets.opname', [
            'aset' => $aset
        ]);
    }

    public function update(Request $request, $id)
    {
        // 1. Ambil data barang berdasarkan ID
        $aset = Aset::findOrFail($id);

        // 2. Cek Keamanan 1 - Isolasi Satker
        // Mencegah petugas memanipulasi ID di URL untuk mengedit barang satker lain
        if ($aset->kode_satker !== $request->user()->kode_satker) {
            abort(403, 'Anda tidak berhak mengopname aset dari Satker lain!');
        }

        // 3. Cek Keamanan 2 - Anti-Tabrakan (Collision-Proff)
        // Mencegah penimpaan data jika form keduluan di-submit oleh petugas lain
        if ($aset->kondisi !== null) {
            return redirect('/asets')->with('error', 'Gagal! Aset ini baru saja selesai diopname oleh petugas lain');
        }

        // 4. Simpan Data Hasil Opname ke Database
        $aset->update([
            'kondisi'             => $request->kondisi,
            'status_penggunaan'   => $request->status_penggunaan,
            'catatan'             => $request->catatan,
        ]);

        // 5. Kembalikan ke halaman daftar aset dengan pesan sukses
        return redirect('/asets')->with('success', 'Data opname berhasil disimpan!');
    }
}
