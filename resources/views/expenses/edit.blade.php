@extends('layouts.app')
@section('title', 'Edit Pengeluaran')
@section('header', 'Edit Pengeluaran: ' . $expense->item_name)

@section('content')
<div class="max-w-lg">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 sm:p-8">
        <form method="POST" action="{{ route('expenses.update', $expense) }}" enctype="multipart/form-data" class="space-y-5">
            @csrf @method('PUT')

            <div>
                <label for="period_id" class="block text-xs font-semibold text-slate-700 mb-1.5">Periode Kas <span class="text-red-500">*</span></label>
                <select name="period_id" id="period_id" required class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-800 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 @error('period_id') border-red-400 @enderror">
                    @foreach($periods as $p)
                        <option value="{{ $p->id }}" {{ old('period_id', $expense->period_id) == $p->id ? 'selected' : '' }}>
                            {{ $p->period_name }} ({{ $p->group->name }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="category_id" class="block text-xs font-semibold text-slate-700 mb-1.5">Kategori Pengeluaran</label>
                <select name="category_id" id="category_id" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-800 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 @error('category_id') border-red-400 @enderror">
                    <option value="">-- Tanpa Kategori / Umum --</option>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}" {{ old('category_id', $expense->category_id) == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="item_name" class="block text-xs font-semibold text-slate-700 mb-1.5">Nama Barang / Keperluan <span class="text-red-500">*</span></label>
                <input type="text" name="item_name" id="item_name" value="{{ old('item_name', $expense->item_name) }}" required
                    class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-800 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 @error('item_name') border-red-400 @enderror">
            </div>

            <div>
                <label for="nominal" class="block text-xs font-semibold text-slate-700 mb-1.5">Nominal Biaya (Rp) <span class="text-red-500">*</span></label>
                <input type="number" name="nominal" id="nominal" value="{{ old('nominal', $expense->nominal) }}" min="1" step="500" required
                    class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm font-bold text-slate-800 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 @error('nominal') border-red-400 @enderror">
            </div>

            <div>
                <label for="transaction_date" class="block text-xs font-semibold text-slate-700 mb-1.5">Tanggal Transaksi <span class="text-red-500">*</span></label>
                <input type="datetime-local" name="transaction_date" id="transaction_date" value="{{ old('transaction_date', $expense->transaction_date->format('Y-m-d\TH:i')) }}" required
                    class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-800 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 @error('transaction_date') border-red-400 @enderror">
            </div>

            <div>
                <label for="note" class="block text-xs font-semibold text-slate-700 mb-1.5">Catatan Tambahan</label>
                <input type="text" name="note" id="note" value="{{ old('note', $expense->note) }}"
                    class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-800 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 @error('note') border-red-400 @enderror">
            </div>

            @if($expense->attachments->isNotEmpty())
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-2">Foto / Bukti Terlampir Saat Ini</label>
                <div class="grid grid-cols-3 gap-2 mb-2">
                    @foreach($expense->attachments as $att)
                    <div class="relative rounded-lg overflow-hidden border border-slate-200 aspect-square">
                        <img src="{{ asset('storage/' . $att->file_path) }}" alt="Bukti" class="h-full w-full object-cover">
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <div>
                <label for="attachments" class="block text-xs font-semibold text-slate-700 mb-1.5">Tambah Foto / Bukti Baru</label>
                <input type="file" name="attachments[]" id="attachments" multiple accept="image/*,.pdf"
                    class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-xs text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-blue-50 file:px-3 file:py-1 file:text-xs file:font-semibold file:text-blue-700 hover:file:bg-blue-100">
            </div>

            <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
                <button type="submit" class="inline-flex items-center gap-1.5 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 shadow-xs transition-colors">
                    <i class="bi bi-check-lg"></i> Perbarui Pengeluaran
                </button>
                <a href="{{ route('expenses.index', ['period_id' => $expense->period_id]) }}" class="rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
