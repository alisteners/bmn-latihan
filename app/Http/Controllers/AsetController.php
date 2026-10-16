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

        // Isolasi Satker: Hanya ambil data sesuai satker user yang login
        $satkerUser = $request->user()->kode_satker;
        $query->where('kode_satker', $satkerUser);

        // A. Logika Filter Kondisi (Bercabang untuk nilai 'belum' / NULL)
        if ($request->filled('kondisi')) {
            if ($request->kondisi === 'belum') {
                $query->whereNull('kondisi');
            } else {
                $query->where('kondisi', $request->kondisi);
            }
        }

        // B. Hitung 3 Angka Rekapitulasi Khusus Satker User
        $totalAset   = Aset::where('kode_satker', $satkerUser)->count();
        $sudahOpname = Aset::where('kode_satker', $satkerUser)->whereNotNull('kondisi')->count();
        $belumOpname = Aset::where('kode_satker', $satkerUser)->whereNull('kondisi')->count();

        // Paginate dan bawa parameter URL
        $asets = $query->paginate(20)->withQueryString();

        // Kirim semua data ke View
        return view('asets.index', [
            'asets'       => $asets,
            'totalAset'   => $totalAset,
            'sudahOpname' => $sudahOpname,
            'belumOpname' => $belumOpname,
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

    public function edit(Request $request, $id)
    {
        // Mencari barang spesifik berdasarkan ID, atau memunculkan halaman 404 jika tidak ada
        $aset = Aset::findOrFail($id);

        if ($aset->kode_satker !== $request->user()->kode_satker) {
            abort(403, 'Anda tidak berhak mengopname aset dari Satker lain!');
        }

        if ($aset->kondisi !== null) {
            return redirect('/asets')->with('error', 'Aset ini sudah selesai diopname.');
        }

        return view('asets.opname', ['aset' => $aset]);
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
