@extends('layouts.app')
@section('title', 'Tambah Anggota')
@section('header', 'Tambah Anggota Baru')

@section('content')
<div class="max-w-lg">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 sm:p-8">
        <form method="POST" action="{{ route('members.store') }}" class="space-y-5">
            @csrf
            <div>
                <label for="group_id" class="block text-xs font-semibold text-slate-700 mb-1.5">Grup Kas <span class="text-red-500">*</span></label>
                <select name="group_id" id="group_id" required class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-800 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 @error('group_id') border-red-400 @enderror">
                    <option value="">-- Pilih Grup Kas --</option>
                    @foreach($groups as $g)
                        <option value="{{ $g->id }}" {{ old('group_id') == $g->id ? 'selected' : '' }}>{{ $g->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="name" class="block text-xs font-semibold text-slate-700 mb-1.5">Nama Anggota <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required placeholder="Contoh: Bpk. Bambang"
                    class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 @error('name') border-red-400 @enderror">
            </div>
            <div>
                <label for="phone" class="block text-xs font-semibold text-slate-700 mb-1.5">No. HP / WhatsApp</label>
                <div class="relative rounded-xl shadow-2xs">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                        <i class="bi bi-telephone"></i>
                    </div>
                    <input type="tel" name="phone" id="phone" value="{{ old('phone') }}" placeholder="08xxxxxxxxxx"
                        class="w-full rounded-xl border border-slate-300 pl-10 pr-3.5 py-2.5 text-sm text-slate-800 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 @error('phone') border-red-400 @enderror">
                </div>
            </div>
            <div class="flex items-center gap-2.5 pt-1">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="rounded border-slate-300 text-blue-600 focus:ring-blue-500/20 h-4 w-4">
                <label for="is_active" class="text-xs sm:text-sm font-medium text-slate-700">Anggota aktif (wajib iuran kas)</label>
            </div>
            <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
                <button type="submit" class="inline-flex items-center gap-1.5 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 shadow-xs transition-colors">
                    <i class="bi bi-check-lg"></i> Simpan Anggota
                </button>
                <a href="{{ route('members.index') }}" class="rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
