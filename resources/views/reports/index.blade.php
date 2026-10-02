@extends('layouts.app')
@section('title', 'Laporan Kas & Export')
@section('header', 'Laporan Kas & Export')

@section('content')
<div x-data="reportPageApp()">
    {{-- Filter Selector & Action Bar --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-4 sm:p-5 mb-6">
        <form method="GET" class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div class="flex flex-wrap items-center gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Grup Kas</label>
                    <select name="group_id" onchange="this.form.submit()" class="rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-xs sm:text-sm font-semibold text-slate-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 shadow-2xs">
                        @foreach($groups as $g)
                            <option value="{{ $g->id }}" {{ $selectedGroupId == $g->id ? 'selected' : '' }}>{{ $g->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Pilih Periode</label>
                    <select name="period_id" onchange="this.form.submit()" class="rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-xs sm:text-sm font-semibold text-slate-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 shadow-2xs">
                        @foreach($periods as $p)
                            <option value="{{ $p->id }}" {{ $selectedPeriodId == $p->id ? 'selected' : '' }}>{{ $p->period_name }} ({{ strtoupper($p->status) }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            @if($period)
            <div class="flex flex-wrap items-center gap-2 pt-2 lg:pt-0">
                {{-- WhatsApp Actions --}}
                <div class="inline-flex items-center rounded-xl bg-emerald-600 p-0.5 shadow-xs">
                    <button type="button" @click="copyWhatsAppBroadcast()"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-white hover:bg-emerald-700 rounded-lg transition-colors">
                        <i class="bi bi-clipboard-check text-sm"></i>
                        <span>Salin Format WA</span>
                    </button>
                    <a :href="getWhatsAppShareUrl()" target="_blank"
                        class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-bold text-emerald-100 hover:text-white hover:bg-emerald-700/80 rounded-lg transition-colors border-l border-emerald-500/50"
                        title="Buka Langsung di WhatsApp">
                        <i class="bi bi-whatsapp text-sm"></i>
                        <span>Kirim WA</span>
                    </a>
                </div>

                {{-- Export Excel/CSV --}}
                <a href="{{ route('reports.export-excel', $period) }}"
                    class="inline-flex items-center gap-1.5 rounded-xl bg-blue-600 px-3.5 py-2 text-xs font-bold text-white hover:bg-blue-700 shadow-xs transition-colors">
                    <i class="bi bi-file-earmark-excel-fill text-sm"></i>
                    <span>Export Excel (CSV)</span>
                </a>

                {{-- Print PDF --}}
                <a href="{{ route('reports.print', $period) }}" target="_blank"
                    class="inline-flex items-center gap-1.5 rounded-xl bg-slate-900 px-3.5 py-2 text-xs font-bold text-white hover:bg-black shadow-xs transition-colors">
                    <i class="bi bi-printer-fill text-sm"></i>
                    <span>Cetak / PDF</span>
                </a>
            </div>
            @endif
        </form>
    </div>

    @if($period)
    {{-- Report Header Banner --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 mb-6">
        <div class="border-b border-slate-100 pb-4 mb-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-lg sm:text-xl font-extrabold text-slate-900">REKAPITULASI KAS: {{ strtoupper($period->group->name) }}</h2>
                    <span class="uppercase font-bold text-[11px] px-2.5 py-0.5 rounded-full {{ $period->status === 'closed' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-blue-50 text-blue-700 border border-blue-200' }}">
                        {{ $period->status }}
                    </span>
                </div>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                    Periode Laporan: <strong class="text-slate-800">{{ $period->period_name }}</strong>
                </p>
            </div>
            <div class="sm:text-right bg-slate-50 px-4 py-2 rounded-xl border border-slate-200/80">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Iuran per Anggota</p>
                <p class="text-sm sm:text-base font-extrabold text-slate-800">Rp {{ number_format($period->effective_due_amount, 0, ',', '.') }}</p>
            </div>
        </div>

        {{-- Financial Summary KPI Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-6 bg-slate-50 border border-slate-200/80 rounded-2xl p-4 text-center">
            <div class="p-2">
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Pemasukan</p>
                <p class="text-xl sm:text-2xl font-extrabold text-emerald-600 mt-1">Rp {{ number_format($period->total_income, 0, ',', '.') }}</p>
                <p class="text-[11px] text-slate-400 mt-0.5">{{ $period->incomes->count() }} setoran anggota</p>
            </div>
            <div class="p-2 border-y sm:border-y-0 sm:border-x border-slate-200">
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Pengeluaran</p>
                <p class="text-xl sm:text-2xl font-extrabold text-red-500 mt-1">Rp {{ number_format($period->total_expense, 0, ',', '.') }}</p>
                <p class="text-[11px] text-slate-400 mt-0.5">{{ $period->expenses->count() }} transaksi belanja</p>
            </div>
            <div class="p-2">
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Sisa Saldo Kas</p>
                <p class="text-xl sm:text-2xl font-extrabold {{ $period->balance >= 0 ? 'text-blue-600' : 'text-red-600' }} mt-1">
                    Rp {{ number_format($period->balance, 0, ',', '.') }}
                </p>
                <p class="text-[11px] font-bold {{ $period->balance >= 0 ? 'text-blue-600' : 'text-red-600' }} mt-0.5">
                    {{ $period->balance >= 0 ? 'Surplus / Sisa Kas' : 'Defisit Anggaran' }}
                </p>
            </div>
        </div>

        {{-- Interactive Report Tabs & Search Filter --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 border-b border-slate-200 pb-3 mb-5">
            <div class="flex flex-wrap items-center gap-1.5 bg-slate-100 p-1 rounded-xl">
                <button type="button" @click="activeTab = 'transaksi'"
                    :class="activeTab === 'transaksi' ? 'bg-white font-bold text-blue-600 shadow-xs' : 'font-semibold text-slate-600 hover:text-slate-900'"
                    class="px-3 py-1.5 rounded-lg text-xs transition-all">
                    <i class="bi bi-arrow-left-right"></i> Daftar Transaksi
                </button>
                <button type="button" @click="activeTab = 'kategori'"
                    :class="activeTab === 'kategori' ? 'bg-white font-bold text-blue-600 shadow-xs' : 'font-semibold text-slate-600 hover:text-slate-900'"
                    class="px-3 py-1.5 rounded-lg text-xs transition-all">
                    <i class="bi bi-pie-chart-fill"></i> Pos Kategori ({{ $categorySummary->count() }})
                </button>
                <button type="button" @click="activeTab = 'anggota'"
                    :class="activeTab === 'anggota' ? 'bg-white font-bold text-blue-600 shadow-xs' : 'font-semibold text-slate-600 hover:text-slate-900'"
                    class="px-3 py-1.5 rounded-lg text-xs transition-all">
                    <i class="bi bi-people-fill"></i> Status Anggota ({{ $periodArrears->count() }})
                </button>
                @if($period->expenses->flatMap->attachments->isNotEmpty())
                <button type="button" @click="activeTab = 'galeri'"
                    :class="activeTab === 'galeri' ? 'bg-white font-bold text-blue-600 shadow-xs' : 'font-semibold text-slate-600 hover:text-slate-900'"
                    class="px-3 py-1.5 rounded-lg text-xs transition-all">
                    <i class="bi bi-images"></i> Bukti Nota ({{ $period->expenses->flatMap->attachments->count() }})
                </button>
                @endif
            </div>

            {{-- Live Search Filter --}}
            <div x-show="activeTab === 'transaksi' || activeTab === 'anggota'" class="relative">
                <i class="bi bi-search absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                <input type="text" x-model="searchQuery" placeholder="Cari transaksi / nama..."
                    class="w-full md:w-56 rounded-xl border border-slate-300 bg-white pl-8 pr-3 py-1.5 text-xs font-medium text-slate-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 shadow-2xs">
            </div>
        </div>

        {{-- ========================================================================= --}}
        {{-- TAB 1: DAFTAR TRANSAKSI (PEMASUKAN & PENGELUARAN) --}}
        {{-- ========================================================================= --}}
        <div x-show="activeTab === 'transaksi'" x-transition.opacity>
            <div class="grid lg:grid-cols-2 gap-6">
                {{-- Incomes Table --}}
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                            <i class="bi bi-arrow-down-left-circle-fill text-emerald-500 text-base"></i>
                            Daftar Setoran Pemasukan
                        </h3>
                        <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-lg border border-emerald-200"
                            x-text="filteredIncomes.length + ' Setoran'"></span>
                    </div>
                    <div class="border border-slate-200 rounded-2xl overflow-hidden shadow-2xs">
                        <table class="w-full text-xs">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-3.5 py-2.5 text-left font-semibold text-slate-600">No</th>
                                    <th class="px-3.5 py-2.5 text-left font-semibold text-slate-600">Nama Anggota</th>
                                    <th class="px-3.5 py-2.5 text-left font-semibold text-slate-600">Tanggal</th>
                                    <th class="px-3.5 py-2.5 text-right font-semibold text-slate-600">Nominal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <template x-for="(inc, idx) in filteredIncomes" :key="inc.id">
                                    <tr class="hover:bg-slate-50/80 transition-colors">
                                        <td class="px-3.5 py-2.5 text-slate-400" x-text="idx + 1"></td>
                                        <td class="px-3.5 py-2.5">
                                            <p class="font-bold text-slate-800" x-text="inc.member_name"></p>
                                            <p x-show="inc.note" class="text-[10px] text-slate-400 mt-0.5" x-text="inc.note"></p>
                                        </td>
                                        <td class="px-3.5 py-2.5 text-slate-500 whitespace-nowrap" x-text="inc.date"></td>
                                        <td class="px-3.5 py-2.5 text-right font-bold text-emerald-600 whitespace-nowrap" x-text="rupiahFormat(inc.nominal)"></td>
                                    </tr>
                                </template>
                                <tr x-show="filteredIncomes.length === 0">
                                    <td colspan="4" class="px-3.5 py-6 text-center text-slate-400">Tidak ada pemasukan yang sesuai pencarian.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Expenses Table --}}
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                            <i class="bi bi-arrow-up-right-circle-fill text-red-500 text-base"></i>
                            Daftar Pengeluaran Kas
                        </h3>
                        <span class="text-xs font-bold text-red-700 bg-red-50 px-2 py-0.5 rounded-lg border border-red-200"
                            x-text="filteredExpenses.length + ' Belanja'"></span>
                    </div>
                    <div class="border border-slate-200 rounded-2xl overflow-hidden shadow-2xs">
                        <table class="w-full text-xs">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-3.5 py-2.5 text-left font-semibold text-slate-600">No</th>
                                    <th class="px-3.5 py-2.5 text-left font-semibold text-slate-600">Keperluan</th>
                                    <th class="px-3.5 py-2.5 text-left font-semibold text-slate-600">Kategori</th>
                                    <th class="px-3.5 py-2.5 text-right font-semibold text-slate-600">Nominal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <template x-for="(exp, idx) in filteredExpenses" :key="exp.id">
                                    <tr class="hover:bg-slate-50/80 transition-colors">
                                        <td class="px-3.5 py-2.5 text-slate-400" x-text="idx + 1"></td>
                                        <td class="px-3.5 py-2.5">
                                            <p class="font-bold text-slate-800" x-text="exp.item_name"></p>
                                            <p class="text-[10px] text-slate-400 mt-0.5" x-text="exp.date + (exp.note ? ' • ' + exp.note : '')"></p>
                                        </td>
                                        <td class="px-3.5 py-2.5">
                                            <span class="text-[10px] font-semibold bg-slate-100 text-slate-700 px-2 py-0.5 rounded-md" x-text="exp.category_name"></span>
                                        </td>
                                        <td class="px-3.5 py-2.5 text-right font-bold text-red-600 whitespace-nowrap" x-text="rupiahFormat(exp.nominal)"></td>
                                    </tr>
                                </template>
                                <tr x-show="filteredExpenses.length === 0">
                                    <td colspan="4" class="px-3.5 py-6 text-center text-slate-400">Tidak ada pengeluaran yang sesuai pencarian.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- ========================================================================= --}}
        {{-- TAB 2: RINGKASAN POS KATEGORI BELANJA --}}
        {{-- ========================================================================= --}}
        <div x-show="activeTab === 'kategori'" x-transition.opacity>
            <div class="grid lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 border border-slate-200 rounded-2xl overflow-hidden shadow-2xs">
                    <table class="w-full text-xs sm:text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-slate-600">No</th>
                                <th class="px-4 py-3 text-left font-semibold text-slate-600">Pos Kategori</th>
                                <th class="px-4 py-3 text-center font-semibold text-slate-600">Item</th>
                                <th class="px-4 py-3 text-right font-semibold text-slate-600">Total Biaya</th>
                                <th class="px-4 py-3 text-right font-semibold text-slate-600">Porsi (%)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($categorySummary as $i => $cat)
                            <tr class="hover:bg-slate-50/80">
                                <td class="px-4 py-3 text-slate-400">{{ $i + 1 }}</td>
                                <td class="px-4 py-3 font-bold text-slate-800">{{ $cat['name'] }}</td>
                                <td class="px-4 py-3 text-center text-slate-500">{{ $cat['count'] }} transaksi</td>
                                <td class="px-4 py-3 text-right font-bold text-red-600">Rp {{ number_format($cat['total'], 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right font-semibold text-slate-700">{{ $cat['percentage'] }}%</td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="px-4 py-8 text-center text-slate-400">Belum ada kategori pengeluaran.</td></tr>
                            @endforelse
                        </tbody>
                        @if($categorySummary->isNotEmpty())
                        <tfoot class="bg-slate-50 font-bold border-t border-slate-200">
                            <tr>
                                <td colspan="3" class="px-4 py-3 text-right text-slate-700">Total Pengeluaran:</td>
                                <td class="px-4 py-3 text-right text-red-600 font-extrabold">Rp {{ number_format($period->total_expense, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right text-slate-800 font-extrabold">100%</td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>

                {{-- Visual Alokasi Bar --}}
                <div class="bg-slate-50 rounded-2xl p-5 border border-slate-200 flex flex-col justify-between">
                    <div>
                        <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-3">Distribusi Porsi Belanja</h4>
                        <div class="space-y-3">
                            @foreach($categorySummary as $cat)
                            <div>
                                <div class="flex justify-between text-xs font-bold mb-1">
                                    <span class="text-slate-700">{{ $cat['name'] }}</span>
                                    <span class="text-slate-500">{{ $cat['percentage'] }}%</span>
                                </div>
                                <div class="h-2 w-full bg-slate-200 rounded-full overflow-hidden">
                                    <div class="h-full bg-blue-600 rounded-full" style="width: {{ $cat['percentage'] }}%"></div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ========================================================================= --}}
        {{-- TAB 3: STATUS ANGGOTA PERIODE INI --}}
        {{-- ========================================================================= --}}
        <div x-show="activeTab === 'anggota'" x-transition.opacity>
            <div class="border border-slate-200 rounded-2xl overflow-hidden shadow-2xs">
                <table class="w-full text-xs sm:text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-slate-600">Anggota</th>
                            <th class="px-4 py-3 text-right font-semibold text-slate-600">Kewajiban</th>
                            <th class="px-4 py-3 text-right font-semibold text-slate-600">Setoran Masuk</th>
                            <th class="px-4 py-3 text-right font-semibold text-slate-600">Sisa Tagihan</th>
                            <th class="px-4 py-3 text-center font-semibold text-slate-600">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <template x-for="row in filteredPeriodArrears" :key="row.member_id">
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-4 py-3">
                                    <p class="font-bold text-slate-800" x-text="row.member_name"></p>
                                    <p class="text-xs text-slate-400" x-text="row.member_phone || '—'"></p>
                                </td>
                                <td class="px-4 py-3 text-right font-medium text-slate-600" x-text="rupiahFormat(row.total_expected)"></td>
                                <td class="px-4 py-3 text-right font-bold text-emerald-600" x-text="rupiahFormat(row.total_paid)"></td>
                                <td class="px-4 py-3 text-right font-bold"
                                    :class="row.total_shortage > 0 ? 'text-red-600' : 'text-emerald-600'"
                                    x-text="row.total_shortage > 0 ? '- ' + rupiahFormat(row.total_shortage) : 'Rp 0'"></td>
                                <td class="px-4 py-3 text-center">
                                    <span :class="row.total_shortage > 0 ? 'bg-red-50 text-red-700 border-red-200' : 'bg-emerald-50 text-emerald-700 border-emerald-200'"
                                        class="inline-flex items-center gap-1 rounded-full border px-2.5 py-0.5 text-xs font-bold"
                                        x-text="row.total_shortage > 0 ? 'Belum Lunas' : '✓ Lunas'"></span>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ========================================================================= --}}
        {{-- TAB 4: GALERI BUKTI NOTA --}}
        {{-- ========================================================================= --}}
        @php
            $allAttachments = $period->expenses->flatMap->attachments;
        @endphp
        @if($allAttachments->isNotEmpty())
        <div x-show="activeTab === 'galeri'" x-transition.opacity>
            <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-3">
                @foreach($allAttachments as $att)
                <div class="group relative rounded-xl border border-slate-200 overflow-hidden bg-slate-100 aspect-square shadow-2xs cursor-pointer"
                    @click="modalPhoto = '{{ asset('storage/' . $att->file_path) }}'">
                    <img src="{{ asset('storage/' . $att->file_path) }}" alt="Bukti" class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-200">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-2 text-[10px] text-white">
                        <span class="truncate font-medium">{{ $att->expense->item_name ?? 'Bukti Nota' }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
    @else
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-12 text-center text-slate-400">
        <i class="bi bi-file-earmark-bar-graph text-4xl block mb-2 text-slate-300"></i>
        Pilih grup dan periode untuk melihat rekapitulasi laporan kas.
    </div>
    @endif

    {{-- Lightbox Modal --}}
    <div x-show="modalPhoto" x-transition.opacity
        class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-xs flex items-center justify-center p-4"
        @click="modalPhoto = null" style="display: none;">
        <div class="relative max-w-3xl max-h-[90vh] bg-white rounded-2xl overflow-hidden p-3 shadow-2xl" @click.stop>
            <button @click="modalPhoto = null" class="absolute top-4 right-4 h-8 w-8 rounded-full bg-slate-900/70 hover:bg-slate-900 text-white flex items-center justify-center text-sm transition-colors">
                <i class="bi bi-x-lg"></i>
            </button>
            <img :src="modalPhoto" alt="Preview Bukti" class="max-h-[80vh] w-auto mx-auto rounded-xl">
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function rupiahFormat(num) {
    return 'Rp ' + new Intl.NumberFormat('id-ID').format(num);
}

function reportPageApp() {
    return {
        activeTab: 'transaksi',
        searchQuery: '',
        modalPhoto: null,

        // Data arrays for client-side search & broadcast
        incomes: [
            @if($period)
                @foreach($period->incomes as $inc)
                {
                    id: {{ $inc->id }},
                    member_name: @json($inc->member->name ?? '—'),
                    nominal: {{ (float) $inc->nominal }},
                    date: '{{ $inc->transaction_date->format('d/m/Y') }}',
                    note: @json($inc->note ?? ''),
                },
                @endforeach
            @endif
        ],

        expenses: [
            @if($period)
                @foreach($period->expenses as $exp)
                {
                    id: {{ $exp->id }},
                    item_name: @json($exp->item_name),
                    category_name: @json($exp->category->name ?? 'Umum'),
                    nominal: {{ (float) $exp->nominal }},
                    date: '{{ $exp->transaction_date->format('d/m/Y') }}',
                    note: @json($exp->note ?? ''),
                },
                @endforeach
            @endif
        ],

        periodArrearsList: @json($periodArrears ?? []),

        get filteredIncomes() {
            if (!this.searchQuery.trim()) return this.incomes;
            const q = this.searchQuery.toLowerCase();
            return this.incomes.filter(i => i.member_name.toLowerCase().includes(q) || i.note.toLowerCase().includes(q));
        },

        get filteredExpenses() {
            if (!this.searchQuery.trim()) return this.expenses;
            const q = this.searchQuery.toLowerCase();
            return this.expenses.filter(e => e.item_name.toLowerCase().includes(q) || e.category_name.toLowerCase().includes(q) || e.note.toLowerCase().includes(q));
        },

        get filteredPeriodArrears() {
            if (!this.searchQuery.trim()) return this.periodArrearsList;
            const q = this.searchQuery.toLowerCase();
            return this.periodArrearsList.filter(m => m.member_name.toLowerCase().includes(q));
        },

        getWhatsAppMessage() {
            @if($period)
            let msg = `📢 *LAPORAN KAS BULANAN*\n`;
            msg += `*${@json($period->group->name)}*\n`;
            msg += `🗓️ *Periode:* ${@json($period->period_name)}\n`;
            msg += `📊 *Status:* ${@json(strtoupper($period->status))}\n\n`;

            msg += `━━━━━━━━━━━━━━━━━━━━\n`;
            msg += `💰 *Total Pemasukan:* Rp ${new Intl.NumberFormat('id-ID').format({{ $period->total_income }})}\n`;
            msg += `💸 *Total Pengeluaran:* Rp ${new Intl.NumberFormat('id-ID').format({{ $period->total_expense }})}\n`;
            msg += `💵 *Sisa Saldo Kas:* Rp ${new Intl.NumberFormat('id-ID').format({{ $period->balance }})}\n`;
            msg += `━━━━━━━━━━━━━━━━━━━━\n\n`;

            msg += `✅ *Ringkasan Pengeluaran:*\n`;
            @foreach($categorySummary as $cat)
            msg += `• ${@json($cat['name'])}: Rp ${new Intl.NumberFormat('id-ID').format({{ $cat['total'] }})}\n`;
            @endforeach

            @if($period->group->is_public)
            msg += `\n🌐 *Cek Detail Laporan Online & Transparan:*\n`;
            msg += `${window.location.origin}/publik/${@json($period->group->slug)}/${@json($period->year)}/${@json($period->month)}\n`;
            @endif

            msg += `\nTerima kasih atas partisipasi dan amanah seluruh anggota 🙏`;
            return msg;
            @else
            return '';
            @endif
        },

        getWhatsAppShareUrl() {
            const msg = this.getWhatsAppMessage();
            return `https://api.whatsapp.com/send?text=${encodeURIComponent(msg)}`;
        },

        copyWhatsAppBroadcast() {
            const msg = this.getWhatsAppMessage();
            if (!msg) return;
            window.copyToClipboard(msg, 'Format WhatsApp berhasil disalin!');
        }
    };
}
</script>
@endpush
