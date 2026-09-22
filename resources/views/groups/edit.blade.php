@extends('layouts.app')
@section('title', 'Edit Grup')
@section('header', 'Edit Grup: ' . $group->name)

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 sm:p-8">
        <form method="POST" action="{{ route('groups.update', $group) }}" class="space-y-5">
            @csrf @method('PUT')
            <div>
                <label for="name" class="block text-xs font-semibold text-slate-700 mb-1.5">Nama Grup <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name', $group->name) }}" required
                    class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-800 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 @error('name') border-red-400 @enderror">
            </div>
            <div>
                <label for="slug" class="block text-xs font-semibold text-slate-700 mb-1.5">Slug URL Halaman Publik <span class="text-red-500">*</span></label>
                <div class="flex items-center gap-2">
                    <span class="text-xs text-slate-400 font-mono bg-slate-100 px-3 py-2.5 rounded-xl border border-slate-200">/publik/</span>
                    <input type="text" name="slug" id="slug" value="{{ old('slug', $group->slug) }}" required
                        class="flex-1 rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-800 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 @error('slug') border-red-400 @enderror">
                </div>
            </div>
            <div>
                <label for="description" class="block text-xs font-semibold text-slate-700 mb-1.5">Deskripsi</label>
                <textarea name="description" id="description" rows="3"
                    class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-800 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">{{ old('description', $group->description) }}</textarea>
            </div>
            <div>
                <label for="default_due_amount" class="block text-xs font-semibold text-slate-700 mb-1.5">Iuran Default per Bulan (Rp) <span class="text-red-500">*</span></label>
                <input type="number" name="default_due_amount" id="default_due_amount" value="{{ old('default_due_amount', $group->default_due_amount) }}" min="0" step="1000" required
                    class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm font-semibold text-slate-800 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 @error('default_due_amount') border-red-400 @enderror">
            </div>
            <div class="flex items-center gap-2.5 pt-1">
                <input type="checkbox" name="is_public" id="is_public" value="1" {{ old('is_public', $group->is_public) ? 'checked' : '' }} class="rounded border-slate-300 text-blue-600 focus:ring-blue-500/20 h-4 w-4">
                <label for="is_public" class="text-xs sm:text-sm font-medium text-slate-700">Tampilkan halaman publik</label>
            </div>
            <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
                <button type="submit" class="inline-flex items-center gap-1.5 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 shadow-xs transition-colors">
                    <i class="bi bi-check-lg"></i> Perbarui Grup
                </button>
                <a href="{{ route('groups.index') }}" class="rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
