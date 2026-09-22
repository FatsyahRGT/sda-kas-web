@extends('layouts.app')
@section('title', 'Tambah User')
@section('header', 'Tambah User Baru')

@section('content')
<div class="max-w-lg">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 sm:p-8">
        <form method="POST" action="{{ route('users.store') }}" class="space-y-5">
            @csrf
            <div>
                <label for="name" class="block text-xs font-semibold text-slate-700 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required placeholder="Contoh: Ahmad Fadilah"
                    class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-800 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 @error('name') border-red-400 @enderror">
            </div>

            <div>
                <label for="email" class="block text-xs font-semibold text-slate-700 mb-1.5">Alamat Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required placeholder="user@kas.test"
                    class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-800 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 @error('email') border-red-400 @enderror">
            </div>

            <div>
                <label for="password" class="block text-xs font-semibold text-slate-700 mb-1.5">Password <span class="text-red-500">*</span></label>
                <input type="password" name="password" id="password" required placeholder="Minimal 8 karakter"
                    class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-800 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 @error('password') border-red-400 @enderror">
            </div>

            <div>
                <label for="role" class="block text-xs font-semibold text-slate-700 mb-1.5">Peran / Role Akses <span class="text-red-500">*</span></label>
                <select name="role" id="role" required class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-800 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 @error('role') border-red-400 @enderror">
                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin (Pengurus Kas Web)</option>
                    <option value="superadmin" {{ old('role') === 'superadmin' ? 'selected' : '' }}>Superadmin (Akses API Otomasi & Manajemen User)</option>
                </select>
            </div>

            <div class="flex items-center gap-2.5 pt-1">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="rounded border-slate-300 text-blue-600 focus:ring-blue-500/20 h-4 w-4">
                <label for="is_active" class="text-xs sm:text-sm font-medium text-slate-700">Akun aktif</label>
            </div>

            <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
                <button type="submit" class="inline-flex items-center gap-1.5 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 shadow-xs transition-colors">
                    <i class="bi bi-person-check-fill"></i> Simpan User
                </button>
                <a href="{{ route('users.index') }}" class="rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
