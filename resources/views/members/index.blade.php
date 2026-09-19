@extends('layouts.app')
@section('title', 'Anggota')
@section('header', 'Manajemen Anggota')
@section('header-actions')
    <a href="{{ route('members.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 transition-colors">
        + Tambah Anggota
    </a>
@endsection

@section('content')
{{-- Filter --}}
<form method="GET" class="flex flex-wrap gap-3 mb-5">
    <select name="group_id" onchange="this.form.submit()" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
        <option value="">Semua Grup</option>
        @foreach($groups as $g)
            <option value="{{ $g->id }}" {{ $groupId == $g->id ? 'selected' : '' }}>{{ $g->name }}</option>
        @endforeach
    </select>
    <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama / no. HP..."
        class="flex-1 min-w-[180px] rounded-lg border border-slate-300 px-3 py-2 text-sm">
    <button type="submit" class="rounded-lg bg-slate-700 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">Cari</button>
    @if($search || $groupId)
        <a href="{{ route('members.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50">Reset</a>
    @endif
</form>

<div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-50">
            <tr>
                <th class="text-left font-medium text-slate-500 px-5 py-3 text-xs uppercase tracking-wide">Nama</th>
                <th class="text-left font-medium text-slate-500 px-3 py-3 text-xs uppercase tracking-wide hidden sm:table-cell">Grup</th>
                <th class="text-left font-medium text-slate-500 px-3 py-3 text-xs uppercase tracking-wide hidden md:table-cell">No. HP</th>
                <th class="text-center font-medium text-slate-500 px-3 py-3 text-xs uppercase tracking-wide">Status</th>
                <th class="px-5 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($members as $member)
            <tr class="hover:bg-slate-50">
                <td class="px-5 py-3 font-medium text-slate-800">{{ $member->name }}</td>
                <td class="px-3 py-3 text-slate-500 hidden sm:table-cell">{{ $member->group->name }}</td>
                <td class="px-3 py-3 text-slate-500 hidden md:table-cell">{{ $member->phone ?? '—' }}</td>
                <td class="px-3 py-3 text-center">
                    <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold {{ $member->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                        {{ $member->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </td>
                <td class="px-5 py-3">
                    <div class="flex items-center gap-2 justify-end">
                        <a href="{{ route('members.edit', $member) }}" class="text-xs text-blue-600 hover:text-blue-800 font-medium">Edit</a>
                        <form method="POST" action="{{ route('members.destroy', $member) }}" onsubmit="return confirm('Hapus anggota ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-medium">Hapus</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-5 py-12 text-center text-sm text-slate-400">Belum ada anggota.</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($members->hasPages())
    <div class="border-t border-slate-100 px-5 py-3">{{ $members->links() }}</div>
    @endif
</div>
@endsection
