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
    <table>
        <thead>
            <tr>
                <th>Kode Satker</th>
                <th>Kode Barang</th>
                <th>NUP</th>
                <th>Nama Barang</th>
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
            </tr>
            @endforeach
            <!-- Selesai Perulangan -->
        </tbody>
    </table>
</body>
</html>