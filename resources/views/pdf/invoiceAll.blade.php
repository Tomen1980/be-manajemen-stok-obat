<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
            color: #333;
        }
        .invoice-container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 24px;
            margin: 0;
        }
        .header p {
            margin: 0;
            font-size: 14px;
            color: #666;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .info-table th, .info-table td {
            text-align: left;
            padding: 8px;
            border: 1px solid #ddd;
        }
        .info-table th {
            background-color: #f4f4f4;
        }
        .breakdown-title {
            margin-top: 20px;
            font-size: 18px;
            font-weight: bold;
        }
        .breakdown-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .breakdown-table th, .breakdown-table td {
            text-align: left;
            padding: 8px;
            border: 1px solid #ddd;
        }
        .breakdown-table th {
            background-color: #f4f4f4;
        }
        .total {
            text-align: right;
            margin-top: 20px;
            font-size: 16px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="header">
            <h1>{{ $title }}</h1>
            <p>Generated on: {{ date('Y-m-d H:i:s') }}</p>
        </div>
        @foreach ($transaksi as $transaksi)
            
        
        <table class="info-table">
            <tr>
                <th>Pasien</th>
                <td>{{ $transaksi->pasien->nama ?? 'Transaksi Pembelian' }}</td>
            </tr>
            <tr>
                <th>Tipe</th>
                <td>{{ $transaksi->tipe }}</td>
            </tr>
            <tr>
                <th>User</th>
                <td>{{ $transaksi->user->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Status</th>
                <td>{{ $transaksi->status }}</td>
            </tr>
            <tr>
                <th>Deskripsi</th>
                <td>{{ $transaksi->deskripsi }}</td>
            </tr>
            <tr>
                <th>Total Harga</th>
                <td>{{ number_format($transaksi->total_harga, 2) }}</td>
            </tr>
            <tr>
                <th>Tanggal</th>
                <td>{{ \Carbon\Carbon::parse($transaksi->tanggal)->format('d-m-Y') }}</td>
            </tr>
        </table>

        <div class="breakdown-title">Breakdown Obat</div>
        <table class="breakdown-table">
            <thead>
                <tr>
                    <th>Nama Obat</th>
                    <th>Jumlah</th>
                    <th>Harga Satuan</th>
                    <th>Total Harga</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transaksi->TransaksiItem as $item)
                <tr>
                    <td>{{ $item->ObatDetail->Obat->nama ?? 'Unknown' }}</td>
                    <td>{{ $item->jumlah }}</td>
                    <td>{{ number_format($item->ObatDetail->harga_beli_unit ?? 0, 2) }}</td>
                    <td>{{ number_format($item->total_harga, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total">
            Total Keseluruhan: {{ number_format($transaksi->total_harga, 2) }}
        </div>
        <br>
        <br>
        @endforeach
    </div>
</body>
</html>
