<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Kas - {{ $period->group->name }} ({{ $period->period_name }})</title>
    <style>
        @page {
            size: A4;
            margin: 1.5cm;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
            color: #111;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 16pt;
            margin: 0 0 5px 0;
            text-transform: uppercase;
        }
        .header p {
            margin: 0;
            font-size: 10pt;
            color: #444;
        }
        .summary-box {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            padding: 10px;
            background: #f9f9f9;
        }
        .summary-item {
            text-align: center;
            flex: 1;
        }
        .summary-title {
            font-size: 9pt;
            color: #666;
            text-transform: uppercase;
        }
        .summary-val {
            font-size: 13pt;
            font-weight: bold;
            margin-top: 3px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 10pt;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px 8px;
        }
        th {
            background-color: #f0f0f0;
            text-align: left;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-row { font-weight: bold; background-color: #f9f9f9; }
        .section-title {
            font-size: 12pt;
            font-weight: bold;
            margin: 15px 0 8px 0;
            border-left: 4px solid #333;
            padding-left: 6px;
        }
        .signatures {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
            page-break-inside: avoid;
        }
        .sign-col {
            text-align: center;
            width: 200px;
        }
        .sign-line {
            margin-top: 60px;
            border-bottom: 1px solid #000;
        }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print()" style="padding: 8px 16px; background: #2563eb; color: #fff; border: none; border-radius: 4px; font-weight: bold; cursor: pointer;">
            🖨️ Cetak / Simpan PDF
        </button>
    </div>

    <div class="header">
        <h1>Laporan Rekapitulasi Kas</h1>
        <p><strong>{{ strtoupper($period->group->name) }}</strong></p>
        <p>Periode: {{ $period->period_name }} | Status: {{ strtoupper($period->status) }} | Iuran/Org: Rp {{ number_format($period->effective_due_amount, 0, ',', '.') }}</p>
    </div>

    <div class="summary-box">
        <div class="summary-item">
            <div class="summary-title">Total Pemasukan</div>
            <div class="summary-val" style="color: #059669;">Rp {{ number_format($period->total_income, 0, ',', '.') }}</div>
        </div>
        <div class="summary-item">
            <div class="summary-title">Total Pengeluaran</div>
            <div class="summary-val" style="color: #dc2626;">Rp {{ number_format($period->total_expense, 0, ',', '.') }}</div>
        </div>
        <div class="summary-item">
            <div class="summary-title">Saldo / Sisa Akhir</div>
            <div class="summary-val" style="color: {{ $period->balance >= 0 ? '#2563eb' : '#dc2626' }};">
                Rp {{ number_format($period->balance, 0, ',', '.') }}
            </div>
        </div>
    </div>

    {{-- Pemasukan --}}
    <div class="section-title">1. Daftar Pemasukan (Iuran Anggota)</div>
    <table>
        <thead>
            <tr>
                <th style="width: 40px;" class="text-center">No</th>
                <th style="width: 100px;">Tanggal</th>
                <th>Nama Anggota</th>
                <th style="width: 130px;" class="text-right">Nominal (Rp)</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($period->incomes as $i => $inc)
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td>{{ $inc->transaction_date->format('d/m/Y') }}</td>
                <td>{{ $inc->member->name ?? '—' }}</td>
                <td class="text-right">{{ number_format($inc->nominal, 0, ',', '.') }}</td>
                <td>{{ $inc->note ?? '-' }}</td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center">Tidak ada pemasukan tercatat pada periode ini.</td></tr>
            @endforelse
            <tr class="total-row">
                <td colspan="3" class="text-right">TOTAL PEMASUKAN</td>
                <td class="text-right">Rp {{ number_format($period->total_income, 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>

    {{-- Pengeluaran --}}
    <div class="section-title">2. Daftar Pengeluaran</div>
    <table>
        <thead>
            <tr>
                <th style="width: 40px;" class="text-center">No</th>
                <th style="width: 100px;">Tanggal</th>
                <th>Barang / Keperluan</th>
                <th>Kategori</th>
                <th style="width: 130px;" class="text-right">Nominal (Rp)</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($period->expenses as $i => $exp)
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td>{{ $exp->transaction_date->format('d/m/Y') }}</td>
                <td>{{ $exp->item_name }}</td>
                <td>{{ $exp->category->name ?? 'Umum' }}</td>
                <td class="text-right">{{ number_format($exp->nominal, 0, ',', '.') }}</td>
                <td>{{ $exp->note ?? '-' }}</td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center">Tidak ada pengeluaran tercatat pada periode ini.</td></tr>
            @endforelse
            <tr class="total-row">
                <td colspan="4" class="text-right">TOTAL PENGELUARAN</td>
                <td class="text-right">Rp {{ number_format($period->total_expense, 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>

    {{-- Tanda Tangan --}}
    <div class="signatures">
        <div class="sign-col">
            <p>Mengetahui,<br>Ketua Pengurus</p>
            <div class="sign-line"></div>
            <p>( .................................... )</p>
        </div>
        <div class="sign-col">
            <p>Dibuat Oleh,<br>Bendahara Kas</p>
            <div class="sign-line"></div>
            <p>( {{ $period->creator->name ?? 'Bendahara' }} )</p>
        </div>
    </div>
</body>
</html>
