@extends('layouts.app')
@section('title', 'Manajemen User & API Token')
@section('header', 'Manajemen User & API Token Otomasi')
@section('header-actions')
    <a href="{{ route('users.create') }}" class="inline-flex items-center gap-1.5 rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 shadow-xs transition-colors">
        <i class="bi bi-person-plus-fill"></i> Tambah User
    </a>
@endsection

@section('content')
<div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-50">
            <tr>
                <th class="text-left font-semibold text-slate-600 px-5 py-3.5 text-xs uppercase tracking-wider">User</th>
                <th class="text-left font-semibold text-slate-600 px-3 py-3.5 text-xs uppercase tracking-wider">Peran (Role)</th>
                <th class="text-center font-semibold text-slate-600 px-3 py-3.5 text-xs uppercase tracking-wider">Status</th>
                <th class="text-center font-semibold text-slate-600 px-3 py-3.5 text-xs uppercase tracking-wider">Status Token API</th>
                <th class="px-5 py-3.5"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($users as $user)
            <tr class="hover:bg-slate-50 transition-colors">
                <td class="px-5 py-3.5">
                    <p class="font-bold text-slate-800">{{ $user->name }}</p>
                    <p class="text-xs text-slate-400 flex items-center gap-1"><i class="bi bi-envelope text-[10px]"></i> {{ $user->email }}</p>
                </td>
                <td class="px-3 py-3.5">
                    <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-bold {{ $user->role === 'superadmin' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                        <i class="bi {{ $user->role === 'superadmin' ? 'bi-shield-shaded' : 'bi-person-badge' }}"></i>
                        {{ $user->role }}
                    </span>
                </td>
                <td class="px-3 py-3.5 text-center">
                    <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $user->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                        <i class="bi {{ $user->is_active ? 'bi-check-circle-fill' : 'bi-dash-circle-fill' }} text-[10px]"></i>
                        {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </td>
                <td class="px-3 py-3.5 text-center">
                    @if($user->tokens_count > 0)
                        <span class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2.5 py-1 rounded-xl">
                            <i class="bi bi-key-fill text-emerald-600"></i> {{ $user->tokens_count }} Token Aktif
                        </span>
                    @else
                        <span class="text-xs text-slate-400 font-medium">Belum ada token</span>
                    @endif
                </td>
                <td class="px-5 py-3.5">
                    <div class="flex items-center gap-2 justify-end">
                        {{-- Generate Token Button --}}
                        <form method="POST" action="{{ route('users.generate-token', $user) }}" data-confirm="true" data-confirm-title="Buat API Token Baru?" data-confirm-text="Generate token baru untuk {{ $user->name }}? Token lama yang sudah dibuat sebelumnya akan digantikan.">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-1 text-xs bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold px-2.5 py-1 rounded-lg transition-colors">
                                <i class="bi bi-lightning-charge-fill text-amber-500"></i> Buat Token
                            </button>
                        </form>
                        @if($user->tokens_count > 0)
                        <form method="POST" action="{{ route('users.revoke-tokens', $user) }}" data-confirm="true" data-confirm-title="Cabut Semua Token?" data-confirm-text="Apakah Anda yakin ingin mencabut semua API token milik {{ $user->name }}?">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-1 text-xs text-amber-700 hover:bg-amber-50 px-2 py-1 rounded-lg font-semibold transition-colors">
                                <i class="bi bi-x-circle"></i> Revoke
                            </button>
                        </form>
                        @endif
                        <a href="{{ route('users.edit', $user) }}" class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2.5 py-1 text-xs text-blue-600 hover:bg-blue-50 font-semibold transition-colors">
                            <i class="bi bi-pencil-square"></i> Edit
                        </a>
                        @if($user->id !== auth()->id())
                        <form method="POST" action="{{ route('users.destroy', $user) }}" data-confirm="true" data-confirm-title="Hapus User?" data-confirm-text="Apakah Anda yakin ingin menghapus akun {{ $user->name }}?">
                            @csrf @method('DELETE')
                            <button type="submit" class="inline-flex items-center gap-1 rounded-lg border border-red-200 bg-red-50/50 px-2.5 py-1 text-xs text-red-600 hover:bg-red-100 font-semibold transition-colors">
                                <i class="bi bi-trash3"></i> Hapus
                            </button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-5 py-8 text-center text-sm text-slate-400">
                    <i class="bi bi-people text-3xl block mb-1 text-slate-300"></i>
                    Belum ada user terdaftar.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
