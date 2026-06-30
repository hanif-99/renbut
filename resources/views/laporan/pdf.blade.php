<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Formasi ASN {{ $tahun }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 10px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        
        .header h2 {
            margin: 0;
            font-size: 16px;
        }
        
        .header p {
            margin: 5px 0;
        }
        
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .summary-table td {
            padding: 8px;
            text-align: center;
            border: 1px solid #ddd;
        }
        
        .summary-table th {
            padding: 8px;
            text-align: center;
            border: 1px solid #000;
            background-color: #f0f0f0;
            font-weight: bold;
        }
        
        .detail-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }
        
        .detail-table th {
            padding: 6px;
            text-align: center;
            border: 1px solid #000;
            background-color: #f0f0f0;
            font-weight: bold;
        }
        
        .detail-table td {
            padding: 6px;
            border: 1px solid #ddd;
            text-align: center;
        }
        
        .detail-table .text-left {
            text-align: left;
        }
        
        .total-row {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        
        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN FORMASI ASN TAHUN {{ $tahun }}</h2>
        <p>Perencanaan Kebutuhan ASN Tahun {{ $tahun }}</p>
    </div>

    <table class="summary-table">
        <tr>
            <th>JPT</th>
            <th>ADM & PENGAWAS</th>
            <th>MUTASI</th>
            <th>CPNS</th>
            <th>PPPK</th>
            <th>TOTAL</th>
        </tr>
        <tr>
            <td>{{ $summary->jpt ?? 0 }}</td>
            <td>{{ $summary->adm_pengawas ?? 0 }}</td>
            <td>{{ $summary->mutasi ?? 0 }}</td>
            <td>{{ $summary->cpns ?? 0 }}</td>
            <td>{{ $summary->pppk ?? 0 }}</td>
            <td><strong>{{ ($summary->jpt ?? 0) + ($summary->adm_pengawas ?? 0) + ($summary->mutasi ?? 0) + ($summary->cpns ?? 0) + ($summary->pppk ?? 0) }}</strong></td>
        </tr>
    </table>

    <table class="detail-table">
        <thead>
            <tr>
                <th>No</th>
                <th>PERANGKAT DAERAH</th>
                <th>UNIT ORGANISASI</th>
                <th>JABATAN</th>
                <th>JPT</th>
                <th>ADM & PENGAWAS</th>
                <th>MUTASI</th>
                <th>CPNS</th>
                <th>PPPK</th>
                <th>TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($formasi as $item)
                <tr>
                    <td>{{ $no++ }}</td>
                    <td class="text-left">{{ $item->jabatan->unitOrganisasi->perangkatDaerah->nama ?? '-' }}</td>
                    <td class="text-left">{{ $item->jabatan->unitOrganisasi->nama ?? '-' }}</td>
                    <td class="text-left">{{ $item->jabatan->nama }}</td>
                    <td>{{ $item->jpt }}</td>
                    <td>{{ $item->adm_pengawas }}</td>
                    <td>{{ $item->mutasi }}</td>
                    <td>{{ $item->cpns }}</td>
                    <td>{{ $item->pppk }}</td>
                    <td><strong>{{ $item->jpt + $item->adm_pengawas + $item->mutasi + $item->cpns + $item->pppk }}</strong></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Tanggal Cetak: {{ date('d-m-Y H:i:s') }}</p>
    </div>
</body>
</html>