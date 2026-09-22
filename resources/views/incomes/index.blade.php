@extends('layouts.app')
@section('title', 'Pemasukan Kas')
@section('header', 'Pencatatan Pemasukan (Iuran Anggota)')
@section('header-actions')
    <a href="{{ route('incomes.create') }}" class="inline-flex items-center gap-1.5 rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 shadow-xs transition-colors">
        <i class="bi bi-plus-circle-fill"></i> Input Setoran Kas
    </a>
@endsection

@section('content')
{{-- Filter Form --}}
<form method="GET" class="flex flex-wrap gap-2.5 mb-5">
    <select name="group_id" onchange="this.form.submit()" class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 shadow-xs">
        <option value="">Semua Grup</option>
        @foreach($groups as $g)
            <option value="{{ $g->id }}" {{ $groupId == $g->id ? 'selected' : '' }}>{{ $g->name }}</option>
        @endforeach
    </select>
    <select name="period_id" onchange="this.form.submit()" class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 shadow-xs">
        <option value="">Semua Periode</option>
        @foreach($periods as $p)
            <option value="{{ $p->id }}" {{ $periodId == $p->id ? 'selected' : '' }}>{{ $p->period_name }} ({{ $p->group->name }})</option>
        @endforeach
    </select>
    @if($groupId || $periodId)
        <a href="{{ route('incomes.index') }}" class="inline-flex items-center gap-1 rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors shadow-xs">
            <i class="bi bi-arrow-counterclockwise"></i> Reset
        </a>
    @endif
</form>

{{-- Summary Banner --}}
<div class="bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-200/80 rounded-2xl p-4 sm:p-5 mb-5 flex items-center justify-between shadow-xs">
    <div>
        <p class="text-xs font-bold text-emerald-800 uppercase tracking-wider">Total Pemasukan Ditampilkan</p>
        <p class="text-2xl font-extrabold text-emerald-700 mt-0.5">Rp {{ number_format($totalNominal, 0, ',', '.') }}</p>
    </div>
    <div class="h-12 w-12 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-2xl shadow-inner">
        <i class="bi bi-cash-stack"></i>
    </div>
</div>

<div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-50">
            <tr>
                <th class="text-left font-semibold text-slate-600 px-5 py-3.5 text-xs uppercase tracking-wider">Tanggal & Waktu</th>
                <th class="text-left font-semibold text-slate-600 px-3 py-3.5 text-xs uppercase tracking-wider">Nama Anggota</th>
                <th class="text-left font-semibold text-slate-600 px-3 py-3.5 text-xs uppercase tracking-wider hidden sm:table-cell">Periode & Grup</th>
                <th class="text-right font-semibold text-slate-600 px-3 py-3.5 text-xs uppercase tracking-wider">Nominal</th>
                <th class="text-left font-semibold text-slate-600 px-3 py-3.5 text-xs uppercase tracking-wider hidden md:table-cell">Keterangan</th>
                <th class="px-5 py-3.5"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($incomes as $income)
            <tr class="hover:bg-slate-50 transition-colors">
                <td class="px-5 py-3.5 text-slate-600 font-mono text-xs whitespace-nowrap">
                    <span class="flex items-center gap-1.5"><i class="bi bi-clock text-slate-400"></i> {{ $income->transaction_date->format('d/m/Y H:i') }}</span>
                </td>
                <td class="px-3 py-3.5 font-bold text-slate-800">{{ $income->member->name ?? '—' }}</td>
                <td class="px-3 py-3.5 text-slate-600 hidden sm:table-cell">
                    <p class="font-medium">{{ $income->period->period_name }}</p>
                    <p class="text-xs text-slate-400">{{ $income->period->group->name }}</p>
                </td>
                <td class="px-3 py-3.5 text-right font-bold text-emerald-600 whitespace-nowrap text-sm">
                    Rp {{ number_format($income->nominal, 0, ',', '.') }}
                </td>
                <td class="px-3 py-3.5 text-slate-500 hidden md:table-cell text-xs">{{ $income->note ?? '—' }}</td>
                <td class="px-5 py-3.5">
                    <div class="flex items-center gap-2 justify-end">
                        <a href="{{ route('incomes.edit', $income) }}" class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2.5 py-1 text-xs text-blue-600 hover:bg-blue-50 font-semibold transition-colors">
                            <i class="bi bi-pencil-square"></i> Edit
                        </a>
                        <form method="POST" action="{{ route('incomes.destroy', $income) }}" data-confirm="true" data-confirm-title="Hapus Catatan Setoran?" data-confirm-text="Catatan setoran sebesar Rp {{ number_format($income->nominal, 0, ',', '.') }} ini akan dihapus!">
                            @csrf @method('DELETE')
                            <button type="submit" class="inline-flex items-center gap-1 rounded-lg border border-red-200 bg-red-50/50 px-2.5 py-1 text-xs text-red-600 hover:bg-red-100 font-semibold transition-colors">
                                <i class="bi bi-trash3"></i> Hapus
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-5 py-12 text-center text-sm text-slate-400">
                    <i class="bi bi-inbox text-3xl block mb-2 text-slate-300"></i>
                    Belum ada catatan setoran kas.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($incomes->hasPages())
    <div class="border-t border-slate-100 px-5 py-3">{{ $incomes->links() }}</div>
    @endif
</div>
@endsection
