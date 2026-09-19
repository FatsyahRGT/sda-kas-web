@extends('layouts.app')
@section('title', 'Periode Kas')
@section('header', 'Manajemen Periode Kas')
@section('header-actions')
    <a href="{{ route('periods.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 transition-colors">
        + Buka Periode Baru
    </a>
@endsection

@section('content')
{{-- Filter Group --}}
<form method="GET" class="flex gap-3 mb-5">
    <select name="group_id" onchange="this.form.submit()" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
        <option value="">Semua Grup</option>
        @foreach($groups as $g)
            <option value="{{ $g->id }}" {{ $groupId == $g->id ? 'selected' : '' }}>{{ $g->name }}</option>
        @endforeach
    </select>
</form>

<div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-50">
            <tr>
                <th class="text-left font-medium text-slate-500 px-5 py-3 text-xs uppercase tracking-wide">Periode</th>
                <th class="text-left font-medium text-slate-500 px-3 py-3 text-xs uppercase tracking-wide hidden sm:table-cell">Grup</th>
                <th class="text-right font-medium text-slate-500 px-3 py-3 text-xs uppercase tracking-wide">Iuran/Org</th>
                <th class="text-right font-medium text-slate-500 px-3 py-3 text-xs uppercase tracking-wide hidden md:table-cell">Saldo</th>
                <th class="text-center font-medium text-slate-500 px-3 py-3 text-xs uppercase tracking-wide">Status</th>
                <th class="px-5 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($periods as $period)
            <tr class="hover:bg-slate-50">
                <td class="px-5 py-3">
                    <p class="font-medium text-slate-800">{{ $period->period_name }}</p>
                    <p class="text-xs text-slate-400 sm:hidden">{{ $period->group->name }}</p>
                </td>
                <td class="px-3 py-3 text-slate-500 hidden sm:table-cell">{{ $period->group->name }}</td>
                <td class="px-3 py-3 text-right text-slate-600">
                    Rp {{ number_format($period->effective_due_amount, 0, ',', '.') }}
                    @if($period->due_amount !== null)
                        <span class="text-[10px] text-amber-600 bg-amber-50 px-1 py-0.5 rounded ml-1">override</span>
                    @endif
                </td>
                <td class="px-3 py-3 text-right hidden md:table-cell font-medium {{ $period->balance >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                    Rp {{ number_format($period->balance, 0, ',', '.') }}
                </td>
                <td class="px-3 py-3 text-center">
                    <div class="inline-flex items-center gap-1.5" x-data="{ openMenu: false }">
                        @php
                            $badgeClass = match($period->status) {
                                'closed' => 'bg-slate-100 text-slate-700 border-slate-300',
                                'open' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                                default => 'bg-amber-100 text-amber-800 border-amber-300',
                            };
                        @endphp
                        <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold uppercase {{ $badgeClass }}">
                            {{ $period->status }}
                        </span>
                        {{-- Quick status changer --}}
                        <form method="POST" action="{{ route('periods.update-status', $period) }}" class="inline">
                            @csrf @method('PATCH')
                            <select name="status" onchange="this.form.submit()" class="text-[11px] border border-slate-200 rounded px-1 py-0.5 text-slate-600 bg-white">
                                <option value="draft" {{ $period->status === 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="open" {{ $period->status === 'open' ? 'selected' : '' }}>Open</option>
                                <option value="closed" {{ $period->status === 'closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                        </form>
                    </div>
                </td>
                <td class="px-5 py-3">
                    <div class="flex items-center gap-2 justify-end">
                        <a href="{{ route('reports.index', ['group_id' => $period->group_id, 'period_id' => $period->id]) }}" class="text-xs text-emerald-600 hover:text-emerald-800 font-medium">Laporan</a>
                        <a href="{{ route('periods.edit', $period) }}" class="text-xs text-blue-600 hover:text-blue-800 font-medium">Edit</a>
                        <form method="POST" action="{{ route('periods.destroy', $period) }}" onsubmit="return confirm('Hapus periode ini? Semua data setoran dan pengeluaran terkait juga akan terhapus.')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-medium">Hapus</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-5 py-12 text-center text-sm text-slate-400">Belum ada periode kas.</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($periods->hasPages())
    <div class="border-t border-slate-100 px-5 py-3">{{ $periods->links() }}</div>
    @endif
</div>
@endsection
