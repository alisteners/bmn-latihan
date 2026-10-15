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
}
