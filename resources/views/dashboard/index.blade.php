@extends('layouts.app')

@section('title', 'Dashboard')
@section('header', 'Dashboard')

@section('header-actions')
    <div class="flex items-center gap-2">
        <label for="group-selector" class="text-xs text-slate-500 hidden sm:inline"><i class="bi bi-funnel"></i> Grup:</label>
        <select id="group-selector" onchange="changeGroup(this.value)"
            class="rounded-xl border border-slate-300 bg-white px-3 py-1.5 text-xs sm:text-sm font-medium text-slate-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 shadow-xs">
            <option value="">Semua Grup</option>
            @foreach($groups as $g)
                <option value="{{ $g->id }}" {{ $selectedGroupId == $g->id ? 'selected' : '' }}>{{ $g->name }}</option>
            @endforeach
        </select>
    </div>
@endsection

@section('content')
{{-- KPI Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-5 shadow-xs flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Pemasukan</p>
            <p class="mt-1 text-lg sm:text-xl font-bold text-emerald-600">{{ 'Rp ' . number_format($totalIncome, 0, ',', '.') }}</p>
        </div>
        <div class="h-10 w-10 sm:h-12 sm:w-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl sm:text-2xl">
            <i class="bi bi-arrow-down-left-circle-fill"></i>
        </div>
    </div>
    
    <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-5 shadow-xs flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Pengeluaran</p>
            <p class="mt-1 text-lg sm:text-xl font-bold text-red-500">{{ 'Rp ' . number_format($totalExpense, 0, ',', '.') }}</p>
        </div>
        <div class="h-10 w-10 sm:h-12 sm:w-12 rounded-xl bg-red-50 text-red-500 flex items-center justify-center text-xl sm:text-2xl">
            <i class="bi bi-arrow-up-right-circle-fill"></i>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-5 shadow-xs flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Saldo Kas</p>
            <p class="mt-1 text-lg sm:text-xl font-bold {{ $totalBalance >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                {{ 'Rp ' . number_format($totalBalance, 0, ',', '.') }}
            </p>
        </div>
        <div class="h-10 w-10 sm:h-12 sm:w-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl sm:text-2xl">
            <i class="bi bi-wallet2"></i>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-5 shadow-xs flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Anggota Aktif</p>
            <p class="mt-1 text-lg sm:text-xl font-bold text-slate-800">{{ $totalMembers }}</p>
        </div>
        <div class="h-10 w-10 sm:h-12 sm:w-12 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center text-xl sm:text-2xl">
            <i class="bi bi-people-fill"></i>
        </div>
    </div>
</div>

{{-- Chart --}}
@if(count($chartLabels) > 0)
<div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-5 mb-6">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-sm font-bold text-slate-800 flex items-center gap-2">
            <i class="bi bi-bar-chart-fill text-blue-600"></i> Tren Pemasukan vs Pengeluaran
        </h2>
    </div>
    <div class="relative h-60">
        <canvas id="trendChart"></canvas>
    </div>
</div>
@endif

{{-- Arrears Panel --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden" x-data="arrearsPanel()" x-init="init()">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-5 border-b border-slate-100">
        <div>
            <h2 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                <i class="bi bi-exclamation-triangle-fill text-red-500"></i> Panel Anggota Menunggak
            </h2>
            <p class="text-xs text-slate-500 mt-0.5">Monitoring anggota yang total setoran kurang dari kewajiban iuran</p>
        </div>
        <div class="flex items-center gap-3">
            <label class="flex items-center gap-1.5 text-xs text-slate-600 cursor-pointer select-none">
                <input type="checkbox" x-model="onlyArrears" @change="load()" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500/20">
                <span>Hanya yang nunggak</span>
            </label>
            <select x-model="sortBy" @change="sort()" class="text-xs border border-slate-300 rounded-xl px-2.5 py-1.5 bg-white text-slate-700 font-medium">
                <option value="total_shortage">Terbesar nunggak</option>
                <option value="total_paid">Terkecil bayar</option>
                <option value="member_name">Nama A-Z</option>
                <option value="unpaid_months_count">Bulan nunggak</option>
            </select>
        </div>
    </div>

    {{-- Loading state --}}
    <div x-show="loading" class="p-10 text-center text-sm text-slate-400">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mb-2"></div>
        <p>Memuat data tunggakan...</p>
    </div>

    <div x-show="!loading">
        {{-- Empty state --}}
        <div x-show="rows.length === 0" class="py-12 text-center text-sm text-slate-400">
            <div class="text-emerald-500 text-3xl mb-2"><i class="bi bi-check-circle-fill"></i></div>
            <p class="font-medium text-slate-600">Lunas Semua!</p>
            <p class="text-xs text-slate-400 mt-0.5">Tidak ada anggota yang menunggak iuran kas.</p>
        </div>

        {{-- Arrears table --}}
        <div x-show="rows.length > 0" class="overflow-x-auto">
            <table class="w-full text-xs sm:text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="text-left font-semibold text-slate-600 px-5 py-3 text-xs uppercase tracking-wider">Anggota</th>
                        <th class="text-left font-semibold text-slate-600 px-3 py-3 text-xs uppercase tracking-wider hidden sm:table-cell">Grup</th>
                        <th class="text-right font-semibold text-slate-600 px-3 py-3 text-xs uppercase tracking-wider">Kewajiban</th>
                        <th class="text-right font-semibold text-slate-600 px-3 py-3 text-xs uppercase tracking-wider">Dibayar</th>
                        <th class="text-right font-semibold text-slate-600 px-3 py-3 text-xs uppercase tracking-wider">Tunggakan</th>
                        <th class="text-center font-semibold text-slate-600 px-3 py-3 text-xs uppercase tracking-wider">Bulan</th>
                        <th class="px-3 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <template x-for="row in rows" :key="row.member_id">
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-5 py-3">
                                <p class="font-semibold text-slate-800" x-text="row.member_name"></p>
                                <p class="text-xs text-slate-400 flex items-center gap-1 mt-0.5">
                                    <i class="bi bi-telephone text-[10px]"></i>
                                    <span x-text="row.member_phone || '—'"></span>
                                </p>
                            </td>
                            <td class="px-3 py-3 text-slate-500 hidden sm:table-cell" x-text="row.group_name"></td>
                            <td class="px-3 py-3 text-right text-slate-600" x-text="rupiahFormat(row.total_expected)"></td>
                            <td class="px-3 py-3 text-right text-emerald-600 font-semibold" x-text="rupiahFormat(row.total_paid)"></td>
                            <td class="px-3 py-3 text-right">
                                <span :class="row.total_shortage > 0 ? 'text-red-600 font-bold' : 'text-emerald-600 font-semibold'"
                                    x-text="row.total_shortage > 0 ? '- ' + rupiahFormat(row.total_shortage) : '✓ Lunas'"></span>
                            </td>
                            <td class="px-3 py-3 text-center">
                                <span x-show="row.unpaid_months_count > 0"
                                    class="inline-flex items-center gap-1 rounded-full bg-red-100 text-red-700 px-2.5 py-0.5 text-xs font-semibold"
                                    x-text="row.unpaid_months_count + ' bln'"></span>
                                <span x-show="row.unpaid_months_count === 0" class="text-xs text-slate-400">—</span>
                            </td>
                            <td class="px-3 py-3">
                                <button x-show="row.unpaid_months_count > 0"
                                    @click="row._expanded = !row._expanded"
                                    class="text-xs text-blue-600 hover:text-blue-800 font-semibold flex items-center gap-1 whitespace-nowrap">
                                    <i :class="row._expanded ? 'bi bi-chevron-up' : 'bi bi-chevron-down'"></i>
                                    <span x-text="row._expanded ? 'Tutup' : 'Detail'"></span>
                                </button>
                            </td>
                        </tr>
                        {{-- Drill-down row --}}
                        <tr x-show="row._expanded" class="bg-blue-50/40">
                            <td colspan="7" class="px-5 py-3">
                                <p class="text-xs font-bold text-slate-700 mb-2 flex items-center gap-1.5">
                                    <i class="bi bi-calendar-x text-red-500"></i> Rincian bulan yang belum lunas:
                                </p>
                                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-2.5">
                                    <template x-for="period in row.unpaid_periods" :key="period.period_id">
                                        <div class="bg-white rounded-xl border border-red-200/80 p-3 text-xs shadow-xs">
                                            <p class="font-bold text-slate-800 flex items-center justify-between" x-text="period.period_name"></p>
                                            <div class="flex justify-between mt-1 text-slate-500">
                                                <span>Kewajiban:</span>
                                                <span class="font-medium text-slate-700" x-text="rupiahFormat(period.due_amount)"></span>
                                            </div>
                                            <div class="flex justify-between text-slate-500">
                                                <span>Dibayar:</span>
                                                <span class="font-medium text-emerald-600" x-text="rupiahFormat(period.paid_amount)"></span>
                                            </div>
                                            <div class="flex justify-between border-t border-slate-100 mt-1.5 pt-1.5 font-bold">
                                                <span class="text-red-600">Kurang:</span>
                                                <span class="text-red-600" x-text="'- ' + rupiahFormat(period.shortage)"></span>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
@if(count($chartLabels) > 0)
const ctx = document.getElementById('trendChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: @json($chartLabels),
        datasets: [
            {
                label: 'Pemasukan',
                data: @json($chartIncome),
                backgroundColor: 'rgba(16, 185, 129, 0.8)',
                borderRadius: 6,
            },
            {
                label: 'Pengeluaran',
                data: @json($chartExpense),
                backgroundColor: 'rgba(239, 68, 68, 0.8)',
                borderRadius: 6,
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'top', labels: { font: { size: 11 } } },
            tooltip: {
                callbacks: {
                    label: (ctx) => ' Rp ' + new Intl.NumberFormat('id-ID').format(ctx.raw)
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: (val) => 'Rp ' + new Intl.NumberFormat('id-ID').format(val),
                    font: { size: 10 }
                }
            },
            x: { ticks: { font: { size: 10 } } }
        }
    }
});
@endif

function rupiahFormat(num) {
    return 'Rp ' + new Intl.NumberFormat('id-ID').format(num);
}

function changeGroup(groupId) {
    const url = new URL(window.location.href);
    if (groupId) {
        url.searchParams.set('group_id', groupId);
    } else {
        url.searchParams.delete('group_id');
    }
    window.location.href = url.toString();
}

function arrearsPanel() {
    return {
        rows: @json($arrears),
        loading: false,
        onlyArrears: false,
        sortBy: 'total_shortage',

        init() {
            this.rows = this.rows.map(r => ({ ...r, _expanded: false }));
        },

        async load() {
            this.loading = true;
            const groupId = {{ $selectedGroupId ?? 'null' }};
            let url = '{{ route('dashboard.arrears-data') }}?';
            if (groupId) url += `group_id=${groupId}&`;
            if (this.onlyArrears) url += 'only_arrears=1&';

            try {
                const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                const data = await res.json();
                this.rows = data.data.map(r => ({ ...r, _expanded: false }));
                this.sort();
            } catch (e) {
                console.error(e);
            } finally {
                this.loading = false;
            }
        },

        sort() {
            const key = this.sortBy;
            this.rows.sort((a, b) => {
                if (key === 'member_name') return a.member_name.localeCompare(b.member_name);
                return b[key] - a[key];
            });
        }
    };
}
</script>
@endpush
