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
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv'
        ]);

        $import = new AsetsImport($request->user()->kode_satker);
        Excel::import($import, $request->file('file_excel'));
        $gagal = collect($import->failures())
            ->map(fn($f) => [
                'baris'  => $f->row(),
                'alasan' => $f->errors(),
            ])
            ->merge($import->duplikatTerlapor())
            ->sortBy('baris')->values()->all();;

        return redirect('/asets')->with([
            'success'     => 'Impor selesai: ' . $import->jumlahBerhasil() . ' baris masuk, ' . count($gagal) . ' baris dilewati.',
            'impor_gagal' => $gagal,
        ]);
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
        $validated = $request->validate([
            'kondisi'           => 'required|in:Baik,Rusak Ringan,Rusak Berat',
            'status_penggunaan' => 'required|in:Digunakan Sendiri untuk Operasional,Tidak Digunakan / Idle',
            'catatan'           => 'nullable|string|max:2000',
        ]);

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

        // 4. Simpan atomik: syarat whereNull('kondisi') dievaluasi database
        //    tepat saat UPDATE berjalan, jadi dua submit bersamaan tidak bisa
        //    dua-duanya lolos — yang kedua dapat 0 baris terpengaruh.
        $berhasil = $aset->selesaikanOpname([
            'kondisi'           => $validated['kondisi'],
            'status_penggunaan' => $validated['status_penggunaan'],
            'catatan'           => $validated['catatan'] ?? null,
        ]);

        if (! $berhasil) {
            return redirect('/asets')->with('error', 'Gagal! Aset ini baru saja selesai diopname oleh petugas lain');
        }


        // 5. Kembalikan ke halaman daftar aset dengan pesan sukses
        return redirect('/asets')->with('success', 'Data opname berhasil disimpan!');
    }
}
