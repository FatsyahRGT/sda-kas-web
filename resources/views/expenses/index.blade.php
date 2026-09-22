@extends('layouts.app')
@section('title', 'Pengeluaran Kas')
@section('header', 'Pencatatan Pengeluaran Kas')
@section('header-actions')
    <a href="{{ route('expenses.create') }}" class="inline-flex items-center gap-1.5 rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 shadow-xs transition-colors">
        <i class="bi bi-dash-circle-fill"></i> Catat Pengeluaran
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
        <a href="{{ route('expenses.index') }}" class="inline-flex items-center gap-1 rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors shadow-xs">
            <i class="bi bi-arrow-counterclockwise"></i> Reset
        </a>
    @endif
</form>

{{-- Summary Banner --}}
<div class="bg-gradient-to-r from-red-50 to-rose-50 border border-red-200/80 rounded-2xl p-4 sm:p-5 mb-5 flex items-center justify-between shadow-xs">
    <div>
        <p class="text-xs font-bold text-red-800 uppercase tracking-wider">Total Pengeluaran Ditampilkan</p>
        <p class="text-2xl font-extrabold text-red-700 mt-0.5">Rp {{ number_format($totalNominal, 0, ',', '.') }}</p>
    </div>
    <div class="h-12 w-12 rounded-2xl bg-red-100 text-red-600 flex items-center justify-center text-2xl shadow-inner">
        <i class="bi bi-receipt-cutoff"></i>
    </div>
</div>

<div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden" x-data="{ modalPhoto: null }">
    <table class="w-full text-sm">
        <thead class="bg-slate-50">
            <tr>
                <th class="text-left font-semibold text-slate-600 px-5 py-3.5 text-xs uppercase tracking-wider">Tanggal</th>
                <th class="text-left font-semibold text-slate-600 px-3 py-3.5 text-xs uppercase tracking-wider">Barang / Keperluan</th>
                <th class="text-left font-semibold text-slate-600 px-3 py-3.5 text-xs uppercase tracking-wider hidden sm:table-cell">Kategori</th>
                <th class="text-right font-semibold text-slate-600 px-3 py-3.5 text-xs uppercase tracking-wider">Nominal</th>
                <th class="text-center font-semibold text-slate-600 px-3 py-3.5 text-xs uppercase tracking-wider">Bukti Nota</th>
                <th class="px-5 py-3.5"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($expenses as $expense)
            <tr class="hover:bg-slate-50 transition-colors">
                <td class="px-5 py-3.5 text-slate-600 font-mono text-xs whitespace-nowrap">
                    <span class="flex items-center gap-1.5"><i class="bi bi-calendar-event text-slate-400"></i> {{ $expense->transaction_date->format('d/m/Y') }}</span>
                </td>
                <td class="px-3 py-3.5">
                    <p class="font-bold text-slate-800">{{ $expense->item_name }}</p>
                    <p class="text-xs text-slate-400">{{ $expense->period->period_name }} ({{ $expense->period->group->name }})</p>
                    @if($expense->note)
                        <p class="text-xs text-slate-500 mt-0.5 flex items-center gap-1"><i class="bi bi-info-circle text-[10px]"></i> {{ $expense->note }}</p>
                    @endif
                </td>
                <td class="px-3 py-3.5 text-slate-600 hidden sm:table-cell text-xs">
                    <span class="inline-flex items-center gap-1 rounded-lg bg-slate-100 border border-slate-200/60 px-2.5 py-1 text-slate-700 font-medium">
                        <i class="bi bi-tag-fill text-slate-400 text-[10px]"></i>
                        {{ $expense->category->name ?? 'Umum' }}
                    </span>
                </td>
                <td class="px-3 py-3.5 text-right font-bold text-red-600 whitespace-nowrap text-sm">
                    Rp {{ number_format($expense->nominal, 0, ',', '.') }}
                </td>
                <td class="px-3 py-3.5 text-center">
                    @if($expense->attachments->isNotEmpty())
                        <div class="flex items-center justify-center gap-1.5">
                            @foreach($expense->attachments as $att)
                                <button type="button" @click="modalPhoto = '{{ asset('storage/' . $att->file_path) }}'"
                                    class="h-8 w-8 rounded-lg overflow-hidden border border-slate-200 hover:scale-105 transition-transform shadow-2xs">
                                    <img src="{{ asset('storage/' . $att->file_path) }}" alt="Bukti" class="h-full w-full object-cover">
                                </button>
                            @endforeach
                        </div>
                    @else
                        <span class="text-xs text-slate-300">—</span>
                    @endif
                </td>
                <td class="px-5 py-3.5">
                    <div class="flex items-center gap-2 justify-end">
                        <a href="{{ route('expenses.edit', $expense) }}" class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2.5 py-1 text-xs text-blue-600 hover:bg-blue-50 font-semibold transition-colors">
                            <i class="bi bi-pencil-square"></i> Edit
                        </a>
                        <form method="POST" action="{{ route('expenses.destroy', $expense) }}" data-confirm="true" data-confirm-title="Hapus Pengeluaran?" data-confirm-text="Catatan pengeluaran '{{ $expense->item_name }}' dan semua lampiran buktinya akan dihapus!">
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
                    <i class="bi bi-receipt text-3xl block mb-2 text-slate-300"></i>
                    Belum ada data pengeluaran kas.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($expenses->hasPages())
    <div class="border-t border-slate-100 px-5 py-3">{{ $expenses->links() }}</div>
    @endif

    {{-- Photo Lightbox Modal --}}
    <div x-show="modalPhoto" x-transition.opacity
        class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-xs flex items-center justify-center p-4"
        @click="modalPhoto = null" style="display: none;">
        <div class="relative max-w-2xl max-h-[90vh] bg-white rounded-2xl overflow-hidden p-3 shadow-2xl" @click.stop>
            <button @click="modalPhoto = null" class="absolute top-4 right-4 h-8 w-8 rounded-full bg-slate-900/70 hover:bg-slate-900 text-white flex items-center justify-center text-sm transition-colors">
                <i class="bi bi-x-lg"></i>
            </button>
            <img :src="modalPhoto" alt="Preview Bukti" class="max-h-[80vh] w-auto mx-auto rounded-xl">
        </div>
    </div>
</div>
@endsection
