<?php

namespace App\Imports;

use App\Models\Aset;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AsetsImport implements ToModel, WithHeadingRow
{
    public function model(array $row): Model|null
    {
        return new Aset([
            'kode_satker' => str_pad($row['kode_satker'], 6, '0', STR_PAD_LEFT),
            'kode_barang' => $row['kode_barang'],
            'nup' => $row['nup'],
            'nama_barang' => $row['nama_barang'],
        ]);
    }
}
