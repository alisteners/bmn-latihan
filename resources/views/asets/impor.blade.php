<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Impor Data BMN</title>
</head>
<body>

    <h2>Form Impor Excel Aset BMN</h2>

    <!-- Form wajib menggunakan metode POST dan enctype multipart -->
    <form method="POST" action="{{ url('/admin/impor') }}" enctype="multipart/form-data">
        
        <!-- Tiket keamanan wajib dari Laravel agar tidak terkena error 419 -->
        @csrf
        
        <div style="margin-bottom: 15px;">
            <label for="file_excel">Pilih file Excel (.xlsx / .csv):</label><br><br>
            <input type="file" name="file_excel" id="file_excel" required>
        </div>
        
        <button type="submit" style="cursor: pointer; padding: 5px 10px;">Impor Data BMN</button>
    </form>

</body>
</html>