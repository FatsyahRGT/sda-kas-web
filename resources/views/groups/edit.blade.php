@extends('layouts.app')
@section('title', 'Edit Grup')
@section('header', 'Edit Grup: ' . $group->name)

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <form method="POST" action="{{ route('groups.update', $group) }}" class="space-y-5">
            @csrf @method('PUT')
            <div>
                <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Nama Grup <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name', $group->name) }}" required
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm @error('name') border-red-400 @enderror">
            </div>
            <div>
                <label for="slug" class="block text-sm font-medium text-slate-700 mb-1">Slug URL <span class="text-red-500">*</span></label>
                <div class="flex items-center gap-2">
                    <span class="text-sm text-slate-400">/publik/</span>
                    <input type="text" name="slug" id="slug" value="{{ old('slug', $group->slug) }}" required
                        class="flex-1 rounded-lg border border-slate-300 px-3 py-2 text-sm @error('slug') border-red-400 @enderror">
                </div>
            </div>
            <div>
                <label for="description" class="block text-sm font-medium text-slate-700 mb-1">Deskripsi</label>
                <textarea name="description" id="description" rows="3"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">{{ old('description', $group->description) }}</textarea>
            </div>
            <div>
                <label for="default_due_amount" class="block text-sm font-medium text-slate-700 mb-1">Iuran Default per Bulan (Rp) <span class="text-red-500">*</span></label>
                <input type="number" name="default_due_amount" id="default_due_amount" value="{{ old('default_due_amount', $group->default_due_amount) }}" min="0" step="1000" required
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm @error('default_due_amount') border-red-400 @enderror">
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_public" id="is_public" value="1" {{ old('is_public', $group->is_public) ? 'checked' : '' }} class="rounded border-slate-300">
                <label for="is_public" class="text-sm text-slate-700">Tampilkan halaman publik</label>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="rounded-lg bg-blue-600 px-5 py-2 text-sm font-semibold text-white hover:bg-blue-700 transition-colors">Perbarui</button>
                <a href="{{ route('groups.index') }}" class="rounded-lg border border-slate-300 px-5 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
