@extends('layouts.app')
@section('title', 'Input Setoran Kas')
@section('header', 'Input Setoran Kas Anggota')

@section('content')
<div class="max-w-lg" x-data="{
    defaultNominal: {{ $defaultDue }},
    changeGroup(id) {
        window.location.href = '{{ route('incomes.create') }}?group_id=' + id;
    }
}">
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <form method="POST" action="{{ route('incomes.store') }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Grup Kas</label>
                <select @change="changeGroup($event.target.value)" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    @foreach($groups as $g)
                        <option value="{{ $g->id }}" {{ $selectedGroupId == $g->id ? 'selected' : '' }}>{{ $g->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="period_id" class="block text-sm font-medium text-slate-700 mb-1">Periode Kas <span class="text-red-500">*</span></label>
                <select name="period_id" id="period_id" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm @error('period_id') border-red-400 @enderror">
                    <option value="">-- Pilih Periode --</option>
                    @foreach($periods as $p)
                        <option value="{{ $p->id }}" {{ old('period_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->period_name }} (Iuran: Rp {{ number_format($p->effective_due_amount, 0, ',', '.') }})
                        </option>
                    @endforeach
                </select>
                @if($periods->isEmpty())
                    <p class="text-xs text-amber-600 mt-1">Belum ada periode open/draft di grup ini. <a href="{{ route('periods.create') }}" class="underline">Buka periode baru</a>.</p>
                @endif
            </div>

            <div>
                <label for="member_id" class="block text-sm font-medium text-slate-700 mb-1">Nama Anggota <span class="text-red-500">*</span></label>
                <select name="member_id" id="member_id" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm @error('member_id') border-red-400 @enderror">
                    <option value="">-- Pilih Anggota --</option>
                    @foreach($members as $m)
                        <option value="{{ $m->id }}" {{ old('member_id') == $m->id ? 'selected' : '' }}>{{ $m->name }} {{ $m->phone ? "({$m->phone})" : '' }}</option>
                    @endforeach
                </select>
                @if($members->isEmpty())
                    <p class="text-xs text-amber-600 mt-1">Belum ada anggota aktif di grup ini. <a href="{{ route('members.create') }}" class="underline">Tambah anggota</a>.</p>
                @endif
            </div>

            <div>
                <label for="nominal" class="block text-sm font-medium text-slate-700 mb-1">Nominal Setoran (Rp) <span class="text-red-500">*</span></label>
                <input type="number" name="nominal" id="nominal" 
                    value="{{ old('nominal', (isset($defaultDue) && $defaultDue > 0) ? (float) $defaultDue : '') }}" 
                    min="0" step="1" required
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-800 @error('nominal') border-red-400 @enderror">
            </div>

            <div>
                <label for="transaction_date" class="block text-sm font-medium text-slate-700 mb-1">Tanggal & Waktu Bayar <span class="text-red-500">*</span></label>
                <input type="datetime-local" name="transaction_date" id="transaction_date" value="{{ old('transaction_date', now()->format('Y-m-d\TH:i')) }}" required
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm @error('transaction_date') border-red-400 @enderror">
            </div>

            <div>
                <label for="note" class="block text-sm font-medium text-slate-700 mb-1">Catatan / Keterangan</label>
                <input type="text" name="note" id="note" value="{{ old('note') }}" placeholder="Contoh: Titip lewat Pak RT, via transfer BCA"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm @error('note') border-red-400 @enderror">
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="rounded-lg bg-blue-600 px-5 py-2 text-sm font-semibold text-white hover:bg-blue-700 transition-colors">Simpan Setoran</button>
                <a href="{{ route('incomes.index') }}" class="rounded-lg border border-slate-300 px-5 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
