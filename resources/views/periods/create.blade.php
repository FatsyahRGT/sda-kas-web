@extends('layouts.app')
@section('title', 'Buka Periode Kas')
@section('header', 'Buka Periode Kas Baru')

@section('content')
<div class="max-w-lg">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 sm:p-8">
        <form method="POST" action="{{ route('periods.store') }}" class="space-y-5">
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

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="month" class="block text-xs font-semibold text-slate-700 mb-1.5">Bulan <span class="text-red-500">*</span></label>
                    <select name="month" id="month" required class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-800 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 @error('month') border-red-400 @enderror">
                        @php
                            $months = [
                                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                            ];
                            $currMonth = (int) date('n');
                        @endphp
                        @foreach($months as $num => $name)
                            <option value="{{ $num }}" {{ old('month', $currMonth) == $num ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="year" class="block text-xs font-semibold text-slate-700 mb-1.5">Tahun <span class="text-red-500">*</span></label>
                    <input type="number" name="year" id="year" value="{{ old('year', date('Y')) }}" min="2000" max="2100" required
                        class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-800 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 @error('year') border-red-400 @enderror">
                </div>
            </div>

            <div>
                <label for="due_amount" class="block text-xs font-semibold text-slate-700 mb-1.5">Iuran Khusus Periode Ini (Rp)</label>
                <input type="number" name="due_amount" id="due_amount" value="{{ old('due_amount') }}" min="0" step="1000" placeholder="Kosongkan untuk memakai iuran default grup"
                    class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-800 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 @error('due_amount') border-red-400 @enderror">
                <p class="text-[11px] text-slate-400 mt-1">Kosongkan jika ingin otomatis mengikuti besaran iuran default grup kas.</p>
            </div>

            <div>
                <label for="status" class="block text-xs font-semibold text-slate-700 mb-1.5">Status Awal Periode <span class="text-red-500">*</span></label>
                <select name="status" id="status" required class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-800 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 @error('status') border-red-400 @enderror">
                    <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft (Persiapan / Belum Dibuka)</option>
                    <option value="open" {{ old('status', 'open') === 'open' ? 'selected' : '' }}>Open (Berjalan / Menerima Setoran)</option>
                    <option value="closed" {{ old('status') === 'closed' ? 'selected' : '' }}>Closed (Tutup Buku / Terbit di Publik)</option>
                </select>
            </div>

            <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
                <button type="submit" class="inline-flex items-center gap-1.5 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 shadow-xs transition-colors">
                    <i class="bi bi-calendar-check"></i> Simpan Periode
                </button>
                <a href="{{ route('periods.index') }}" class="rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
