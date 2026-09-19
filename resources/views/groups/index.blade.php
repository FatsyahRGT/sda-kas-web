@extends('layouts.app')
@section('title', 'Grup Kas')
@section('header', 'Manajemen Grup Kas')
@section('header-actions')
    <a href="{{ route('groups.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 transition-colors">
        + Tambah Grup
    </a>
@endsection

@section('content')
<div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-50">
            <tr>
                <th class="text-left font-medium text-slate-500 px-5 py-3 text-xs uppercase tracking-wide">Nama Grup</th>
                <th class="text-left font-medium text-slate-500 px-3 py-3 text-xs uppercase tracking-wide hidden sm:table-cell">Slug</th>
                <th class="text-right font-medium text-slate-500 px-3 py-3 text-xs uppercase tracking-wide hidden md:table-cell">Iuran Default</th>
                <th class="text-center font-medium text-slate-500 px-3 py-3 text-xs uppercase tracking-wide">Publik</th>
                <th class="text-center font-medium text-slate-500 px-3 py-3 text-xs uppercase tracking-wide hidden sm:table-cell">Anggota</th>
                <th class="px-5 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($groups as $group)
            <tr class="hover:bg-slate-50">
                <td class="px-5 py-3">
                    <p class="font-medium text-slate-800">{{ $group->name }}</p>
                    @if($group->description)
                        <p class="text-xs text-slate-400 truncate max-w-xs">{{ $group->description }}</p>
                    @endif
                </td>
                <td class="px-3 py-3 text-slate-500 font-mono text-xs hidden sm:table-cell">{{ $group->slug }}</td>
                <td class="px-3 py-3 text-right text-slate-600 hidden md:table-cell">Rp {{ number_format($group->default_due_amount, 0, ',', '.') }}</td>
                <td class="px-3 py-3 text-center">
                    <form method="POST" action="{{ route('groups.toggle-public', $group) }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $group->is_public ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }} hover:opacity-80 transition-opacity">
                            {{ $group->is_public ? '✓ Publik' : '🔒 Privat' }}
                        </button>
                    </form>
                </td>
                <td class="px-3 py-3 text-center text-slate-600 hidden sm:table-cell">{{ $group->members_count }}</td>
                <td class="px-5 py-3">
                    <div class="flex items-center gap-2 justify-end">
                        @if($group->is_public)
                        <a href="{{ route('public.group', $group->slug) }}" target="_blank" class="text-xs text-slate-400 hover:text-slate-600">🔗</a>
                        @endif
                        <a href="{{ route('groups.edit', $group) }}" class="text-xs text-blue-600 hover:text-blue-800 font-medium">Edit</a>
                        <form method="POST" action="{{ route('groups.destroy', $group) }}" onsubmit="return confirm('Hapus grup ini? Semua data terkait (anggota, periode, transaksi) juga akan dihapus.')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-medium">Hapus</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-5 py-12 text-center text-sm text-slate-400">
                    Belum ada grup kas. <a href="{{ route('groups.create') }}" class="text-blue-600 hover:underline">Tambah sekarang</a>.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($groups->hasPages())
    <div class="border-t border-slate-100 px-5 py-3">{{ $groups->links() }}</div>
    @endif
</div>
@endsection
