@extends('layouts.app')
@section('title', 'Tambah User')
@section('header', 'Tambah User Baru')

@section('content')
<div class="max-w-lg">
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <form method="POST" action="{{ route('users.store') }}" class="space-y-5">
            @csrf
            <div>
                <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm @error('name') border-red-400 @enderror">
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Alamat Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm @error('email') border-red-400 @enderror">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Password <span class="text-red-500">*</span></label>
                <input type="password" name="password" id="password" required minlength="8"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm @error('password') border-red-400 @enderror">
            </div>

            <div>
                <label for="role" class="block text-sm font-medium text-slate-700 mb-1">Peran / Role <span class="text-red-500">*</span></label>
                <select name="role" id="role" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm @error('role') border-red-400 @enderror">
                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin (Akses Web Panel)</option>
                    <option value="superadmin" {{ old('role') === 'superadmin' ? 'selected' : '' }}>Superadmin (Akses Web + Akses Token Otomasi)</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="rounded border-slate-300">
                <label for="is_active" class="text-sm text-slate-700">Akun aktif</label>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="rounded-lg bg-blue-600 px-5 py-2 text-sm font-semibold text-white hover:bg-blue-700 transition-colors">Simpan User</button>
                <a href="{{ route('users.index') }}" class="rounded-lg border border-slate-300 px-5 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
