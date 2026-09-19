@extends('layouts.app')
@section('title', 'Kategori Pengeluaran')
@section('header', 'Kategori Pengeluaran')

@section('content')
<div class="grid md:grid-cols-3 gap-6">
    {{-- Form Tambah --}}
    <div class="md:col-span-1">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
            <h2 class="text-sm font-semibold text-slate-700 mb-4">+ Tambah Kategori</h2>
            <form method="POST" action="{{ route('expense-categories.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="name" class="block text-xs font-medium text-slate-600 mb-1">Nama Kategori <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" required placeholder="Contoh: Konsumsi, Kebersihan, Peralatan"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm @error('name') border-red-400 @enderror">
                </div>
                <button type="submit" class="w-full rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 transition-colors">
                    Simpan Kategori
                </button>
            </form>
        </div>
    </div>

    {{-- List Table --}}
    <div class="md:col-span-2">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden" x-data="{ editingId: null, editingName: '' }">
            <table class="w-full text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="text-left font-medium text-slate-500 px-5 py-3 text-xs uppercase tracking-wide">Nama Kategori</th>
                        <th class="text-center font-medium text-slate-500 px-3 py-3 text-xs uppercase tracking-wide">Dipakai</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($categories as $cat)
                    <tr class="hover:bg-slate-50">
                        <td class="px-5 py-3">
                            {{-- View Mode --}}
                            <div x-show="editingId !== {{ $cat->id }}">
                                <span class="font-medium text-slate-800">{{ $cat->name }}</span>
                            </div>
                            {{-- Inline Edit Mode --}}
                            <div x-show="editingId === {{ $cat->id }}" style="display: none;">
                                <form method="POST" action="{{ route('expense-categories.update', $cat) }}" class="flex gap-2">
                                    @csrf @method('PUT')
                                    <input type="text" name="name" x-model="editingName" required class="rounded border border-slate-300 px-2 py-1 text-xs">
                                    <button type="submit" class="rounded bg-emerald-600 px-2.5 py-1 text-xs text-white font-medium">OK</button>
                                    <button type="button" @click="editingId = null" class="rounded border border-slate-300 px-2 py-1 text-xs text-slate-600">Batal</button>
                                </form>
                            </div>
                        </td>
                        <td class="px-3 py-3 text-center text-xs text-slate-500">
                            {{ $cat->expenses_count }}x transaksi
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2 justify-end">
                                <button type="button" @click="editingId = {{ $cat->id }}; editingName = '{{ addslashes($cat->name) }}'"
                                    class="text-xs text-blue-600 hover:text-blue-800 font-medium">Edit</button>
                                <form method="POST" action="{{ route('expense-categories.destroy', $cat) }}" onsubmit="return confirm('Hapus kategori ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-medium">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="px-5 py-8 text-center text-sm text-slate-400">Belum ada kategori pengeluaran.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
