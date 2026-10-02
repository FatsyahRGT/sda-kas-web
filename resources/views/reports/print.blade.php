<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Kas - {{ $period->group->name }} ({{ $period->period_name }})</title>
    
    {{-- Alpine.js CDN for interactive print controls --}}
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        @page {
            size: A4 portrait;
            margin: 1.2cm 1.5cm;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: "Segoe UI", Roboto, Arial, sans-serif;
            font-size: 10pt;
            color: #1e293b;
            line-height: 1.4;
            margin: 0;
            padding: 0;
            background-color: #f8fafc;
        }
        .page-container {
            max-width: 21cm;
            margin: 20px auto;
            background: #ffffff;
            padding: 30px 40px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        /* Floating Interactive Toolbar */
        .toolbar {
            position: sticky;
            top: 15px;
            z-index: 100;
            max-width: 21cm;
            margin: 15px auto;
            background: #0f172a;
            color: #ffffff;
            padding: 10px 20px;
            border-radius: 12px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3);
        }
        .toolbar-group {
            display: flex;
            align-items: center;
            gap: 15px;
            font-size: 9pt;
        }
        .toolbar label {
            display: flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            user-select: none;
        }
        .btn-print {
            padding: 8px 18px;
            background: #2563eb;
            color: #ffffff;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            font-size: 10pt;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: background 0.2s;
        }
        .btn-print:hover {
            background: #1d4ed8;
        }
        .btn-back {
            padding: 8px 14px;
            background: #334155;
            color: #ffffff;
            text-decoration: none;
            border-radius: 8px;
            font-size: 9pt;
            font-weight: 600;
        }
        .btn-back:hover {
            background: #475569;
        }

        /* Kop Surat Resmi */
        .kop-surat {
            text-align: center;
            border-bottom: 3px double #0f172a;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }
        .kop-surat h1 {
            font-size: 15pt;
            font-weight: 900;
            letter-spacing: 0.5px;
            margin: 0 0 4px 0;
            text-transform: uppercase;
            color: #0f172a;
        }
        .kop-surat h2 {
            font-size: 12pt;
            font-weight: 700;
            margin: 0 0 4px 0;
            color: #334155;
        }
        .kop-surat p {
            margin: 0;
            font-size: 8.5pt;
            color: #64748b;
        }

        /* Document Meta */
        .doc-meta {
            display: flex;
            justify-content: space-between;
            font-size: 8.5pt;
            color: #64748b;
            margin-bottom: 15px;
            border-bottom: 1px dashed #cbd5e1;
            padding-bottom: 8px;
        }

        /* Summary Boxes */
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-bottom: 20px;
        }
        .summary-card {
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px;
            text-align: center;
            background: #f8fafc;
        }
        .summary-label {
            font-size: 8pt;
            font-weight: bold;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .summary-value {
            font-size: 13pt;
            font-weight: 900;
            margin-top: 3px;
        }

        /* Tables */
        .section-header {
            font-size: 10.5pt;
            font-weight: bold;
            color: #0f172a;
            margin: 16px 0 8px 0;
            border-left: 3.5px solid #2563eb;
            padding-left: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
            font-size: 9pt;
        }
        th, td {
            border: 1px solid #cbd5e1;
            padding: 5px 8px;
        }
        th {
            background-color: #f1f5f9;
            color: #334155;
            font-weight: 700;
            text-align: left;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-row {
            font-weight: bold;
            background-color: #f8fafc;
            border-top: 2px solid #94a3b8;
        }

        /* Signatures */
        .signatures {
            margin-top: 35px;
            display: flex;
            justify-content: space-between;
            page-break-inside: avoid;
        }
        .sign-col {
            text-align: center;
            width: 220px;
        }
        .sign-col p {
            margin: 0;
            font-size: 9.5pt;
        }
        .sign-space {
            height: 60px;
        }
        .sign-name {
            font-weight: bold;
            border-bottom: 1px solid #0f172a;
            padding-bottom: 2px;
            display: inline-block;
            min-width: 160px;
        }

        /* Attachments Grid */
        .attachments-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-top: 10px;
            page-break-inside: avoid;
        }
        .attachment-item {
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            overflow: hidden;
            text-align: center;
            background: #f8fafc;
            padding: 4px;
        }
        .attachment-item img {
            max-height: 140px;
            width: 100%;
            object-fit: cover;
            border-radius: 2px;
        }
        .attachment-caption {
            font-size: 8pt;
            color: #475569;
            margin-top: 4px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Print Media Queries */
        @media print {
            body {
                background: #ffffff;
                color: #000000;
            }
            .page-container {
                max-width: 100%;
                margin: 0;
                padding: 0;
                box-shadow: none;
                border-radius: 0;
            }
            .no-print {
                display: none !important;
            }
            th {
                background-color: #f1f5f9 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .summary-card {
                background-color: #f8fafc !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body x-data="{ showCategorySummary: true, showAttachments: true, showSignatures: true }">
    {{-- Floating Print Control Bar (Screen only) --}}
    <div class="toolbar no-print">
        <div class="toolbar-group">
            <span style="font-weight: bold;">⚙️ Opsi Cetak:</span>
            <label>
                <input type="checkbox" x-model="showCategorySummary">
                <span>Ringkasan Kategori</span>
            </label>
            <label>
                <input type="checkbox" x-model="showAttachments">
                <span>Lampiran Foto Nota</span>
            </label>
            <label>
                <input type="checkbox" x-model="showSignatures">
                <span>Tanda Tangan</span>
            </label>
        </div>

        <div style="display: flex; align-items: center; gap: 8px;">
            <a href="{{ route('reports.index', ['group_id' => $period->group_id, 'period_id' => $period->id]) }}" class="btn-back">
                &larr; Kembali
            </a>
            <button type="button" onclick="window.print()" class="btn-print">
                🖨️ Cetak / Simpan PDF
            </button>
        </div>
    </div>

    {{-- Printable Paper Container --}}
    <div class="page-container">
        {{-- Kop Surat Resmi --}}
        <div class="kop-surat">
            <h1>Laporan Rekapitulasi Kas Keuangan</h1>
            <h2>{{ strtoupper($period->group->name) }}</h2>
            <p>Periode: <strong>{{ $period->period_name }}</strong> &bull; Status Tutup Buku: <strong>{{ strtoupper($period->status) }}</strong></p>
        </div>

        {{-- Meta Info --}}
        <div class="doc-meta">
            <div>
                <span>Iuran Wajib: <strong>Rp {{ number_format($period->effective_due_amount, 0, ',', '.') }}</strong> / anggota</span>
            </div>
            <div>
                <span>Dokumen dicetak: <strong>{{ now()->translatedFormat('d F Y, H:i') }} WIB</strong></span>
            </div>
        </div>

        {{-- Summary Cards --}}
        <div class="summary-grid">
            <div class="summary-card">
                <div class="summary-label">Total Pemasukan</div>
                <div class="summary-value" style="color: #059669;">Rp {{ number_format($period->total_income, 0, ',', '.') }}</div>
                <div style="font-size: 7.5pt; color: #64748b; margin-top: 2px;">{{ $period->incomes->count() }} setoran anggota</div>
            </div>
            <div class="summary-card">
                <div class="summary-label">Total Pengeluaran</div>
                <div class="summary-value" style="color: #dc2626;">Rp {{ number_format($period->total_expense, 0, ',', '.') }}</div>
                <div style="font-size: 7.5pt; color: #64748b; margin-top: 2px;">{{ $period->expenses->count() }} transaksi</div>
            </div>
            <div class="summary-card">
                <div class="summary-label">Sisa Saldo Kas</div>
                <div class="summary-value" style="color: {{ $period->balance >= 0 ? '#2563eb' : '#dc2626' }};">
                    Rp {{ number_format($period->balance, 0, ',', '.') }}
                </div>
                <div style="font-size: 7.5pt; font-weight: bold; color: {{ $period->balance >= 0 ? '#2563eb' : '#dc2626' }}; margin-top: 2px;">
                    {{ $period->balance >= 0 ? 'Surplus' : 'Defisit' }}
                </div>
            </div>
        </div>

        {{-- Ringkasan Kategori (Optional) --}}
        <div x-show="showCategorySummary" style="margin-bottom: 18px;">
            <div class="section-header">
                <span>1. Rekapitulasi Alokasi Pengeluaran per Kategori</span>
            </div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 35px;" class="text-center">No</th>
                        <th>Pos / Kategori Pengeluaran</th>
                        <th style="width: 90px;" class="text-center">Jumlah Transaksi</th>
                        <th style="width: 140px;" class="text-right">Total Biaya (Rp)</th>
                        <th style="width: 80px;" class="text-right">Porsi (%)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categorySummary as $i => $cat)
                    <tr>
                        <td class="text-center">{{ $i + 1 }}</td>
                        <td><strong>{{ $cat['name'] }}</strong></td>
                        <td class="text-center">{{ $cat['count'] }} item</td>
                        <td class="text-right">{{ number_format($cat['total'], 0, ',', '.') }}</td>
                        <td class="text-right">{{ $cat['percentage'] }}%</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center">Belum ada pengeluaran tercatat.</td></tr>
                    @endforelse
                    @if($categorySummary->isNotEmpty())
                    <tr class="total-row">
                        <td colspan="3" class="text-right">TOTAL</td>
                        <td class="text-right">Rp {{ number_format($period->total_expense, 0, ',', '.') }}</td>
                        <td class="text-right">100%</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>

        {{-- Daftar Pemasukan --}}
        <div class="section-header">
            <span>2. Rincian Pemasukan (Setoran Anggota)</span>
            <span style="font-size: 8.5pt; font-weight: normal; color: #64748b;">Subtotal: Rp {{ number_format($period->total_income, 0, ',', '.') }}</span>
        </div>
        <table>
            <thead>
                <tr>
                    <th style="width: 35px;" class="text-center">No</th>
                    <th style="width: 85px;">Tanggal</th>
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
                    <td><strong>{{ $inc->member->name ?? '—' }}</strong></td>
                    <td class="text-right">{{ number_format($inc->nominal, 0, ',', '.') }}</td>
                    <td style="color: #64748b;">{{ $inc->note ?? '-' }}</td>
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

        {{-- Daftar Pengeluaran --}}
        <div class="section-header">
            <span>3. Rincian Pengeluaran Kas</span>
            <span style="font-size: 8.5pt; font-weight: normal; color: #64748b;">Subtotal: Rp {{ number_format($period->total_expense, 0, ',', '.') }}</span>
        </div>
        <table>
            <thead>
                <tr>
                    <th style="width: 35px;" class="text-center">No</th>
                    <th style="width: 85px;">Tanggal</th>
                    <th>Keperluan / Nama Barang</th>
                    <th style="width: 110px;">Kategori</th>
                    <th style="width: 130px;" class="text-right">Nominal (Rp)</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($period->expenses as $i => $exp)
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td>{{ $exp->transaction_date->format('d/m/Y') }}</td>
                    <td><strong>{{ $exp->item_name }}</strong></td>
                    <td>{{ $exp->category->name ?? 'Umum' }}</td>
                    <td class="text-right">{{ number_format($exp->nominal, 0, ',', '.') }}</td>
                    <td style="color: #64748b;">{{ $exp->note ?? '-' }}</td>
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

        {{-- Lampiran Foto Bukti Nota (Optional) --}}
        @php
            $allAttachments = $period->expenses->flatMap->attachments;
        @endphp
        @if($allAttachments->isNotEmpty())
        <div x-show="showAttachments" style="margin-top: 25px; page-break-before: auto;">
            <div class="section-header">
                <span>4. Lampiran Bukti Nota & Dokumentasi Belanja ({{ $allAttachments->count() }} Foto)</span>
            </div>
            <div class="attachments-grid">
                @foreach($allAttachments as $att)
                <div class="attachment-item">
                    <img src="{{ asset('storage/' . $att->file_path) }}" alt="Bukti">
                    <div class="attachment-caption">{{ $att->expense->item_name ?? 'Bukti Nota' }}</div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Kolom Tanda Tangan (Optional) --}}
        <div x-show="showSignatures" class="signatures">
            <div class="sign-col">
                <p>Mengetahui,<br><strong>Ketua Pengurus</strong></p>
                <div class="sign-space"></div>
                <div class="sign-name"></div>
            </div>
            <div class="sign-col">
                <p>Dibuat & Diverifikasi Oleh,<br><strong>Bendahara Kas</strong></p>
                <div class="sign-space"></div>
                <div class="sign-name">( {{ $period->creator->name ?? 'Bendahara' }} )</div>
            </div>
        </div>
    </div>
</body>
</html>
