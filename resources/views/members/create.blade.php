@extends('layouts.app')
@section('title', 'Tambah Anggota')
@section('header', 'Tambah Anggota Baru')

@section('content')
<div class="max-w-lg">
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <form method="POST" action="{{ route('members.store') }}" class="space-y-5">
            @csrf
            <div>
                <label for="group_id" class="block text-sm font-medium text-slate-700 mb-1">Grup Kas <span class="text-red-500">*</span></label>
                <select name="group_id" id="group_id" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm @error('group_id') border-red-400 @enderror">
                    <option value="">-- Pilih Grup --</option>
                    @foreach($groups as $g)
                        <option value="{{ $g->id }}" {{ old('group_id') == $g->id ? 'selected' : '' }}>{{ $g->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Nama Anggota <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm @error('name') border-red-400 @enderror">
            </div>
            <div>
                <label for="phone" class="block text-sm font-medium text-slate-700 mb-1">No. HP / WhatsApp</label>
                <input type="tel" name="phone" id="phone" value="{{ old('phone') }}"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm @error('phone') border-red-400 @enderror">
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="rounded border-slate-300">
                <label for="is_active" class="text-sm text-slate-700">Anggota aktif</label>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="rounded-lg bg-blue-600 px-5 py-2 text-sm font-semibold text-white hover:bg-blue-700 transition-colors">Simpan</button>
                <a href="{{ route('members.index') }}" class="rounded-lg border border-slate-300 px-5 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
