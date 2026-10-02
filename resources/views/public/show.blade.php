<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Kas: {{ $group->name }} — SDA Kas</title>
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#2563eb">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    
    {{-- Bootstrap Icons CDN --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    {{-- Tailwind CSS CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['"Instrument Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif'] },
                    colors: { primary: '#2563eb', 'primary-dark': '#1d4ed8' }
                }
            }
        }
    </script>
</head>
<body class="min-h-full font-sans antialiased text-slate-800 p-3 sm:p-6 md:p-8" x-data="publicKasApp()">
    <div class="max-w-6xl mx-auto">
        {{-- Public Header --}}
        <header class="bg-white rounded-2xl border border-slate-200 shadow-xs p-5 sm:p-6 mb-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-blue-50 border border-blue-100 p-2 shadow-inner">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo SDA Kas" class="h-full w-full object-contain">
                    </div>
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">{{ $group->name }}</h1>
                            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 border border-emerald-200 px-2.5 py-0.5 text-xs font-semibold text-emerald-700">
                                <i class="bi bi-shield-check"></i> Transparan & Publik
                            </span>
                        </div>
                        @if($group->description)
                            <p class="text-xs sm:text-sm text-slate-500 mt-1">{{ $group->description }}</p>
                        @endif
                    </div>
                </div>

                {{-- Mode Switcher Tabs --}}
                <div class="flex items-center bg-slate-100 p-1 rounded-xl border border-slate-200 self-start md:self-auto">
                    <button type="button" @click="viewMode = 'rekap'"
                        :class="viewMode === 'rekap' ? 'bg-white text-blue-600 shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900 font-medium'"
                        class="flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-xs sm:text-sm transition-all duration-150">
                        <i class="bi bi-pie-chart-fill"></i>
                        <span>Opsi Rekap & Tunggakan</span>
                    </button>
                    <button type="button" @click="viewMode = 'bulanan'"
                        :class="viewMode === 'bulanan' ? 'bg-white text-blue-600 shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900 font-medium'"
                        class="flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-xs sm:text-sm transition-all duration-150">
                        <i class="bi bi-calendar3"></i>
                        <span>Opsi Rincian Bulanan</span>
                    </button>
                </div>
            </div>
        </header>

        {{-- ========================================================================= --}}
        {{-- VIEW MODE: OPSI REKAP & TUNGGAKAN --}}
        {{-- ========================================================================= --}}
        <div x-show="viewMode === 'rekap'" x-transition.opacity>
            {{-- KPI Summary Cards --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-5 shadow-xs flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Pemasukan</p>
                        <p class="mt-1 text-lg sm:text-xl font-bold text-emerald-600">Rp {{ number_format($totalIncome, 0, ',', '.') }}</p>
                    </div>
                    <div class="h-10 w-10 sm:h-12 sm:w-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl sm:text-2xl">
                        <i class="bi bi-arrow-down-left-circle-fill"></i>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-5 shadow-xs flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Pengeluaran</p>
                        <p class="mt-1 text-lg sm:text-xl font-bold text-red-500">Rp {{ number_format($totalExpense, 0, ',', '.') }}</p>
                    </div>
                    <div class="h-10 w-10 sm:h-12 sm:w-12 rounded-xl bg-red-50 text-red-500 flex items-center justify-center text-xl sm:text-2xl">
                        <i class="bi bi-arrow-up-right-circle-fill"></i>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-5 shadow-xs flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Sisa Saldo Kas</p>
                        <p class="mt-1 text-lg sm:text-xl font-bold {{ $totalBalance >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                            Rp {{ number_format($totalBalance, 0, ',', '.') }}
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

            {{-- Trend Chart --}}
            @if(count($chartLabels) > 0)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-5 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                        <i class="bi bi-bar-chart-fill text-blue-600"></i> Tren Pemasukan vs Pengeluaran (Periode Tutup Buku)
                    </h2>
                </div>
                <div class="relative h-64">
                    <canvas id="publicTrendChart"></canvas>
                </div>
            </div>
            @endif

            {{-- Arrears Panel (Tunggakan Anggota) --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden mb-6">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 p-5 border-b border-slate-100">
                    <div>
                        <h2 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                            <i class="bi bi-exclamation-triangle-fill text-red-500"></i> Panel Transparansi Tunggakan Kas
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">Daftar anggota yang memiliki kewajiban iuran belum terlunasi</p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                        {{-- Search --}}
                        <div class="relative">
                            <i class="bi bi-search absolute left-3 top-2 text-slate-400 text-xs"></i>
                            <input type="text" x-model="searchQuery" placeholder="Cari nama..."
                                class="rounded-xl border border-slate-300 bg-white pl-8 pr-3 py-1.5 text-xs font-medium text-slate-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 shadow-2xs">
                        </div>

                        {{-- Only Arrears Checkbox --}}
                        <label class="flex items-center gap-1.5 text-xs text-slate-600 cursor-pointer select-none bg-slate-50 px-2.5 py-1.5 rounded-xl border border-slate-200">
                            <input type="checkbox" x-model="onlyArrears" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500/20">
                            <span>Hanya yang nunggak</span>
                        </label>

                        {{-- Sort --}}
                        <select x-model="sortBy" class="text-xs border border-slate-300 rounded-xl px-2.5 py-1.5 bg-white text-slate-700 font-medium">
                            <option value="total_shortage">Terbesar nunggak</option>
                            <option value="total_paid">Terkecil bayar</option>
                            <option value="member_name">Nama A-Z</option>
                            <option value="unpaid_months_count">Bulan nunggak</option>
                        </select>
                    </div>
                </div>

                {{-- Empty state --}}
                <div x-show="filteredRows.length === 0" class="py-12 text-center text-sm text-slate-400">
                    <div class="text-emerald-500 text-3xl mb-2"><i class="bi bi-check-circle-fill"></i></div>
                    <p class="font-medium text-slate-600">Semua Terbayar / Data Tidak Ditemukan</p>
                    <p class="text-xs text-slate-400 mt-0.5">Tidak ada anggota yang memenuhi kriteria filter saat ini.</p>
                </div>

                {{-- Arrears Table --}}
                <div x-show="filteredRows.length > 0" class="overflow-x-auto">
                    <table class="w-full text-xs sm:text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="text-left font-semibold text-slate-600 px-5 py-3 text-xs uppercase tracking-wider">Anggota</th>
                                <th class="text-right font-semibold text-slate-600 px-3 py-3 text-xs uppercase tracking-wider">Kewajiban</th>
                                <th class="text-right font-semibold text-slate-600 px-3 py-3 text-xs uppercase tracking-wider">Dibayar</th>
                                <th class="text-right font-semibold text-slate-600 px-3 py-3 text-xs uppercase tracking-wider">Tunggakan</th>
                                <th class="text-center font-semibold text-slate-600 px-3 py-3 text-xs uppercase tracking-wider">Bulan</th>
                                <th class="px-3 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <template x-for="row in filteredRows" :key="row.member_id">
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-5 py-3">
                                        <p class="font-semibold text-slate-800" x-text="row.member_name"></p>
                                    </td>
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
                                    <td class="px-3 py-3 text-right">
                                        <button x-show="row.unpaid_months_count > 0"
                                            @click="row._expanded = !row._expanded"
                                            class="text-xs text-blue-600 hover:text-blue-800 font-semibold inline-flex items-center gap-1 whitespace-nowrap">
                                            <i :class="row._expanded ? 'bi bi-chevron-up' : 'bi bi-chevron-down'"></i>
                                            <span x-text="row._expanded ? 'Tutup' : 'Detail'"></span>
                                        </button>
                                    </td>
                                </tr>
                                {{-- Drill-down row for unpaid periods --}}
                                <tr x-show="row._expanded" class="bg-blue-50/40">
                                    <td colspan="6" class="px-5 py-3">
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

        {{-- ========================================================================= --}}
        {{-- VIEW MODE: OPSI RINCIAN BULANAN --}}
        {{-- ========================================================================= --}}
        <div x-show="viewMode === 'bulanan'" x-transition.opacity>
            {{-- Period Selector Toolbar --}}
            @if($closedPeriods->isNotEmpty())
            <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-xs mb-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <span class="text-sm font-bold text-slate-800 flex items-center gap-1.5">
                        <i class="bi bi-calendar-event text-blue-600"></i> Periode Laporan:
                    </span>
                    <span class="text-xs text-slate-500">Hanya menampilkan periode yang sudah tutup buku</span>
                </div>
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <select id="period-select" onchange="window.location.href = this.value"
                        class="w-full sm:w-auto rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-xs sm:text-sm font-bold text-slate-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 shadow-2xs">
                        @foreach($closedPeriods as $cp)
                            <option value="{{ route('public.group.period', ['slug' => $group->slug, 'year' => $cp->year, 'month' => $cp->month]) }}"
                                {{ $selectedPeriod && $selectedPeriod->id === $cp->id ? 'selected' : '' }}>
                                {{ $cp->period_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            @endif

            @if($selectedPeriod)
            {{-- Balance & KPI Cards for selected period --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Pemasukan Bulan Ini</p>
                        <p class="mt-1 text-2xl font-extrabold text-emerald-600">
                            Rp {{ number_format($selectedPeriod->total_income, 0, ',', '.') }}
                        </p>
                        <p class="text-xs text-slate-400 mt-1">{{ $selectedPeriod->incomes->count() }} setoran anggota</p>
                    </div>
                    <div class="h-12 w-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl">
                        <i class="bi bi-arrow-down-left-circle-fill"></i>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Pengeluaran Bulan Ini</p>
                        <p class="mt-1 text-2xl font-extrabold text-red-500">
                            Rp {{ number_format($selectedPeriod->total_expense, 0, ',', '.') }}
                        </p>
                        <p class="text-xs text-slate-400 mt-1">{{ $selectedPeriod->expenses->count() }} transaksi</p>
                    </div>
                    <div class="h-12 w-12 rounded-xl bg-red-50 text-red-500 flex items-center justify-center text-2xl">
                        <i class="bi bi-arrow-up-right-circle-fill"></i>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Sisa Saldo Kas</p>
                        <p class="mt-1 text-2xl font-extrabold {{ $selectedPeriod->balance >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                            Rp {{ number_format($selectedPeriod->balance, 0, ',', '.') }}
                        </p>
                        <p class="text-xs text-slate-400 mt-1">Periode {{ $selectedPeriod->period_name }}</p>
                    </div>
                    <div class="h-12 w-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-2xl">
                        <i class="bi bi-wallet2"></i>
                    </div>
                </div>
            </div>

            {{-- Main Tables (Incomes & Expenses) --}}
            <div class="grid lg:grid-cols-2 gap-6 mb-6">
                {{-- Pemasukan List --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                    <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                        <h2 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                            <i class="bi bi-arrow-down-left-circle-fill text-emerald-500 text-base"></i>
                            Daftar Pemasukan (Setoran Anggota)
                        </h2>
                        <span class="text-xs text-slate-500 font-semibold bg-slate-100 px-2 py-0.5 rounded-lg">{{ $selectedPeriod->incomes->count() }} Orang</span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-xs">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="text-left font-semibold text-slate-600 px-4 py-2.5">No</th>
                                    <th class="text-left font-semibold text-slate-600 px-3 py-2.5">Nama</th>
                                    <th class="text-left font-semibold text-slate-600 px-3 py-2.5">Tanggal</th>
                                    <th class="text-right font-semibold text-slate-600 px-4 py-2.5">Nominal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($selectedPeriod->incomes as $idx => $inc)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-2.5 text-slate-400">{{ $idx + 1 }}</td>
                                    <td class="px-3 py-2.5 font-bold text-slate-800">
                                        {{ $inc->member->name ?? '—' }}
                                        @if($inc->note)
                                            <p class="text-[10px] text-slate-400 font-normal mt-0.5">{{ $inc->note }}</p>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2.5 text-slate-500 whitespace-nowrap">{{ $inc->transaction_date->format('d M') }}</td>
                                    <td class="px-4 py-2.5 text-right font-bold text-emerald-600 whitespace-nowrap">
                                        Rp {{ number_format($inc->nominal, 0, ',', '.') }}
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="px-4 py-8 text-center text-slate-400">Belum ada pemasukan yang dicatat.</td></tr>
                                @endforelse
                            </tbody>
                            @if($selectedPeriod->incomes->isNotEmpty())
                            <tfoot class="bg-slate-50 font-bold border-t border-slate-200">
                                <tr>
                                    <td colspan="3" class="px-4 py-2.5 text-right text-slate-700">Total Pemasukan:</td>
                                    <td class="px-4 py-2.5 text-right text-emerald-700 font-extrabold">
                                        Rp {{ number_format($selectedPeriod->total_income, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </div>

                {{-- Pengeluaran List --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                    <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                        <h2 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                            <i class="bi bi-arrow-up-right-circle-fill text-red-500 text-base"></i>
                            Daftar Pengeluaran
                        </h2>
                        <span class="text-xs text-slate-500 font-semibold bg-slate-100 px-2 py-0.5 rounded-lg">{{ $selectedPeriod->expenses->count() }} Item</span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-xs">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="text-left font-semibold text-slate-600 px-4 py-2.5">No</th>
                                    <th class="text-left font-semibold text-slate-600 px-3 py-2.5">Keperluan</th>
                                    <th class="text-left font-semibold text-slate-600 px-3 py-2.5">Tanggal</th>
                                    <th class="text-right font-semibold text-slate-600 px-4 py-2.5">Nominal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($selectedPeriod->expenses as $idx => $exp)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-2.5 text-slate-400">{{ $idx + 1 }}</td>
                                    <td class="px-3 py-2.5 font-bold text-slate-800">
                                        {{ $exp->item_name }}
                                        <div class="flex items-center gap-2 mt-0.5">
                                            <span class="text-[10px] text-slate-600 bg-slate-100 px-1.5 py-0.5 rounded font-medium">{{ $exp->category->name ?? 'Umum' }}</span>
                                            @if($exp->note)
                                                <span class="text-[10px] text-slate-400 truncate max-w-xs font-normal">{{ $exp->note }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-3 py-2.5 text-slate-500 whitespace-nowrap">{{ $exp->transaction_date->format('d M') }}</td>
                                    <td class="px-4 py-2.5 text-right font-bold text-red-600 whitespace-nowrap">
                                        Rp {{ number_format($exp->nominal, 0, ',', '.') }}
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="px-4 py-8 text-center text-slate-400">Belum ada pengeluaran pada bulan ini.</td></tr>
                                @endforelse
                            </tbody>
                            @if($selectedPeriod->expenses->isNotEmpty())
                            <tfoot class="bg-slate-50 font-bold border-t border-slate-200">
                                <tr>
                                    <td colspan="3" class="px-4 py-2.5 text-right text-slate-700">Total Pengeluaran:</td>
                                    <td class="px-4 py-2.5 text-right text-red-700 font-extrabold">
                                        Rp {{ number_format($selectedPeriod->total_expense, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>

            {{-- Bukti & Dokumentasi Galeri --}}
            @php
                $attachments = $selectedPeriod->expenses->flatMap->attachments;
            @endphp
            @if($attachments->isNotEmpty())
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 mb-6">
                <h2 class="text-sm font-bold text-slate-800 mb-4 flex items-center gap-2">
                    <i class="bi bi-images text-blue-600"></i> Dokumentasi & Foto Bukti Nota Pengeluaran
                </h2>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                    @foreach($attachments as $att)
                    <div class="group relative rounded-xl border border-slate-200 overflow-hidden bg-slate-50 aspect-square cursor-pointer shadow-2xs"
                        @click="modalPhoto = '{{ asset('storage/' . $att->file_path) }}'">
                        <img src="{{ asset('storage/' . $att->file_path) }}" alt="Bukti" class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-200">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-2 text-[11px] text-white">
                            <span class="truncate font-medium">{{ $att->expense->item_name ?? 'Bukti Nota' }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            @else
            {{-- No closed periods state --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-12 text-center">
                <div class="text-slate-300 text-4xl mb-3"><i class="bi bi-folder2-open"></i></div>
                <h2 class="text-lg font-bold text-slate-800">Belum Ada Laporan Kas yang Dipublikasikan</h2>
                <p class="text-sm text-slate-500 mt-1 max-w-md mx-auto">
                    Laporan kas bulanan untuk grup <strong>{{ $group->name }}</strong> belum ada yang berstatus selesai/tutup buku (*closed*).
                </p>
            </div>
            @endif
        </div>

        {{-- Public Footer --}}
        <footer class="mt-8 text-center text-xs text-slate-400 pb-6 flex flex-col sm:flex-row items-center justify-between gap-2">
            <span>Dikelola dengan sistem transparansi kas &bull; <a href="{{ route('login') }}" class="text-blue-600 font-semibold hover:underline">Login Pengurus</a></span>
            <span class="text-slate-400 flex items-center gap-1"><i class="bi bi-shield-check text-emerald-600"></i> Terverifikasi Sistem SDA Kas</span>
        </footer>

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

    {{-- Chart.js CDN & Alpine.js CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        function rupiahFormat(num) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(num);
        }

        function publicKasApp() {
            return {
                viewMode: '{{ ($year && $month) ? 'bulanan' : 'rekap' }}',
                modalPhoto: null,
                rows: @json($arrears),
                onlyArrears: false,
                searchQuery: '',
                sortBy: 'total_shortage',

                init() {
                    this.rows = this.rows.map(r => ({ ...r, _expanded: false }));
                    this.$nextTick(() => {
                        this.initChart();
                    });
                },

                get filteredRows() {
                    let list = this.rows.filter(r => {
                        if (this.onlyArrears && r.total_shortage <= 0) return false;
                        if (this.searchQuery.trim() !== '') {
                            const q = this.searchQuery.toLowerCase();
                            return r.member_name.toLowerCase().includes(q);
                        }
                        return true;
                    });

                    const key = this.sortBy;
                    return list.sort((a, b) => {
                        if (key === 'member_name') return a.member_name.localeCompare(b.member_name);
                        return b[key] - a[key];
                    });
                },

                initChart() {
                    const canvas = document.getElementById('publicTrendChart');
                    if (!canvas) return;

                    const ctx = canvas.getContext('2d');
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
                }
            };
        }
    </script>
</body>
</html>
