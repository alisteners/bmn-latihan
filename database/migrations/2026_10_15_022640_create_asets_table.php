<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('asets', function (Blueprint $table) {
            $table->id();
            // Data dari Excel Admin (Wajib terisi/tidak boleh kosong)
            $table->string('kode_satker');
            $table->string('kode_barang');
            $table->integer('nup');
            $table->string('nama_barang');
            // Data Isian Petugas Lapangan (Kosong saat awal diimpor Admin, baru terisi saat petugas Submit)
            $table->string('kondisi')->nullable();
            $table->string('status_penggunaan')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asets');
    }
};
