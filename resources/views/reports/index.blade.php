@extends('layouts.app')
@section('title', 'Laporan Kas Bulanan')
@section('header', 'Laporan Kas & Export')

@section('content')
{{-- Filter Selector --}}
<div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4 mb-6">
    <form method="GET" class="flex flex-wrap items-center gap-3">
        <div>
            <label class="block text-xs text-slate-500 mb-1">Grup Kas</label>
            <select name="group_id" onchange="this.form.submit()" class="rounded-lg border border-slate-300 px-3 py-1.5 text-sm">
                @foreach($groups as $g)
                    <option value="{{ $g->id }}" {{ $selectedGroupId == $g->id ? 'selected' : '' }}>{{ $g->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs text-slate-500 mb-1">Pilih Periode</label>
            <select name="period_id" onchange="this.form.submit()" class="rounded-lg border border-slate-300 px-3 py-1.5 text-sm">
                @foreach($periods as $p)
                    <option value="{{ $p->id }}" {{ $selectedPeriodId == $p->id ? 'selected' : '' }}>{{ $p->period_name }} ({{ strtoupper($p->status) }})</option>
                @endforeach
            </select>
        </div>

        @if($period)
        <div class="ml-auto flex items-center gap-2 pt-4 sm:pt-0">
            <a href="{{ route('reports.export-excel', $period) }}" class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-3 py-2 text-xs font-semibold text-white hover:bg-emerald-700 transition-colors">
                📥 Export Excel (CSV)
            </a>
            <a href="{{ route('reports.print', $period) }}" target="_blank" class="inline-flex items-center gap-1.5 rounded-lg bg-slate-700 px-3 py-2 text-xs font-semibold text-white hover:bg-slate-800 transition-colors">
                🖨️ Cetak / PDF
            </a>
        </div>
        @endif
    </form>
</div>

@if($period)
{{-- Report Header Banner --}}
<div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 mb-6">
    <div class="border-b border-slate-100 pb-4 mb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
        <div>
            <h2 class="text-xl font-bold text-slate-800">REKAPITULASI KAS: {{ strtoupper($period->group->name) }}</h2>
            <p class="text-sm text-slate-500">Periode: <strong class="text-slate-700">{{ $period->period_name }}</strong> &bull; Status: <span class="uppercase font-semibold text-xs px-2 py-0.5 rounded bg-slate-100">{{ $period->status }}</span></p>
        </div>
        <div class="text-right">
            <p class="text-xs text-slate-400">Iuran per Anggota</p>
            <p class="text-sm font-semibold text-slate-700">Rp {{ number_format($period->effective_due_amount, 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Financial Summary Box --}}
    <div class="grid grid-cols-3 gap-4 mb-6 bg-slate-50 rounded-lg p-4 text-center">
        <div>
            <p class="text-xs font-medium text-slate-500 uppercase">Total Pemasukan</p>
            <p class="text-lg font-bold text-emerald-600 mt-0.5">Rp {{ number_format($period->total_income, 0, ',', '.') }}</p>
            <p class="text-[11px] text-slate-400">{{ $period->incomes->count() }} transaksi</p>
        </div>
        <div>
            <p class="text-xs font-medium text-slate-500 uppercase">Total Pengeluaran</p>
            <p class="text-lg font-bold text-red-500 mt-0.5">Rp {{ number_format($period->total_expense, 0, ',', '.') }}</p>
            <p class="text-[11px] text-slate-400">{{ $period->expenses->count() }} transaksi</p>
        </div>
        <div>
            <p class="text-xs font-medium text-slate-500 uppercase">Sisa / Saldo Bersih</p>
            <p class="text-lg font-bold {{ $period->balance >= 0 ? 'text-blue-600' : 'text-red-600' }} mt-0.5">
                Rp {{ number_format($period->balance, 0, ',', '.') }}
            </p>
            <p class="text-[11px] text-slate-400">{{ $period->balance >= 0 ? 'Surplus' : 'Defisit' }}</p>
        </div>
    </div>

    {{-- Tables Grid --}}
    <div class="grid lg:grid-cols-2 gap-6">
        {{-- Incomes Table --}}
        <div>
            <h3 class="text-sm font-semibold text-slate-700 mb-3 flex items-center gap-2">
                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                Daftar Setoran Pemasukan
            </h3>
            <div class="border border-slate-200 rounded-lg overflow-hidden">
                <table class="w-full text-xs">
                    <thead class="bg-slate-100">
                        <tr>
                            <th class="px-3 py-2 text-left font-medium text-slate-600">No</th>
                            <th class="px-3 py-2 text-left font-medium text-slate-600">Nama Anggota</th>
                            <th class="px-3 py-2 text-left font-medium text-slate-600">Tanggal</th>
                            <th class="px-3 py-2 text-right font-medium text-slate-600">Nominal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($period->incomes as $idx => $inc)
                        <tr>
                            <td class="px-3 py-2 text-slate-400">{{ $idx + 1 }}</td>
                            <td class="px-3 py-2 font-medium text-slate-800">{{ $inc->member->name ?? '—' }}</td>
                            <td class="px-3 py-2 text-slate-500">{{ $inc->transaction_date->format('d/m') }}</td>
                            <td class="px-3 py-2 text-right font-semibold text-emerald-600">Rp {{ number_format($inc->nominal, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="px-3 py-6 text-center text-slate-400">Tidak ada pemasukan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Expenses Table --}}
        <div>
            <h3 class="text-sm font-semibold text-slate-700 mb-3 flex items-center gap-2">
                <span class="h-2 w-2 rounded-full bg-red-500"></span>
                Daftar Pengeluaran
            </h3>
            <div class="border border-slate-200 rounded-lg overflow-hidden">
                <table class="w-full text-xs">
                    <thead class="bg-slate-100">
                        <tr>
                            <th class="px-3 py-2 text-left font-medium text-slate-600">No</th>
                            <th class="px-3 py-2 text-left font-medium text-slate-600">Keperluan</th>
                            <th class="px-3 py-2 text-left font-medium text-slate-600">Kategori</th>
                            <th class="px-3 py-2 text-right font-medium text-slate-600">Nominal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($period->expenses as $idx => $exp)
                        <tr>
                            <td class="px-3 py-2 text-slate-400">{{ $idx + 1 }}</td>
                            <td class="px-3 py-2 font-medium text-slate-800">{{ $exp->item_name }}</td>
                            <td class="px-3 py-2 text-slate-500">{{ $exp->category->name ?? 'Umum' }}</td>
                            <td class="px-3 py-2 text-right font-semibold text-red-600">Rp {{ number_format($exp->nominal, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="px-3 py-6 text-center text-slate-400">Tidak ada pengeluaran.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Expense Documentation Gallery --}}
    @php
        $allAttachments = $period->expenses->flatMap->attachments;
    @endphp
    @if($allAttachments->isNotEmpty())
    <div class="mt-8 pt-6 border-t border-slate-100">
        <h3 class="text-sm font-semibold text-slate-700 mb-3">📸 Galeri Bukti Nota & Dokumentasi Pengeluaran</h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-3">
            @foreach($allAttachments as $att)
            <div class="group relative rounded-lg border border-slate-200 overflow-hidden bg-slate-100 aspect-square">
                <img src="{{ asset('storage/' . $att->file_path) }}" alt="Bukti" class="h-full w-full object-cover">
                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-2 text-[10px] text-white">
                    <span class="truncate">{{ $att->expense->item_name ?? 'Bukti Nota' }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@else
<div class="bg-white rounded-xl border border-slate-200 shadow-sm p-12 text-center text-slate-400">
    Pilih grup dan periode untuk melihat rekapitulasi laporan kas.
</div>
@endif
@endsection
