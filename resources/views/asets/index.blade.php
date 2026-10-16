<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Aset BMN</title>

    <style>
        /* CSS Dasar untuk Body dan Layout */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7f6;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        h2 {
            margin-top: 0;
            color: #2c3e50;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        /* Notifikasi Flash Message */
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
            border-radius: 4px;
        }
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
        }

        /* CSS untuk Form Filter */
        .filter-form {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            border: 1px solid #e9ecef;
        }
        select {
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
            outline: none;
        }
        button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            transition: background 0.3s;
        }
        button:hover {
            background-color: #0056b3;
        }

        /* CSS untuk Tabel */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }
        th, td {
            border: 1px solid #dddddd;
            padding: 10px 12px;
            text-align: left;
        }
        th {
            background-color: #343a40;
            color: #ffffff;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }

        /* Pagination & Aksi */
        .pagination { margin-top: 20px; }
        .pagination svg { width: 20px; height: 20px; }
        .pagination nav { display: flex; flex-direction: column; gap: 15px; align-items: center; }
        .pagination nav > div:last-child { display: flex; align-items: center; gap: 5px; }
        .pagination a, .pagination span {
            display: inline-flex; align-items: center; justify-content: center;
            padding: 8px 12px; text-decoration: none; border: 1px solid #dddddd;
            color: #007bff; background-color: #ffffff; border-radius: 4px; transition: 0.2s;
        }
        .pagination a:hover { background-color: #f1f1f1; }
        .pagination [aria-current="page"] span { background-color: #007bff; color: #ffffff; font-weight: bold; }
        .pagination p { margin: 0; font-size: 14px; color: #666; }
        .btn-opname { color: #007bff; text-decoration: none; font-weight: bold; }
        .btn-opname:hover { text-decoration: underline; }
    </style>
</head>
<body>
    
    <div class="container">
        <h2>Daftar Aset BMN - BBWS Cimanuk Cisanggarung</h2>

        <!-- Flash Message Bawaan Laravel (Menangkap session dari Hari 6) -->
        @if (session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert-error">{{ session('error') }}</div>
        @endif

        <!-- Baris Info User, Tombol Khusus Admin, & Tombol Logout -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
    <div>
        Login sebagai: <strong>{{ auth()->user()->name }}</strong> 
        (Role: <span style="text-transform: uppercase;">{{ auth()->user()->role }}</span> | Satker: <strong>{{ auth()->user()->kode_satker }}</strong>)
    </div>

    <div style="display: flex; gap: 10px; align-items: center;">
        @if (auth()->user()->role === 'admin')
            <a href="{{ url('/admin/impor') }}" style="background-color: #28a745; color: white; padding: 8px 14px; text-decoration: none; border-radius: 4px; font-weight: bold;">
                + Impor Excel BMN
            </a>
        @endif

        <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
            @csrf
            <button type="submit" style="background-color: #dc3545; color: white; padding: 8px 14px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">
                Logout
            </button>
        </form>
    </div>
</div>

        <!-- Kartu Ringkasan Rekapitulasi Opname -->
        <div style="display: flex; gap: 15px; margin-bottom: 20px;">
            <div style="flex: 1; background: #e3f2fd; padding: 15px; border-radius: 6px; border-left: 5px solid #007bff;">
                <div style="font-size: 13px; color: #555;">Total Aset Satker</div>
                <div style="font-size: 24px; font-weight: bold; color: #007bff;">{{ $totalAset }}</div>
            </div>
            <div style="flex: 1; background: #e8f5e9; padding: 15px; border-radius: 6px; border-left: 5px solid #28a745;">
                <div style="font-size: 13px; color: #555;">Sudah Diopname</div>
                <div style="font-size: 24px; font-weight: bold; color: #28a745;">{{ $sudahOpname }}</div>
            </div>
            <div style="flex: 1; background: #fff3cd; padding: 15px; border-radius: 6px; border-left: 5px solid #ffc107;">
                <div style="font-size: 13px; color: #555;">Belum Diopname (Antrean)</div>
                <div style="font-size: 24px; font-weight: bold; color: #d39e00;">{{ $belumOpname }}</div>
            </div>
        </div>

        <!-- Form Filter (Pilihan Satker dihapus) -->
        <form method="GET" action="{{ url('/asets') }}" class="filter-form">
            <select name="kondisi">
                <option value="">-- Semua Kondisi --</option>
                <!-- Pilihan Baru: Belum Diopname -->
                <option value="belum" {{ request('kondisi') == 'belum' ? 'selected' : '' }}>⏳ Belum Diopname (Antrean)</option>
                <option value="Baik" {{ request('kondisi') == 'Baik' ? 'selected' : '' }}>Baik</option>
                <option value="Rusak Ringan" {{ request('kondisi') == 'Rusak Ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                <option value="Rusak Berat" {{ request('kondisi') == 'Rusak Berat' ? 'selected' : '' }}>Rusak Berat</option>
            </select>

            <button type="submit">Filter</button>
        </form>
        
        <!-- Tabel Data -->
        <table>
            <thead>
                <tr>
                    <th>Kode Satker</th>
                    <th>Kode Barang</th>
                    <th>NUP</th>
                    <th>Nama Barang</th>
                    <th>Kondisi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <!-- Mulai Perulangan Data Aset -->
                @forelse ($asets as $aset)
                <tr>
                    <td>{{ $aset->kode_satker }}</td>
                    <td>{{ $aset->kode_barang }}</td>
                    <td>{{ $aset->nup }}</td>
                    <td><strong>{{ $aset->nama_barang }}</strong></td>
                    <td>
                        @if($aset->kondisi == 'Baik')
                            <span style="color: green; font-weight: bold;">Baik</span>
                        @elseif($aset->kondisi == 'Rusak Ringan')
                            <span style="color: orange; font-weight: bold;">Rusak Ringan</span>
                        @elseif($aset->kondisi == 'Rusak Berat')
                            <span style="color: red; font-weight: bold;">Rusak Berat</span>
                        @else
                            <span style="color: gray;">Belum Diopname</span>
                        @endif
                    </td>
                    <td>
                        @if ($aset->kondisi === null)
                            <a href="{{ url('/asets/' . $aset->id . '/opname') }}" class="btn-opname">Kerjakan Opname</a>
                        @else
                            <span style="color: green;">✅ Selesai</span>
                        @endif
                    </td>
                </tr>
                @empty
                <!-- Ditampilkan jika hasil filter kosong -->
                <tr>
                    <td colspan="6" style="text-align: center; color: #777;">Data aset tidak ditemukan.</td>
                </tr>
                @endforelse
                <!-- Selesai Perulangan -->
            </tbody>
        </table>
        
        <!-- Pagination -->
        <div class="pagination">
            {{ $asets->links() }}
        </div>
    </div>

</body>
</html>