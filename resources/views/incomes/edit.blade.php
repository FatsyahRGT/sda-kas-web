@extends('layouts.app')
@section('title', 'Edit Setoran Kas')
@section('header', 'Edit Setoran: ' . ($income->member->name ?? 'Anggota'))

@section('content')
<div class="max-w-lg">
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <form method="POST" action="{{ route('incomes.update', $income) }}" class="space-y-5">
            @csrf @method('PUT')

            <div>
                <label for="period_id" class="block text-sm font-medium text-slate-700 mb-1">Periode Kas <span class="text-red-500">*</span></label>
                <select name="period_id" id="period_id" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm @error('period_id') border-red-400 @enderror">
                    @foreach($periods as $p)
                        <option value="{{ $p->id }}" {{ old('period_id', $income->period_id) == $p->id ? 'selected' : '' }}>
                            {{ $p->period_name }} ({{ $p->group->name }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="member_id" class="block text-sm font-medium text-slate-700 mb-1">Nama Anggota <span class="text-red-500">*</span></label>
                <select name="member_id" id="member_id" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm @error('member_id') border-red-400 @enderror">
                    @foreach($members as $m)
                        <option value="{{ $m->id }}" {{ old('member_id', $income->member_id) == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="nominal" class="block text-sm font-medium text-slate-700 mb-1">Nominal Setoran (Rp) <span class="text-red-500">*</span></label>
                <input type="number" name="nominal" id="nominal" value="{{ old('nominal', $income->nominal) }}" min="1" step="500" required
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-800 @error('nominal') border-red-400 @enderror">
            </div>

            <div>
                <label for="transaction_date" class="block text-sm font-medium text-slate-700 mb-1">Tanggal & Waktu Bayar <span class="text-red-500">*</span></label>
                <input type="datetime-local" name="transaction_date" id="transaction_date" value="{{ old('transaction_date', $income->transaction_date->format('Y-m-d\TH:i')) }}" required
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm @error('transaction_date') border-red-400 @enderror">
            </div>

            <div>
                <label for="note" class="block text-sm font-medium text-slate-700 mb-1">Catatan / Keterangan</label>
                <input type="text" name="note" id="note" value="{{ old('note', $income->note) }}"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm @error('note') border-red-400 @enderror">
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="rounded-lg bg-blue-600 px-5 py-2 text-sm font-semibold text-white hover:bg-blue-700 transition-colors">Perbarui</button>
                <a href="{{ route('incomes.index', ['period_id' => $income->period_id]) }}" class="rounded-lg border border-slate-300 px-5 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
