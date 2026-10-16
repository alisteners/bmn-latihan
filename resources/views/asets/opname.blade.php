<x-app-layout>
    <div class="py-8 px-4 mx-auto max-w-2xl lg:py-16">
        <h2 class="mb-4 text-2xl font-bold text-gray-900 dark:text-white">Form Hasil Opname</h2>
        
        <!-- Informasi Aset yang Sedang Dikerjakan -->
        <div class="p-4 mb-6 text-sm text-blue-800 rounded-lg bg-blue-50 dark:bg-gray-800 dark:text-blue-400" role="alert">
            <span class="font-medium">Data Barang:</span>
            <ul class="mt-1.5 list-disc list-inside">
                <li>Nama: <strong>{{ $aset->nama_barang }}</strong></li>
                <li>Kode Barang: <strong>{{ $aset->kode_barang }}</strong></li>
                <li>NUP: <strong>{{ $aset->nup }}</strong></li>
            </ul>
        </div>

        <!-- Form Input Data -->
        <form method="POST" action="{{ url('/asets/' . $aset->id . '/opname') }}">
            @csrf
            
            <div class="grid gap-4 sm:grid-cols-2 sm:gap-6">
                
                <!-- Pilihan Kondisi -->
                <div class="sm:col-span-2">
                    <label for="kondisi" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kondisi Barang</label>
                    <select name="kondisi" id="kondisi" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">-- Pilih Kondisi --</option>
                        <option value="Baik">Baik</option>
                        <option value="Rusak Ringan">Rusak Ringan</option>
                        <option value="Rusak Berat">Rusak Berat</option>
                    </select>
                </div>

                <!-- Pilihan Status Penggunaan -->
                <div class="sm:col-span-2">
                    <label for="status_penggunaan" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Status Penggunaan</label>
                    <select name="status_penggunaan" id="status_penggunaan" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">-- Pilih Status --</option>
                        <option value="Digunakan Sendiri untuk Operasional">Digunakan Sendiri untuk Operasional</option>
                        <option value="Tidak Digunakan / Idle">Tidak Digunakan / Idle</option>
                    </select>
                </div>

                <!-- Kolom Catatan -->
                <div class="sm:col-span-2">
                    <label for="catatan" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Catatan / Keterangan (Opsional)</label>
                    <textarea name="catatan" id="catatan" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Tuliskan keterangan tambahan jika diperlukan..."></textarea>
                </div>
            </div>
            
            <!-- Tombol Submit -->
            <button type="submit" class="inline-flex items-center px-5 py-2.5 mt-4 sm:mt-6 text-sm font-medium text-center text-white bg-blue-700 rounded-lg focus:ring-4 focus:ring-blue-200 dark:focus:ring-blue-900 hover:bg-blue-800">
                Simpan Hasil Opname
            </button>
        </form>
    </div>
</x-app-layout>