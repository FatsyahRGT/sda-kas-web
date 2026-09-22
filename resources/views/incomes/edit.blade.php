@extends('layouts.app')
@section('title', 'Edit Setoran Kas')
@section('header', 'Edit Setoran: ' . ($income->member->name ?? 'Anggota'))

@section('content')
<div class="max-w-lg">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 sm:p-8">
        <form method="POST" action="{{ route('incomes.update', $income) }}" class="space-y-5">
            @csrf @method('PUT')

            <div>
                <label for="period_id" class="block text-xs font-semibold text-slate-700 mb-1.5">Periode Kas <span class="text-red-500">*</span></label>
                <select name="period_id" id="period_id" required class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-800 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 @error('period_id') border-red-400 @enderror">
                    @foreach($periods as $p)
                        <option value="{{ $p->id }}" {{ old('period_id', $income->period_id) == $p->id ? 'selected' : '' }}>
                            {{ $p->period_name }} ({{ $p->group->name }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="member_id" class="block text-xs font-semibold text-slate-700 mb-1.5">Nama Anggota <span class="text-red-500">*</span></label>
                <select name="member_id" id="member_id" required class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-800 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 @error('member_id') border-red-400 @enderror">
                    @foreach($members as $m)
                        <option value="{{ $m->id }}" {{ old('member_id', $income->member_id) == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="nominal" class="block text-xs font-semibold text-slate-700 mb-1.5">Nominal Setoran (Rp) <span class="text-red-500">*</span></label>
                <input type="number" name="nominal" id="nominal" value="{{ old('nominal', $income->nominal) }}" min="1" step="500" required
                    class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm font-bold text-slate-800 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 @error('nominal') border-red-400 @enderror">
            </div>

            <div>
                <label for="transaction_date" class="block text-xs font-semibold text-slate-700 mb-1.5">Tanggal & Waktu Bayar <span class="text-red-500">*</span></label>
                <input type="datetime-local" name="transaction_date" id="transaction_date" value="{{ old('transaction_date', $income->transaction_date->format('Y-m-d\TH:i')) }}" required
                    class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-800 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 @error('transaction_date') border-red-400 @enderror">
            </div>

            <div>
                <label for="note" class="block text-xs font-semibold text-slate-700 mb-1.5">Catatan / Keterangan</label>
                <input type="text" name="note" id="note" value="{{ old('note', $income->note) }}"
                    class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-800 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 @error('note') border-red-400 @enderror">
            </div>

            <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
                <button type="submit" class="inline-flex items-center gap-1.5 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 shadow-xs transition-colors">
                    <i class="bi bi-check-lg"></i> Perbarui Setoran
                </button>
                <a href="{{ route('incomes.index', ['period_id' => $income->period_id]) }}" class="rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
