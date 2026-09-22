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
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 sm:p-8">
        <form method="POST" action="{{ route('incomes.store') }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Grup Kas</label>
                <select @change="changeGroup($event.target.value)" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm font-medium text-slate-800 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                    @foreach($groups as $g)
                        <option value="{{ $g->id }}" {{ $selectedGroupId == $g->id ? 'selected' : '' }}>{{ $g->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="period_id" class="block text-xs font-semibold text-slate-700 mb-1.5">Periode Kas <span class="text-red-500">*</span></label>
                <select name="period_id" id="period_id" required class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-800 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 @error('period_id') border-red-400 @enderror">
                    <option value="">-- Pilih Periode --</option>
                    @foreach($periods as $p)
                        <option value="{{ $p->id }}" {{ old('period_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->period_name }} (Iuran: Rp {{ number_format($p->effective_due_amount, 0, ',', '.') }})
                        </option>
                    @endforeach
                </select>
                @if($periods->isEmpty())
                    <p class="text-xs text-amber-600 mt-1 flex items-center gap-1"><i class="bi bi-info-circle"></i> Belum ada periode open/draft di grup ini. <a href="{{ route('periods.create') }}" class="underline font-semibold">Buka periode baru</a>.</p>
                @endif
            </div>

            <div>
                <label for="member_id" class="block text-xs font-semibold text-slate-700 mb-1.5">Nama Anggota <span class="text-red-500">*</span></label>
                <select name="member_id" id="member_id" required class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-800 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 @error('member_id') border-red-400 @enderror">
                    <option value="">-- Pilih Anggota --</option>
                    @foreach($members as $m)
                        <option value="{{ $m->id }}" {{ old('member_id') == $m->id ? 'selected' : '' }}>{{ $m->name }} {{ $m->phone ? "({$m->phone})" : '' }}</option>
                    @endforeach
                </select>
                @if($members->isEmpty())
                    <p class="text-xs text-amber-600 mt-1 flex items-center gap-1"><i class="bi bi-info-circle"></i> Belum ada anggota aktif di grup ini. <a href="{{ route('members.create') }}" class="underline font-semibold">Tambah anggota</a>.</p>
                @endif
            </div>

            <div>
                <label for="nominal" class="block text-xs font-semibold text-slate-700 mb-1.5">Nominal Setoran (Rp) <span class="text-red-500">*</span></label>
                <input type="number" name="nominal" id="nominal" value="{{ old('nominal', $defaultDue > 0 ? $defaultDue : '') }}" min="1" step="500" required
                    class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm font-bold text-slate-800 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 @error('nominal') border-red-400 @enderror">
            </div>

            <div>
                <label for="transaction_date" class="block text-xs font-semibold text-slate-700 mb-1.5">Tanggal & Waktu Bayar <span class="text-red-500">*</span></label>
                <input type="datetime-local" name="transaction_date" id="transaction_date" value="{{ old('transaction_date', now()->format('Y-m-d\TH:i')) }}" required
                    class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-800 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 @error('transaction_date') border-red-400 @enderror">
            </div>

            <div>
                <label for="note" class="block text-xs font-semibold text-slate-700 mb-1.5">Catatan / Keterangan</label>
                <input type="text" name="note" id="note" value="{{ old('note') }}" placeholder="Contoh: Titip lewat Pak RT, via transfer BCA"
                    class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-800 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 @error('note') border-red-400 @enderror">
            </div>

            <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
                <button type="submit" class="inline-flex items-center gap-1.5 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 shadow-xs transition-colors">
                    <i class="bi bi-check-lg"></i> Simpan Setoran
                </button>
                <a href="{{ route('incomes.index') }}" class="rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
