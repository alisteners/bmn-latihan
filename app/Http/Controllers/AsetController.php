<?php

namespace App\Http\Controllers;

use App\Models\Aset;
use Illuminate\Http\Request;

class AsetController extends Controller
{
    public function index()
    {
        // 1. Mengambil semua data dari tabel asets menggunakan Aset::all()
        $asets = Aset::all();
        // 2. Mengembalikan tampilan Blade 'asets.index' sambil mengirimkan data $asetes
        return view('asets.index', [
            'asets' => $asets
        ]);
    }
}
