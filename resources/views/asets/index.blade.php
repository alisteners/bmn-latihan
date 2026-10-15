<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Aset BMN</title>

    <Style>
        table{
            width: 100%;
            border-collapse: collapse;
            font-family: sans-serif;
        }
        th, td{
            border: 1px solid #dddddd;
            padding: 8px 12px;
            text-align: left;
        }
        th{
            background-color: #f4f4f4;
        }
    </Style>

</head>
<body>
    <h2>Daftar Aset BMN - BBWS Cimanuk Cisanggarung</h2>
    <form method="GET" action="{{ url('/asets') }}" style="margin-bottom: 15px;">
    <select name="satker">
        <option value="">-- Semua Satker --</option>
        <option value="033041">033041</option>
        <option value="033042">033042</option>
        <option value="033043">033043</option>
    </select>

    <select name="kondisi">
        <option value="">-- Semua Kondisi --</option>
        <option value="Baik">Baik</option>
        <option value="Rusak Ringan">Rusak Ringan</option>
        <option value="Rusak Berat">Rusak Berat</option>
    </select>

    <button type="submit">Filter</button>
</form>
    <table>
        <thead>
            <tr>
                <th>Kode Satker</th>
                <th>Kode Barang</th>
                <th>NUP</th>
                <th>Nama Barang</th>
                <th>Kondisi</th>
            </tr>
        </thead>
        <tbody>
            <!-- Mulai Perulangan Data Aset -->
            @foreach ($asets as $aset)
            <tr>
                <td>{{ $aset->kode_satker }}</td>
                <td>{{ $aset->kode_barang }}</td>
                <td>{{ $aset->nup }}</td>
                <td>{{ $aset->nama_barang }}</td>
                <td>{{ $aset->kondisi ?? 'Belum Diopname' }}</td>
            </tr>
            @endforeach
            <!-- Selesai Perulangan -->
        </tbody>
    </table>
    <div style="margin-top: 15px;">
    {{ $asets->links() }}
</div>
</body>
</html>