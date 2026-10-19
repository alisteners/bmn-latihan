<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class Aset extends Model
{
    protected $guarded = ['id'];

    public const KONDISI = ['Baik', 'Rusak Ringan', 'Rusak Berat'];

    public const STATUS_PENGGUNAAN = ['Digunakan Sendiri untuk Operasional', 'Tidak Digunakan / Idle'];

    public function selesaikanOpname(array $data): bool
    {
        $dataAman = Arr::only($data, ['kondisi', 'status_penggunaan', 'catatan']);

        return static::where('id', $this->id)
            ->whereNull('kondisi')
            ->update($dataAman) === 1;
    }
}
