@extends('layouts.app')
@section('title', 'Edit Periode Kas')
@section('header', 'Edit Periode: ' . $period->period_name)

@section('content')
<div class="max-w-lg">
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <form method="POST" action="{{ route('periods.update', $period) }}" class="space-y-5">
            @csrf @method('PUT')
            <div>
                <label for="group_id" class="block text-sm font-medium text-slate-700 mb-1">Grup Kas <span class="text-red-500">*</span></label>
                <select name="group_id" id="group_id" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm @error('group_id') border-red-400 @enderror">
                    @foreach($groups as $g)
                        <option value="{{ $g->id }}" {{ old('group_id', $period->group_id) == $g->id ? 'selected' : '' }}>{{ $g->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="month" class="block text-sm font-medium text-slate-700 mb-1">Bulan <span class="text-red-500">*</span></label>
                    <select name="month" id="month" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm @error('month') border-red-400 @enderror">
                        @php
                            $months = [
                                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                            ];
                        @endphp
                        @foreach($months as $num => $name)
                            <option value="{{ $num }}" {{ old('month', $period->month) == $num ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="year" class="block text-sm font-medium text-slate-700 mb-1">Tahun <span class="text-red-500">*</span></label>
                    <input type="number" name="year" id="year" value="{{ old('year', $period->year) }}" min="2000" max="2100" required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm @error('year') border-red-400 @enderror">
                </div>
            </div>

            <div>
                <label for="due_amount" class="block text-sm font-medium text-slate-700 mb-1">Iuran Khusus Periode Ini (Rp)</label>
                <input type="number" name="due_amount" id="due_amount" value="{{ old('due_amount', $period->due_amount) }}" min="0" step="1000" placeholder="Kosongkan jika mengikuti iuran default grup"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm @error('due_amount') border-red-400 @enderror">
            </div>

            <div>
                <label for="status" class="block text-sm font-medium text-slate-700 mb-1">Status Periode <span class="text-red-500">*</span></label>
                <select name="status" id="status" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm @error('status') border-red-400 @enderror">
                    <option value="draft" {{ old('status', $period->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="open" {{ old('status', $period->status) === 'open' ? 'selected' : '' }}>Open</option>
                    <option value="closed" {{ old('status', $period->status) === 'closed' ? 'selected' : '' }}>Closed</option>
                </select>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="rounded-lg bg-blue-600 px-5 py-2 text-sm font-semibold text-white hover:bg-blue-700 transition-colors">Perbarui</button>
                <a href="{{ route('periods.index', ['group_id' => $period->group_id]) }}" class="rounded-lg border border-slate-300 px-5 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
