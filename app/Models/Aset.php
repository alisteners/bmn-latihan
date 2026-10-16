<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aset extends Model
{
    protected $guarded = ['id'];

    public function selesaikanOpname(array $data): bool
    {
        return static::where('id', $this->id)
            ->whereNull('kondisi')
            ->update($data) === 1;
    }
}
