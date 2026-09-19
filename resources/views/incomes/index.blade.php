@extends('layouts.app')
@section('title', 'Pemasukan Kas')
@section('header', 'Pencatatan Pemasukan (Iuran Anggota)')
@section('header-actions')
    <a href="{{ route('incomes.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 transition-colors">
        + Input Setoran Kas
    </a>
@endsection

@section('content')
{{-- Filter Form --}}
<form method="GET" class="flex flex-wrap gap-3 mb-5">
    <select name="group_id" onchange="this.form.submit()" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
        <option value="">Semua Grup</option>
        @foreach($groups as $g)
            <option value="{{ $g->id }}" {{ $groupId == $g->id ? 'selected' : '' }}>{{ $g->name }}</option>
        @endforeach
    </select>
    <select name="period_id" onchange="this.form.submit()" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
        <option value="">Semua Periode</option>
        @foreach($periods as $p)
            <option value="{{ $p->id }}" {{ $periodId == $p->id ? 'selected' : '' }}>{{ $p->period_name }} ({{ $p->group->name }})</option>
        @endforeach
    </select>
    @if($groupId || $periodId)
        <a href="{{ route('incomes.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50">Reset</a>
    @endif
</form>

{{-- Summary Banner --}}
<div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 mb-5 flex items-center justify-between">
    <div>
        <p class="text-xs font-medium text-emerald-700 uppercase">Total Pemasukan Ditampilkan</p>
        <p class="text-xl font-bold text-emerald-800">Rp {{ number_format($totalNominal, 0, ',', '.') }}</p>
    </div>
    <span class="text-2xl">💰</span>
</div>

<div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-50">
            <tr>
                <th class="text-left font-medium text-slate-500 px-5 py-3 text-xs uppercase tracking-wide">Tanggal</th>
                <th class="text-left font-medium text-slate-500 px-3 py-3 text-xs uppercase tracking-wide">Anggota</th>
                <th class="text-left font-medium text-slate-500 px-3 py-3 text-xs uppercase tracking-wide hidden sm:table-cell">Periode & Grup</th>
                <th class="text-right font-medium text-slate-500 px-3 py-3 text-xs uppercase tracking-wide">Nominal</th>
                <th class="text-left font-medium text-slate-500 px-3 py-3 text-xs uppercase tracking-wide hidden md:table-cell">Keterangan</th>
                <th class="px-5 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($incomes as $income)
            <tr class="hover:bg-slate-50">
                <td class="px-5 py-3 text-slate-600 font-mono text-xs whitespace-nowrap">{{ $income->transaction_date->format('d/m/Y H:i') }}</td>
                <td class="px-3 py-3 font-medium text-slate-800">{{ $income->member->name ?? '—' }}</td>
                <td class="px-3 py-3 text-slate-500 hidden sm:table-cell">
                    <p>{{ $income->period->period_name }}</p>
                    <p class="text-xs text-slate-400">{{ $income->period->group->name }}</p>
                </td>
                <td class="px-3 py-3 text-right font-semibold text-emerald-600 whitespace-nowrap">
                    Rp {{ number_format($income->nominal, 0, ',', '.') }}
                </td>
                <td class="px-3 py-3 text-slate-500 hidden md:table-cell text-xs">{{ $income->note ?? '—' }}</td>
                <td class="px-5 py-3">
                    <div class="flex items-center gap-2 justify-end">
                        <a href="{{ route('incomes.edit', $income) }}" class="text-xs text-blue-600 hover:text-blue-800 font-medium">Edit</a>
                        <form method="POST" action="{{ route('incomes.destroy', $income) }}" onsubmit="return confirm('Hapus catatan setoran ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-medium">Hapus</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-5 py-12 text-center text-sm text-slate-400">Belum ada catatan setoran kas.</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($incomes->hasPages())
    <div class="border-t border-slate-100 px-5 py-3">{{ $incomes->links() }}</div>
    @endif
</div>
@endsection
