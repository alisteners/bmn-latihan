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

        /* Pagination */
        .pagination {
            margin-top: 20px;
        }

        /* --- CSS Khusus untuk Merapikan Pagination Bawaan Laravel --- */
        .pagination svg {
            width: 20px;
            height: 20px;
        }
        .pagination nav {
            display: flex;
            flex-direction: column;
            gap: 15px;
            align-items: center;
        }
        .pagination nav > div:last-child {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .pagination a, .pagination span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 12px;
            text-decoration: none;
            border: 1px solid #dddddd;
            color: #007bff;
            background-color: #ffffff;
            border-radius: 4px;
            font-weight: normal;
            transition: 0.2s;
        }
        .pagination a:hover {
            background-color: #f1f1f1;
        }
        .pagination [aria-current="page"] span {
            background-color: #007bff;
            color: #ffffff;
            border-color: #007bff;
            font-weight: bold;
        }
        .pagination p {
            margin: 0;
            font-size: 14px;
            color: #666;
        }

    </style>
</head>
<body>
    
    <div class="container">
        <h2>Daftar Aset BMN - BBWS Cimanuk Cisanggarung</h2>

        @if (session('success'))
    <div style="background-color: #d4edda; color: #155724; padding: 12px; border-radius: 4px; margin-bottom: 15px;">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div style="background-color: #f8d7da; color: #721c24; padding: 12px; border-radius: 4px; margin-bottom: 15px;">
        {{ session('error') }}
    </div>
@endif
        
        <!-- Form Filter -->
        <form method="GET" action="{{ url('/asets') }}" class="filter-form">
            <select name="satker">
                <option value="">-- Semua Satker --</option>
                <option value="033041" {{ request('satker') == '033041' ? 'selected' : '' }}>033041</option>
                <option value="033042" {{ request('satker') == '033042' ? 'selected' : '' }}>033042</option>
                <option value="033043" {{ request('satker') == '033043' ? 'selected' : '' }}>033043</option>
            </select>

            <select name="kondisi">
                <option value="">-- Semua Kondisi --</option>
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
        <a href="{{ url('/asets/' . $aset->id . '/opname') }}">Kerjakan Opname</a>
    @else
        <span>✅ Selesai</span>
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