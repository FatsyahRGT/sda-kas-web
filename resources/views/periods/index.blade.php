@extends('layouts.app')
@section('title', 'Periode Kas')
@section('header', 'Manajemen Periode Kas')
@section('header-actions')
    <a href="{{ route('periods.create') }}" class="inline-flex items-center gap-1.5 rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 shadow-xs transition-colors">
        <i class="bi bi-calendar-plus"></i> Buka Periode Baru
    </a>
@endsection

@section('content')
{{-- Filter Group --}}
<form method="GET" class="flex gap-3 mb-5">
    <select name="group_id" onchange="this.form.submit()" class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 shadow-xs">
        <option value="">Semua Grup</option>
        @foreach($groups as $g)
            <option value="{{ $g->id }}" {{ $groupId == $g->id ? 'selected' : '' }}>{{ $g->name }}</option>
        @endforeach
    </select>
</form>

<div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-50">
            <tr>
                <th class="text-left font-semibold text-slate-600 px-5 py-3.5 text-xs uppercase tracking-wider">Periode</th>
                <th class="text-left font-semibold text-slate-600 px-3 py-3.5 text-xs uppercase tracking-wider hidden sm:table-cell">Grup</th>
                <th class="text-right font-semibold text-slate-600 px-3 py-3.5 text-xs uppercase tracking-wider">Iuran/Org</th>
                <th class="text-right font-semibold text-slate-600 px-3 py-3.5 text-xs uppercase tracking-wider hidden md:table-cell">Saldo</th>
                <th class="text-center font-semibold text-slate-600 px-3 py-3.5 text-xs uppercase tracking-wider">Status</th>
                <th class="px-5 py-3.5"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($periods as $period)
            <tr class="hover:bg-slate-50 transition-colors">
                <td class="px-5 py-3.5">
                    <p class="font-bold text-slate-800">{{ $period->period_name }}</p>
                    <p class="text-xs text-slate-400 sm:hidden">{{ $period->group->name }}</p>
                </td>
                <td class="px-3 py-3.5 text-slate-600 hidden sm:table-cell">{{ $period->group->name }}</td>
                <td class="px-3 py-3.5 text-right font-medium text-slate-700">
                    Rp {{ number_format($period->effective_due_amount, 0, ',', '.') }}
                    @if($period->due_amount !== null)
                        <span class="text-[10px] text-amber-700 bg-amber-100 px-1.5 py-0.5 rounded font-semibold ml-1">khusus</span>
                    @endif
                </td>
                <td class="px-3 py-3.5 text-right hidden md:table-cell font-bold {{ $period->balance >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                    Rp {{ number_format($period->balance, 0, ',', '.') }}
                </td>
                <td class="px-3 py-3.5 text-center">
                    <div class="inline-flex items-center gap-1.5">
                        @php
                            $badgeClass = match($period->status) {
                                'closed' => 'bg-slate-100 text-slate-700 border-slate-300',
                                'open' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                                default => 'bg-amber-100 text-amber-800 border-amber-300',
                            };
                            $iconName = match($period->status) {
                                'closed' => 'bi-lock-fill',
                                'open' => 'bi-unlock-fill',
                                default => 'bi-pencil-fill',
                            };
                        @endphp
                        <span class="inline-flex items-center gap-1 rounded-full border px-2.5 py-0.5 text-xs font-bold uppercase {{ $badgeClass }}">
                            <i class="bi {{ $iconName }} text-[10px]"></i>
                            {{ $period->status }}
                        </span>
                        {{-- Quick status changer --}}
                        <form method="POST" action="{{ route('periods.update-status', $period) }}" class="inline">
                            @csrf @method('PATCH')
                            <select name="status" onchange="this.form.submit()" class="text-[11px] border border-slate-200 rounded-lg px-1.5 py-0.5 text-slate-700 bg-white font-medium cursor-pointer shadow-2xs">
                                <option value="draft" {{ $period->status === 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="open" {{ $period->status === 'open' ? 'selected' : '' }}>Open</option>
                                <option value="closed" {{ $period->status === 'closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                        </form>
                    </div>
                </td>
                <td class="px-5 py-3.5">
                    <div class="flex items-center gap-2 justify-end">
                        <a href="{{ route('reports.index', ['group_id' => $period->group_id, 'period_id' => $period->id]) }}" class="inline-flex items-center gap-1 rounded-lg border border-emerald-200 bg-emerald-50/50 px-2.5 py-1 text-xs text-emerald-700 hover:bg-emerald-100 font-semibold transition-colors">
                            <i class="bi bi-file-earmark-text"></i> Laporan
                        </a>
                        <a href="{{ route('periods.edit', $period) }}" class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2.5 py-1 text-xs text-blue-600 hover:bg-blue-50 font-semibold transition-colors">
                            <i class="bi bi-pencil-square"></i> Edit
                        </a>
                        <form method="POST" action="{{ route('periods.destroy', $period) }}" data-confirm="true" data-confirm-title="Hapus Periode Kas?" data-confirm-text="Semua catatan setoran dan pengeluaran pada periode {{ $period->period_name }} ini juga akan ikut terhapus!">
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
                    <i class="bi bi-calendar-x text-3xl block mb-2 text-slate-300"></i>
                    Belum ada periode kas.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($periods->hasPages())
    <div class="border-t border-slate-100 px-5 py-3">{{ $periods->links() }}</div>
    @endif
</div>
@endsection
