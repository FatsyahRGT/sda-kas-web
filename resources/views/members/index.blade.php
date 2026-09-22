@extends('layouts.app')
@section('title', 'Anggota')
@section('header', 'Manajemen Anggota')
@section('header-actions')
    <a href="{{ route('members.create') }}" class="inline-flex items-center gap-1.5 rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 shadow-xs transition-colors">
        <i class="bi bi-person-plus-fill"></i> Tambah Anggota
    </a>
@endsection

@section('content')
{{-- Filter --}}
<form method="GET" class="flex flex-wrap gap-2.5 mb-5">
    <select name="group_id" onchange="this.form.submit()" class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 shadow-xs">
        <option value="">Semua Grup</option>
        @foreach($groups as $g)
            <option value="{{ $g->id }}" {{ $groupId == $g->id ? 'selected' : '' }}>{{ $g->name }}</option>
        @endforeach
    </select>
    <div class="relative flex-1 min-w-[200px]">
        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
            <i class="bi bi-search"></i>
        </span>
        <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama / no. HP..."
            class="w-full rounded-xl border border-slate-300 bg-white pl-9 pr-3 py-2 text-sm text-slate-800 placeholder-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 shadow-xs">
    </div>
    <button type="submit" class="rounded-xl bg-slate-800 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-900 transition-colors shadow-xs">
        Cari
    </button>
    @if($search || $groupId)
        <a href="{{ route('members.index') }}" class="inline-flex items-center gap-1 rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors shadow-xs">
            <i class="bi bi-arrow-counterclockwise"></i> Reset
        </a>
    @endif
</form>

<div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-50">
            <tr>
                <th class="text-left font-semibold text-slate-600 px-5 py-3.5 text-xs uppercase tracking-wider">Nama Anggota</th>
                <th class="text-left font-semibold text-slate-600 px-3 py-3.5 text-xs uppercase tracking-wider hidden sm:table-cell">Grup Kas</th>
                <th class="text-left font-semibold text-slate-600 px-3 py-3.5 text-xs uppercase tracking-wider hidden md:table-cell">No. HP / WA</th>
                <th class="text-center font-semibold text-slate-600 px-3 py-3.5 text-xs uppercase tracking-wider">Status</th>
                <th class="px-5 py-3.5"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($members as $member)
            <tr class="hover:bg-slate-50 transition-colors">
                <td class="px-5 py-3.5 font-bold text-slate-800 flex items-center gap-2.5">
                    <div class="h-8 w-8 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-xs">
                        {{ strtoupper(substr($member->name, 0, 1)) }}
                    </div>
                    <span>{{ $member->name }}</span>
                </td>
                <td class="px-3 py-3.5 text-slate-600 hidden sm:table-cell">{{ $member->group->name }}</td>
                <td class="px-3 py-3.5 text-slate-500 hidden md:table-cell font-mono text-xs">
                    @if($member->phone)
                        <span class="flex items-center gap-1"><i class="bi bi-whatsapp text-emerald-600"></i> {{ $member->phone }}</span>
                    @else
                        <span class="text-slate-300">—</span>
                    @endif
                </td>
                <td class="px-3 py-3.5 text-center">
                    <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $member->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                        <i class="bi {{ $member->is_active ? 'bi-check-circle-fill' : 'bi-dash-circle-fill' }} text-[10px]"></i>
                        {{ $member->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </td>
                <td class="px-5 py-3.5">
                    <div class="flex items-center gap-2 justify-end">
                        <a href="{{ route('members.edit', $member) }}" class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2.5 py-1 text-xs text-blue-600 hover:bg-blue-50 font-semibold transition-colors">
                            <i class="bi bi-pencil-square"></i> Edit
                        </a>
                        <form method="POST" action="{{ route('members.destroy', $member) }}" data-confirm="true" data-confirm-title="Hapus Anggota?" data-confirm-text="Apakah Anda yakin ingin menghapus data anggota {{ $member->name }}?">
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
                <td colspan="5" class="px-5 py-12 text-center text-sm text-slate-400">
                    <i class="bi bi-people text-3xl block mb-2 text-slate-300"></i>
                    Belum ada data anggota.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($members->hasPages())
    <div class="border-t border-slate-100 px-5 py-3">{{ $members->links() }}</div>
    @endif
</div>
@endsection
