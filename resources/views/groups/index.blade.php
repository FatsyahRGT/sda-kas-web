@extends('layouts.app')
@section('title', 'Grup Kas')
@section('header', 'Manajemen Grup Kas')
@section('header-actions')
    <a href="{{ route('groups.create') }}" class="inline-flex items-center gap-1.5 rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 shadow-xs transition-colors">
        <i class="bi bi-plus-lg"></i> Tambah Grup
    </a>
@endsection

@section('content')
<div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-50">
            <tr>
                <th class="text-left font-semibold text-slate-600 px-5 py-3.5 text-xs uppercase tracking-wider">Nama Grup</th>
                <th class="text-left font-semibold text-slate-600 px-3 py-3.5 text-xs uppercase tracking-wider hidden sm:table-cell">Slug</th>
                <th class="text-right font-semibold text-slate-600 px-3 py-3.5 text-xs uppercase tracking-wider hidden md:table-cell">Iuran Default</th>
                <th class="text-center font-semibold text-slate-600 px-3 py-3.5 text-xs uppercase tracking-wider">Status Publik</th>
                <th class="text-center font-semibold text-slate-600 px-3 py-3.5 text-xs uppercase tracking-wider hidden sm:table-cell">Anggota</th>
                <th class="px-5 py-3.5"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($groups as $group)
            <tr class="hover:bg-slate-50 transition-colors">
                <td class="px-5 py-3.5">
                    <p class="font-bold text-slate-800">{{ $group->name }}</p>
                    @if($group->description)
                        <p class="text-xs text-slate-400 truncate max-w-xs mt-0.5">{{ $group->description }}</p>
                    @endif
                </td>
                <td class="px-3 py-3.5 text-slate-500 font-mono text-xs hidden sm:table-cell">{{ $group->slug }}</td>
                <td class="px-3 py-3.5 text-right font-semibold text-slate-700 hidden md:table-cell">Rp {{ number_format($group->default_due_amount, 0, ',', '.') }}</td>
                <td class="px-3 py-3.5 text-center">
                    <form method="POST" action="{{ route('groups.toggle-public', $group) }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-semibold {{ $group->is_public ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }} hover:opacity-80 transition-opacity">
                            <i class="bi {{ $group->is_public ? 'bi-globe2' : 'bi-lock-fill' }}"></i>
                            <span>{{ $group->is_public ? 'Publik' : 'Privat' }}</span>
                        </button>
                    </form>
                </td>
                <td class="px-3 py-3.5 text-center text-slate-600 font-semibold hidden sm:table-cell">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-slate-100 text-xs">
                        <i class="bi bi-people"></i> {{ $group->members_count }}
                    </span>
                </td>
                <td class="px-5 py-3.5">
                    <div class="flex items-center gap-2 justify-end">
                        @if($group->is_public)
                        <a href="{{ route('public.group', $group->slug) }}" target="_blank" title="Lihat Halaman Publik" class="h-8 w-8 rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 flex items-center justify-center text-sm transition-colors">
                            <i class="bi bi-box-arrow-up-right"></i>
                        </a>
                        @endif
                        <a href="{{ route('groups.edit', $group) }}" class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2.5 py-1 text-xs text-blue-600 hover:bg-blue-50 font-semibold transition-colors">
                            <i class="bi bi-pencil-square"></i> Edit
                        </a>
                        <form method="POST" action="{{ route('groups.destroy', $group) }}" data-confirm="true" data-confirm-title="Hapus Grup Kas?" data-confirm-text="Semua data terkait (anggota, periode, dan transaksi) pada grup ini akan ikut terhapus permanen!">
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
                    <i class="bi bi-folder2-open text-3xl block mb-2 text-slate-300"></i>
                    Belum ada grup kas. <a href="{{ route('groups.create') }}" class="text-blue-600 font-semibold hover:underline">Tambah sekarang</a>.
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
