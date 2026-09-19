@extends('layouts.app')
@section('title', 'Manajemen User & API Token')
@section('header', 'Manajemen User & API Token Otomasi')
@section('header-actions')
    <a href="{{ route('users.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 transition-colors">
        + Tambah User
    </a>
@endsection

@section('content')
<div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-50">
            <tr>
                <th class="text-left font-medium text-slate-500 px-5 py-3 text-xs uppercase tracking-wide">User</th>
                <th class="text-left font-medium text-slate-500 px-3 py-3 text-xs uppercase tracking-wide">Role</th>
                <th class="text-center font-medium text-slate-500 px-3 py-3 text-xs uppercase tracking-wide">Status</th>
                <th class="text-center font-medium text-slate-500 px-3 py-3 text-xs uppercase tracking-wide">API Token</th>
                <th class="px-5 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($users as $user)
            <tr class="hover:bg-slate-50">
                <td class="px-5 py-3">
                    <p class="font-medium text-slate-800">{{ $user->name }}</p>
                    <p class="text-xs text-slate-400">{{ $user->email }}</p>
                </td>
                <td class="px-3 py-3">
                    <span class="inline-flex rounded px-2 py-0.5 text-xs font-semibold {{ $user->role === 'superadmin' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                        {{ $user->role }}
                    </span>
                </td>
                <td class="px-3 py-3 text-center">
                    <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold {{ $user->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                        {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </td>
                <td class="px-3 py-3 text-center">
                    @if($user->tokens_count > 0)
                        <span class="inline-flex items-center gap-1 text-xs text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded">
                            🔑 {{ $user->tokens_count }} Token Aktif
                        </span>
                    @else
                        <span class="text-xs text-slate-400">Belum ada token</span>
                    @endif
                </td>
                <td class="px-5 py-3">
                    <div class="flex items-center gap-2 justify-end">
                        {{-- Generate Token Button --}}
                        <form method="POST" action="{{ route('users.generate-token', $user) }}" onsubmit="return confirm('Generate API token baru untuk {{ $user->name }}? Token lama akan digantikan.')">
                            @csrf
                            <button type="submit" class="text-xs bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium px-2 py-1 rounded">
                                ⚡ Buat Token
                            </button>
                        </form>
                        @if($user->tokens_count > 0)
                        <form method="POST" action="{{ route('users.revoke-tokens', $user) }}" onsubmit="return confirm('Cabut semua token untuk {{ $user->name }}?')">
                            @csrf
                            <button type="submit" class="text-xs text-amber-600 hover:text-amber-800 font-medium">Revoke</button>
                        </form>
                        @endif
                        <a href="{{ route('users.edit', $user) }}" class="text-xs text-blue-600 hover:text-blue-800 font-medium">Edit</a>
                        @if($user->id !== auth()->id())
                        <form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('Hapus user ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-medium">Hapus</button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-5 py-8 text-center text-sm text-slate-400">Belum ada user.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
