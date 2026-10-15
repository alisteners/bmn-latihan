<?php

namespace App\Http\Controllers;

use App\Models\Aset;
use Illuminate\Http\Request;

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
}
