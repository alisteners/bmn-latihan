<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Impor Data BMN</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7f6;
            padding: 20px;
        }
        .container {
            max-width: 500px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .btn-kembali {
            display: inline-block;
            margin-bottom: 15px;
            color: #007bff;
            text-decoration: none;
            font-size: 14px;
        }
        .btn-kembali:hover {
            text-decoration: underline;
        }
        button[type="submit"] {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            width: 100%;
        }
        button[type="submit"]:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

    <div class="container">
        <!-- Tombol Kembali ke Daftar Aset -->
        <a href="{{ url('/asets') }}" class="btn-kembali">← Kembali ke Daftar Aset</a>

        <h2 style="margin-top: 0;">Form Impor Excel Aset BMN</h2>

        <form method="POST" action="{{ url('/admin/impor') }}" enctype="multipart/form-data">
            @csrf
            
            <div style="margin-bottom: 20px;">
                <label for="file_excel" style="display: block; margin-bottom: 8px; font-weight: bold;">Pilih file Excel (.xlsx / .csv):</label>
                <input type="file" name="file_excel" id="file_excel" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
            </div>
            
            <button type="submit">Impor Data BMN</button>
        </form>
    </div>

</body>
</html>