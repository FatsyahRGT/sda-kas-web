@extends('layouts.app')
@section('title', 'Pengeluaran Kas')
@section('header', 'Pencatatan Pengeluaran Kas')
@section('header-actions')
    <a href="{{ route('expenses.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 transition-colors">
        + Catat Pengeluaran
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
        <a href="{{ route('expenses.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50">Reset</a>
    @endif
</form>

{{-- Summary Banner --}}
<div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-5 flex items-center justify-between">
    <div>
        <p class="text-xs font-medium text-red-700 uppercase">Total Pengeluaran Ditampilkan</p>
        <p class="text-xl font-bold text-red-800">Rp {{ number_format($totalNominal, 0, ',', '.') }}</p>
    </div>
    <span class="text-2xl">🧾</span>
</div>

<div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden" x-data="{ modalPhoto: null }">
    <table class="w-full text-sm">
        <thead class="bg-slate-50">
            <tr>
                <th class="text-left font-medium text-slate-500 px-5 py-3 text-xs uppercase tracking-wide">Tanggal</th>
                <th class="text-left font-medium text-slate-500 px-3 py-3 text-xs uppercase tracking-wide">Barang / Keperluan</th>
                <th class="text-left font-medium text-slate-500 px-3 py-3 text-xs uppercase tracking-wide hidden sm:table-cell">Kategori</th>
                <th class="text-right font-medium text-slate-500 px-3 py-3 text-xs uppercase tracking-wide">Nominal</th>
                <th class="text-center font-medium text-slate-500 px-3 py-3 text-xs uppercase tracking-wide">Bukti</th>
                <th class="px-5 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($expenses as $expense)
            <tr class="hover:bg-slate-50">
                <td class="px-5 py-3 text-slate-600 font-mono text-xs whitespace-nowrap">{{ $expense->transaction_date->format('d/m/Y') }}</td>
                <td class="px-3 py-3">
                    <p class="font-medium text-slate-800">{{ $expense->item_name }}</p>
                    <p class="text-xs text-slate-400">{{ $expense->period->period_name }} ({{ $expense->period->group->name }})</p>
                    @if($expense->note)
                        <p class="text-xs text-slate-500 mt-0.5">{{ $expense->note }}</p>
                    @endif
                </td>
                <td class="px-3 py-3 text-slate-500 hidden sm:table-cell text-xs">
                    <span class="inline-flex rounded bg-slate-100 px-2 py-0.5 text-slate-600">
                        {{ $expense->category->name ?? 'Umum' }}
                    </span>
                </td>
                <td class="px-3 py-3 text-right font-semibold text-red-600 whitespace-nowrap">
                    Rp {{ number_format($expense->nominal, 0, ',', '.') }}
                </td>
                <td class="px-3 py-3 text-center">
                    @if($expense->attachments->isNotEmpty())
                        <div class="flex items-center justify-center gap-1">
                            @foreach($expense->attachments as $att)
                                <button type="button" @click="modalPhoto = '{{ asset('storage/' . $att->file_path) }}'"
                                    class="h-7 w-7 rounded overflow-hidden border border-slate-200 hover:opacity-80 transition-opacity">
                                    <img src="{{ asset('storage/' . $att->file_path) }}" alt="Bukti" class="h-full w-full object-cover">
                                </button>
                            @endforeach
                        </div>
                    @else
                        <span class="text-xs text-slate-300">—</span>
                    @endif
                </td>
                <td class="px-5 py-3">
                    <div class="flex items-center gap-2 justify-end">
                        <a href="{{ route('expenses.edit', $expense) }}" class="text-xs text-blue-600 hover:text-blue-800 font-medium">Edit</a>
                        <form method="POST" action="{{ route('expenses.destroy', $expense) }}" onsubmit="return confirm('Hapus pengeluaran ini dan semua bukti lampirannya?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-medium">Hapus</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-5 py-12 text-center text-sm text-slate-400">Belum ada data pengeluaran kas.</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($expenses->hasPages())
    <div class="border-t border-slate-100 px-5 py-3">{{ $expenses->links() }}</div>
    @endif

    {{-- Photo Lightbox Modal --}}
    <div x-show="modalPhoto" x-transition.opacity
        class="fixed inset-0 z-50 bg-black/80 flex items-center justify-center p-4"
        @click="modalPhoto = null" style="display: none;">
        <div class="relative max-w-2xl max-h-[90vh] bg-white rounded-xl overflow-hidden p-2" @click.stop>
            <button @click="modalPhoto = null" class="absolute top-3 right-3 h-8 w-8 rounded-full bg-black/50 text-white flex items-center justify-center text-sm hover:bg-black/70">✕</button>
            <img :src="modalPhoto" alt="Preview Bukti" class="max-h-[80vh] w-auto mx-auto rounded">
        </div>
    </div>
</div>
@endsection
