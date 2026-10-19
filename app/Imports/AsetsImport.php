<?php

namespace App\Imports;

use App\Models\Aset;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class AsetsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    private int $jumlahBerhasil = 0;
    private int $jumlahSudahAda = 0;

    /** @var array<string, list<int>> kunci baris => daftar batch-index (urutan kemunculan) */
    private array $indexPerKunci = [];

    /** @var array<string, true> kunci yang sudah berhasil diimpor pada proses ini */
    private array $kunciTerlihat = [];

    /** @var list<array{baris:int, alasan:list<string>}> duplikat dalam file yang dilaporkan */
    private array $duplikatTerlapor = [];

    public function __construct(private string $kodeSatkerAdmin) {}

    public function prepareForValidation(array $data, int $index): array
    {
        if (isset($data['kode_satker'])) {
            $data['kode_satker'] = str_pad(trim((string) $data['kode_satker']), 6, '0', STR_PAD_LEFT);
        }
        foreach (['kode_barang', 'nama_barang'] as $kolom) {
            if (isset($data[$kolom])) {
                $data[$kolom] = trim((string) $data[$kolom]);
            }
        }

        // Catat index tiap baris per kunci. Berbasis kunci (bukan urutan),
        // agar tetap benar walau ada baris yang gagal validasi dan
        // tidak sampai ke model().
        $this->indexPerKunci[$this->kunciBaris($data)][] = $index;

        return $data;
    }

    public function rules(): array
    {
        return [
            'kode_satker' => ['required', 'string', Rule::in([$this->kodeSatkerAdmin])],
            'kode_barang' => ['required', 'string', 'max:50'],
            'nup'         => ['required', 'integer', 'min:1'],
            'nama_barang' => ['required', 'string', 'max:255'],
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'kode_satker.required' => 'kode_satker wajib diisi.',
            'kode_satker.in'       => 'kode_satker harus milik satker Anda (' . $this->kodeSatkerAdmin . ').',
            'kode_barang.required' => 'kode_barang wajib diisi.',
            'nup.required'         => 'nup harus berupa angka.',
            'nup.integer'          => 'nup harus berupa angka.',
            'nama_barang.required' => 'nama_barang wajib diisi.',
        ];
    }

    public function model(array $row): Model|null
    {
        $kodeSatker = str_pad(trim((string) ($row['kode_satker'] ?? '')), 6, '0', STR_PAD_LEFT);
        $kodeBarang = trim((string) ($row['kode_barang'] ?? ''));
        $nup        = $row['nup'] ?? null;
        $kunci      = $kodeSatker . '|' . $kodeBarang . '|' . $nup;

        $daftarIndex = $this->indexPerKunci[$kunci] ?? [];
        $index = array_shift($daftarIndex);
        $this->indexPerKunci[$kunci] = $daftarIndex;
        $barisExcel = $index ?? 0; // $index sudah berupa nomor baris Excel

        // Tandai terlihat SEBELUM cek database -> duplikat konsisten
        // antara impor pertama dan impor ulang.
        $duplikatDalamFile = isset($this->kunciTerlihat[$kunci]);
        $this->kunciTerlihat[$kunci] = true;

        if ($duplikatDalamFile) {
            $this->duplikatTerlapor[] = [
                'baris'  => $barisExcel,
                'alasan' => ['Duplikat: kombinasi kode_satker + kode_barang + nup sudah muncul pada baris sebelumnya di file ini.'],
            ];
            return null;
        }

        $sudahAda = Aset::where('kode_satker', $kodeSatker)
            ->where('kode_barang', $kodeBarang)
            ->where('nup', $nup)
            ->exists();

        if ($sudahAda) {
            $this->jumlahSudahAda++;
            return null;
        }

        $this->jumlahBerhasil++;

        return new Aset([
            'kode_satker' => $kodeSatker,
            'kode_barang' => $kodeBarang,
            'nup'         => $nup,
            'nama_barang' => trim((string) ($row['nama_barang'] ?? '')),
        ]);
    }

    private function kunciBaris(array $baris): string
    {
        return ($baris['kode_satker'] ?? '') . '|'
            . ($baris['kode_barang'] ?? '') . '|'
            . ($baris['nup'] ?? '');
    }

    public function jumlahBerhasil(): int
    {
        return $this->jumlahBerhasil;
    }

    public function jumlahSudahAda(): int
    {
        return $this->jumlahSudahAda;
    }

    /** @return list<array{baris:int, alasan:list<string>}> */
    public function duplikatTerlapor(): array
    {
        return $this->duplikatTerlapor;
    }
}
