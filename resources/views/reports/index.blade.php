@extends('layouts.app')
@section('title', 'Laporan Kas Bulanan')
@section('header', 'Laporan Kas & Export')

@section('content')
{{-- Filter Selector --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-4 sm:p-5 mb-6">
    <form method="GET" class="flex flex-wrap items-center gap-3">
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1">Grup Kas</label>
            <select name="group_id" onchange="this.form.submit()" class="rounded-xl border border-slate-300 bg-white px-3 py-1.5 text-sm font-medium text-slate-700 shadow-xs">
                @foreach($groups as $g)
                    <option value="{{ $g->id }}" {{ $selectedGroupId == $g->id ? 'selected' : '' }}>{{ $g->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1">Pilih Periode</label>
            <select name="period_id" onchange="this.form.submit()" class="rounded-xl border border-slate-300 bg-white px-3 py-1.5 text-sm font-medium text-slate-700 shadow-xs">
                @foreach($periods as $p)
                    <option value="{{ $p->id }}" {{ $selectedPeriodId == $p->id ? 'selected' : '' }}>{{ $p->period_name }} ({{ strtoupper($p->status) }})</option>
                @endforeach
            </select>
        </div>

        @if($period)
        <div class="ml-auto flex items-center gap-2 pt-2 sm:pt-0">
            <a href="{{ route('reports.export-excel', $period) }}" class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-600 px-3.5 py-2 text-xs font-semibold text-white hover:bg-emerald-700 shadow-xs transition-colors">
                <i class="bi bi-file-earmark-excel-fill text-sm"></i> Export Excel (CSV)
            </a>
            <a href="{{ route('reports.print', $period) }}" target="_blank" class="inline-flex items-center gap-1.5 rounded-xl bg-slate-800 px-3.5 py-2 text-xs font-semibold text-white hover:bg-slate-900 shadow-xs transition-colors">
                <i class="bi bi-printer-fill text-sm"></i> Cetak / PDF
            </a>
        </div>
        @endif
    </form>
</div>

@if($period)
{{-- Report Header Banner --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 mb-6">
    <div class="border-b border-slate-100 pb-4 mb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
        <div>
            <h2 class="text-lg sm:text-xl font-extrabold text-slate-900">REKAPITULASI KAS: {{ strtoupper($period->group->name) }}</h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                Periode: <strong class="text-slate-800">{{ $period->period_name }}</strong> &bull; 
                Status: <span class="uppercase font-bold text-[11px] px-2 py-0.5 rounded-full bg-slate-100 text-slate-700">{{ $period->status }}</span>
            </p>
        </div>
        <div class="sm:text-right">
            <p class="text-xs text-slate-400">Iuran per Anggota</p>
            <p class="text-sm font-bold text-slate-800">Rp {{ number_format($period->effective_due_amount, 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Financial Summary Box --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-6 bg-slate-50 border border-slate-200/60 rounded-xl p-4 text-center">
        <div class="p-2">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Pemasukan</p>
            <p class="text-xl font-extrabold text-emerald-600 mt-1">Rp {{ number_format($period->total_income, 0, ',', '.') }}</p>
            <p class="text-[11px] text-slate-400 mt-0.5">{{ $period->incomes->count() }} setoran anggota</p>
        </div>
        <div class="p-2 border-y sm:border-y-0 sm:border-x border-slate-200">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Pengeluaran</p>
            <p class="text-xl font-extrabold text-red-500 mt-1">Rp {{ number_format($period->total_expense, 0, ',', '.') }}</p>
            <p class="text-[11px] text-slate-400 mt-0.5">{{ $period->expenses->count() }} transaksi pengeluaran</p>
        </div>
        <div class="p-2">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Sisa Saldo Bersih</p>
            <p class="text-xl font-extrabold {{ $period->balance >= 0 ? 'text-blue-600' : 'text-red-600' }} mt-1">
                Rp {{ number_format($period->balance, 0, ',', '.') }}
            </p>
            <p class="text-[11px] font-semibold {{ $period->balance >= 0 ? 'text-blue-500' : 'text-red-500' }} mt-0.5">
                {{ $period->balance >= 0 ? 'Surplus' : 'Defisit' }}
            </p>
        </div>
    </div>

    {{-- Tables Grid --}}
    <div class="grid lg:grid-cols-2 gap-6">
        {{-- Incomes Table --}}
        <div>
            <h3 class="text-sm font-bold text-slate-800 mb-3 flex items-center gap-2">
                <i class="bi bi-arrow-down-left-circle-fill text-emerald-500 text-base"></i>
                Daftar Setoran Pemasukan
            </h3>
            <div class="border border-slate-200 rounded-xl overflow-hidden shadow-xs">
                <table class="w-full text-xs">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-3.5 py-2.5 text-left font-semibold text-slate-600">No</th>
                            <th class="px-3.5 py-2.5 text-left font-semibold text-slate-600">Nama Anggota</th>
                            <th class="px-3.5 py-2.5 text-left font-semibold text-slate-600">Tanggal</th>
                            <th class="px-3.5 py-2.5 text-right font-semibold text-slate-600">Nominal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($period->incomes as $idx => $inc)
                        <tr class="hover:bg-slate-50">
                            <td class="px-3.5 py-2 text-slate-400">{{ $idx + 1 }}</td>
                            <td class="px-3.5 py-2 font-bold text-slate-800">{{ $inc->member->name ?? '—' }}</td>
                            <td class="px-3.5 py-2 text-slate-500">{{ $inc->transaction_date->format('d/m') }}</td>
                            <td class="px-3.5 py-2 text-right font-bold text-emerald-600">Rp {{ number_format($inc->nominal, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="px-3.5 py-6 text-center text-slate-400">Tidak ada pemasukan tercatat.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Expenses Table --}}
        <div>
            <h3 class="text-sm font-bold text-slate-800 mb-3 flex items-center gap-2">
                <i class="bi bi-arrow-up-right-circle-fill text-red-500 text-base"></i>
                Daftar Pengeluaran
            </h3>
            <div class="border border-slate-200 rounded-xl overflow-hidden shadow-xs">
                <table class="w-full text-xs">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-3.5 py-2.5 text-left font-semibold text-slate-600">No</th>
                            <th class="px-3.5 py-2.5 text-left font-semibold text-slate-600">Keperluan</th>
                            <th class="px-3.5 py-2.5 text-left font-semibold text-slate-600">Kategori</th>
                            <th class="px-3.5 py-2.5 text-right font-semibold text-slate-600">Nominal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($period->expenses as $idx => $exp)
                        <tr class="hover:bg-slate-50">
                            <td class="px-3.5 py-2 text-slate-400">{{ $idx + 1 }}</td>
                            <td class="px-3.5 py-2 font-bold text-slate-800">{{ $exp->item_name }}</td>
                            <td class="px-3.5 py-2 text-slate-500">{{ $exp->category->name ?? 'Umum' }}</td>
                            <td class="px-3.5 py-2 text-right font-bold text-red-600">Rp {{ number_format($exp->nominal, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="px-3.5 py-6 text-center text-slate-400">Tidak ada pengeluaran tercatat.</td></tr>
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
        <h3 class="text-sm font-bold text-slate-800 mb-3 flex items-center gap-2">
            <i class="bi bi-images text-blue-600"></i> Galeri Bukti Nota & Dokumentasi Pengeluaran
        </h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-3">
            @foreach($allAttachments as $att)
            <div class="group relative rounded-xl border border-slate-200 overflow-hidden bg-slate-100 aspect-square shadow-2xs">
                <img src="{{ asset('storage/' . $att->file_path) }}" alt="Bukti" class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-200">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-2 text-[10px] text-white">
                    <span class="truncate font-medium">{{ $att->expense->item_name ?? 'Bukti Nota' }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@else
<div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-12 text-center text-slate-400">
    <i class="bi bi-file-earmark-bar-graph text-4xl block mb-2 text-slate-300"></i>
    Pilih grup dan periode untuk melihat rekapitulasi laporan kas.
</div>
@endif
@endsection
