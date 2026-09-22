@extends('layouts.app')
@section('title', 'Kategori Pengeluaran')
@section('header', 'Kategori Pengeluaran')

@section('content')
<div class="grid md:grid-cols-3 gap-6">
    {{-- Form Tambah --}}
    <div class="md:col-span-1">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-5">
            <h2 class="text-sm font-bold text-slate-800 mb-4 flex items-center gap-2">
                <i class="bi bi-tag-fill text-blue-600"></i> Tambah Kategori
            </h2>
            <form method="POST" action="{{ route('expense-categories.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="name" class="block text-xs font-semibold text-slate-700 mb-1.5">Nama Kategori <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" required placeholder="Contoh: Konsumsi, Kebersihan, Listrik"
                        class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm text-slate-800 placeholder-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 @error('name') border-red-400 @enderror">
                </div>
                <button type="submit" class="w-full flex items-center justify-center gap-1.5 rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 shadow-xs transition-colors">
                    <i class="bi bi-plus-lg"></i> Simpan Kategori
                </button>
            </form>
        </div>
    </div>

    {{-- List Table --}}
    <div class="md:col-span-2">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden" x-data="{ editingId: null, editingName: '' }">
            <table class="w-full text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="text-left font-semibold text-slate-600 px-5 py-3.5 text-xs uppercase tracking-wider">Nama Kategori</th>
                        <th class="text-center font-semibold text-slate-600 px-3 py-3.5 text-xs uppercase tracking-wider">Frekuensi Dipakai</th>
                        <th class="px-5 py-3.5"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($categories as $cat)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-5 py-3.5">
                            {{-- View Mode --}}
                            <div x-show="editingId !== {{ $cat->id }}" class="flex items-center gap-2">
                                <i class="bi bi-tag text-slate-400"></i>
                                <span class="font-bold text-slate-800">{{ $cat->name }}</span>
                            </div>
                            {{-- Inline Edit Mode --}}
                            <div x-show="editingId === {{ $cat->id }}" style="display: none;">
                                <form method="POST" action="{{ route('expense-categories.update', $cat) }}" class="flex items-center gap-2">
                                    @csrf @method('PUT')
                                    <input type="text" name="name" x-model="editingName" required class="rounded-lg border border-slate-300 px-2.5 py-1 text-xs text-slate-800 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                    <button type="submit" class="rounded-lg bg-emerald-600 px-2.5 py-1 text-xs text-white font-semibold hover:bg-emerald-700"><i class="bi bi-check-lg"></i> OK</button>
                                    <button type="button" @click="editingId = null" class="rounded-lg border border-slate-300 px-2 py-1 text-xs text-slate-600 hover:bg-slate-100">Batal</button>
                                </form>
                            </div>
                        </td>
                        <td class="px-3 py-3.5 text-center text-xs text-slate-500 font-medium">
                            <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-0.5 font-semibold text-slate-700">
                                {{ $cat->expenses_count }}x transaksi
                            </span>
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-2 justify-end">
                                <button type="button" @click="editingId = {{ $cat->id }}; editingName = '{{ addslashes($cat->name) }}'"
                                    class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2.5 py-1 text-xs text-blue-600 hover:bg-blue-50 font-semibold transition-colors">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </button>
                                <form method="POST" action="{{ route('expense-categories.destroy', $cat) }}" data-confirm="true" data-confirm-title="Hapus Kategori?" data-confirm-text="Apakah Anda yakin ingin menghapus kategori '{{ $cat->name }}'?">
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
                        <td colspan="3" class="px-5 py-8 text-center text-sm text-slate-400">
                            <i class="bi bi-tags text-2xl block mb-1 text-slate-300"></i>
                            Belum ada kategori pengeluaran.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
