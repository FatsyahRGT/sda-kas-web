@extends('layouts.app')

@section('title', 'Dashboard')
@section('header', 'Dashboard')

@section('header-actions')
    <div class="flex items-center gap-2">
        <label for="group-selector" class="text-xs font-semibold text-slate-500 hidden sm:inline"><i class="bi bi-funnel"></i> Grup:</label>
        <select id="group-selector" onchange="changeGroup(this.value)"
            class="rounded-xl border border-slate-300 bg-white px-3 py-1.5 text-xs sm:text-sm font-semibold text-slate-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 shadow-xs">
            <option value="">Semua Grup</option>
            @foreach($groups as $g)
                <option value="{{ $g->id }}" {{ $selectedGroupId == $g->id ? 'selected' : '' }}>{{ $g->name }}</option>
            @endforeach
        </select>
    </div>
@endsection

@section('content')
<div x-data="arrearsPanel()" x-init="init()">
    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-5 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Pemasukan</p>
                <p class="mt-1 text-lg sm:text-2xl font-extrabold text-emerald-600">{{ 'Rp ' . number_format($totalIncome, 0, ',', '.') }}</p>
                <p class="text-[11px] text-slate-400 mt-0.5">Akumulasi seluruh kas masuk</p>
            </div>
            <div class="h-11 w-11 sm:h-12 sm:w-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl sm:text-2xl shadow-inner">
                <i class="bi bi-arrow-down-left-circle-fill"></i>
            </div>
        </div>
        
        <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-5 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Pengeluaran</p>
                <p class="mt-1 text-lg sm:text-2xl font-extrabold text-red-500">{{ 'Rp ' . number_format($totalExpense, 0, ',', '.') }}</p>
                <p class="text-[11px] text-slate-400 mt-0.5">Pengeluaran terverifikasi</p>
            </div>
            <div class="h-11 w-11 sm:h-12 sm:w-12 rounded-2xl bg-red-50 text-red-500 flex items-center justify-center text-xl sm:text-2xl shadow-inner">
                <i class="bi bi-arrow-up-right-circle-fill"></i>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-5 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Sisa Saldo Kas</p>
                <p class="mt-1 text-lg sm:text-2xl font-extrabold {{ $totalBalance >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                    {{ 'Rp ' . number_format($totalBalance, 0, ',', '.') }}
                </p>
                <p class="text-[11px] text-slate-400 mt-0.5">Kas bersih saat ini</p>
            </div>
            <div class="h-11 w-11 sm:h-12 sm:w-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl sm:text-2xl shadow-inner">
                <i class="bi bi-wallet2"></i>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-5 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Anggota Aktif</p>
                <p class="mt-1 text-lg sm:text-2xl font-extrabold text-slate-800">{{ $totalMembers }}</p>
                <p class="text-[11px] text-slate-400 mt-0.5">Terdaftar wajib iuran</p>
            </div>
            <div class="h-11 w-11 sm:h-12 sm:w-12 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center text-xl sm:text-2xl shadow-inner">
                <i class="bi bi-people-fill"></i>
            </div>
        </div>
    </div>

    {{-- Charts Grid (Tren Kas + Alokasi Kategori + Tingkat Kepatuhan) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        {{-- Trend Chart (2 Cols) --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-xs p-5 flex flex-col justify-between">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
                <div>
                    <h2 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                        <i class="bi bi-bar-chart-fill text-blue-600"></i> Tren Pemasukan vs Pengeluaran Bulanan
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">Perbandingan arus kas masuk dan kas keluar per periode</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-lg">
                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span> Pemasukan
                    </span>
                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-lg">
                        <span class="h-2 w-2 rounded-full bg-red-500"></span> Pengeluaran
                    </span>
                </div>
            </div>
            <div class="relative h-64">
                @if(count($chartLabels) > 0)
                    <canvas id="trendChart"></canvas>
                @else
                    <div class="h-full flex items-center justify-center text-xs text-slate-400">
                        Belum ada riwayat transaksi periode kas
                    </div>
                @endif
            </div>
        </div>

        {{-- Expense Category Breakdown (1 Col) --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-5 flex flex-col justify-between">
            <div class="mb-3">
                <h2 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                    <i class="bi bi-pie-chart-fill text-indigo-600"></i> Komposisi Pengeluaran
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Alokasi anggaran berdasarkan kategori</p>
            </div>
            <div class="relative h-56 flex items-center justify-center">
                @if(count($categoryLabels) > 0 && array_sum($categoryData) > 0)
                    <canvas id="categoryChart"></canvas>
                @else
                    <div class="text-center text-xs text-slate-400 p-4">
                        <i class="bi bi-tag text-2xl text-slate-300 mb-1 block"></i>
                        Belum ada data pengeluaran berkategori
                    </div>
                @endif
            </div>
            <div class="mt-3 pt-3 border-t border-slate-100 text-center">
                <span class="text-[11px] text-slate-400 font-medium">Total: Rp {{ number_format($totalExpense, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    {{-- Compliance Stats Bar --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
            <div>
                <h2 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                    <i class="bi bi-shield-check text-emerald-600"></i> Rasio Kepatuhan Iuran Kas Anggota
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Proporsi anggota yang tertib lunas vs menunggak iuran</p>
            </div>
            <div class="flex items-center gap-2 text-xs">
                <span class="inline-flex items-center gap-1.5 font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200">
                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span> {{ $complianceData[0] ?? 0 }} Lunas
                </span>
                <span class="inline-flex items-center gap-1.5 font-bold text-amber-700 bg-amber-50 px-2.5 py-1 rounded-lg border border-amber-200">
                    <span class="h-2 w-2 rounded-full bg-amber-500"></span> {{ $complianceData[1] ?? 0 }} Nunggak 1-2 Bln
                </span>
                <span class="inline-flex items-center gap-1.5 font-bold text-red-700 bg-red-50 px-2.5 py-1 rounded-lg border border-red-200">
                    <span class="h-2 w-2 rounded-full bg-red-500"></span> {{ $complianceData[2] ?? 0 }} Nunggak >2 Bln
                </span>
            </div>
        </div>

        @php
            $totalMembersCount = array_sum($complianceData) ?: 1;
            $paidPct = round((($complianceData[0] ?? 0) / $totalMembersCount) * 100, 1);
            $lightPct = round((($complianceData[1] ?? 0) / $totalMembersCount) * 100, 1);
            $heavyPct = round((($complianceData[2] ?? 0) / $totalMembersCount) * 100, 1);
        @endphp
        <div class="h-3 w-full bg-slate-100 rounded-full overflow-hidden flex shadow-inner">
            <div style="width: {{ $paidPct }}%" class="bg-emerald-500 h-full transition-all duration-500" title="Lunas: {{ $paidPct }}%"></div>
            <div style="width: {{ $lightPct }}%" class="bg-amber-400 h-full transition-all duration-500" title="Nunggak 1-2 Bulan: {{ $lightPct }}%"></div>
            <div style="width: {{ $heavyPct }}%" class="bg-red-500 h-full transition-all duration-500" title="Nunggak >2 Bulan: {{ $heavyPct }}%"></div>
        </div>
        <div class="flex items-center justify-between text-[11px] text-slate-400 font-medium mt-1.5">
            <span>Tingkat Kepatuhan Organisasi: <strong class="text-slate-700">{{ $paidPct }}% Lunas</strong></span>
            <span>Total: {{ $totalMembers }} Anggota</span>
        </div>
    </div>

    {{-- Arrears Panel (Monitoring Tunggakan & Pembayaran) --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 p-5 border-b border-slate-100">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                        <i class="bi bi-exclamation-triangle-fill text-amber-500"></i> Monitoring Status Iuran & Tunggakan Anggota
                    </h2>
                    <span class="bg-blue-50 text-blue-700 text-xs font-bold px-2 py-0.5 rounded-full border border-blue-200"
                        x-text="filteredRows.length + ' Anggota'"></span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">Pantau kepatuhan pembayaran, kirim pengingat WhatsApp, dan lihat histori lengkap</p>
            </div>

            <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                {{-- Live Search --}}
                <div class="relative">
                    <i class="bi bi-search absolute left-3 top-2 text-slate-400 text-xs"></i>
                    <input type="text" x-model="searchQuery" placeholder="Cari nama anggota..."
                        class="rounded-xl border border-slate-300 bg-white pl-8 pr-3 py-1.5 text-xs font-medium text-slate-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 shadow-2xs">
                </div>

                {{-- Only Arrears Checkbox --}}
                <label class="flex items-center gap-1.5 text-xs text-slate-600 cursor-pointer select-none bg-slate-50 px-2.5 py-1.5 rounded-xl border border-slate-200">
                    <input type="checkbox" x-model="onlyArrears" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500/20">
                    <span>Hanya yang nunggak</span>
                </label>

                {{-- Sort Dropdown --}}
                <select x-model="sortBy" class="text-xs border border-slate-300 rounded-xl px-2.5 py-1.5 bg-white text-slate-700 font-semibold shadow-2xs">
                    <option value="total_shortage">Terbesar nunggak</option>
                    <option value="payment_percentage">Persentase bayar terendah</option>
                    <option value="total_paid">Terkecil bayar</option>
                    <option value="member_name">Nama A-Z</option>
                    <option value="unpaid_months_count">Bulan nunggak</option>
                </select>
            </div>
        </div>

        {{-- Loading state --}}
        <div x-show="loading" class="p-10 text-center text-sm text-slate-400">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mb-2"></div>
            <p>Memuat data anggota...</p>
        </div>

        <div x-show="!loading">
            {{-- Empty state --}}
            <div x-show="filteredRows.length === 0" class="py-12 text-center text-sm text-slate-400">
                <div class="text-emerald-500 text-3xl mb-2"><i class="bi bi-check-circle-fill"></i></div>
                <p class="font-bold text-slate-700">Semua Terpenuhi / Data Tidak Ditemukan</p>
                <p class="text-xs text-slate-400 mt-0.5">Tidak ada anggota yang memenuhi kriteria pencarian/filter.</p>
            </div>

            {{-- Arrears table --}}
            <div x-show="filteredRows.length > 0" class="overflow-x-auto">
                <table class="w-full text-xs sm:text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="text-left font-semibold text-slate-600 px-5 py-3 text-xs uppercase tracking-wider">Anggota</th>
                            <th class="text-left font-semibold text-slate-600 px-3 py-3 text-xs uppercase tracking-wider hidden sm:table-cell">Kepatuhan</th>
                            <th class="text-right font-semibold text-slate-600 px-3 py-3 text-xs uppercase tracking-wider">Kewajiban</th>
                            <th class="text-right font-semibold text-slate-600 px-3 py-3 text-xs uppercase tracking-wider">Dibayar</th>
                            <th class="text-right font-semibold text-slate-600 px-3 py-3 text-xs uppercase tracking-wider">Tunggakan</th>
                            <th class="text-center font-semibold text-slate-600 px-3 py-3 text-xs uppercase tracking-wider">Status</th>
                            <th class="text-center font-semibold text-slate-600 px-4 py-3 text-xs uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <template x-for="row in filteredRows" :key="row.member_id">
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="h-9 w-9 rounded-xl flex items-center justify-center font-bold text-xs shrink-0"
                                            :class="row.total_shortage > 0 ? 'bg-red-50 text-red-600 border border-red-200' : 'bg-emerald-50 text-emerald-600 border border-emerald-200'"
                                            x-text="getInitials(row.member_name)">
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-800" x-text="row.member_name"></p>
                                            <p class="text-xs text-slate-400 flex items-center gap-1 mt-0.5">
                                                <i class="bi bi-telephone text-[10px]"></i>
                                                <span x-text="row.member_phone || 'Tidak ada no. HP'"></span>
                                                <span class="text-slate-300 sm:hidden">&bull;</span>
                                                <span class="sm:hidden text-slate-500" x-text="row.group_name"></span>
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-3 hidden sm:table-cell min-w-[130px]">
                                    <div class="flex items-center justify-between text-[11px] font-semibold mb-1">
                                        <span :class="row.payment_percentage >= 100 ? 'text-emerald-600' : (row.payment_percentage >= 50 ? 'text-amber-600' : 'text-red-600')"
                                            x-text="row.payment_percentage + '%'"></span>
                                        <span class="text-slate-400" x-text="row.unpaid_months_count === 0 ? 'Lengkap' : row.unpaid_months_count + ' bln nunggak'"></span>
                                    </div>
                                    <div class="h-2 w-full bg-slate-100 rounded-full overflow-hidden">
                                        <div class="h-full rounded-full transition-all duration-300"
                                            :class="row.payment_percentage >= 100 ? 'bg-emerald-500' : (row.payment_percentage >= 50 ? 'bg-amber-500' : 'bg-red-500')"
                                            :style="'width: ' + row.payment_percentage + '%'"></div>
                                    </div>
                                </td>
                                <td class="px-3 py-3 text-right text-slate-600 font-medium" x-text="rupiahFormat(row.total_expected)"></td>
                                <td class="px-3 py-3 text-right text-emerald-600 font-semibold" x-text="rupiahFormat(row.total_paid)"></td>
                                <td class="px-3 py-3 text-right">
                                    <span :class="row.total_shortage > 0 ? 'text-red-600 font-bold' : 'text-emerald-600 font-semibold'"
                                        x-text="row.total_shortage > 0 ? '- ' + rupiahFormat(row.total_shortage) : 'Rp 0'"></span>
                                </td>
                                <td class="px-3 py-3 text-center">
                                    <span x-show="row.unpaid_months_count > 0"
                                        class="inline-flex items-center gap-1 rounded-full bg-red-100 text-red-700 px-2.5 py-0.5 text-xs font-semibold"
                                        x-text="row.unpaid_months_count + ' Bulan'"></span>
                                    <span x-show="row.unpaid_months_count === 0"
                                        class="inline-flex items-center gap-1 rounded-full bg-emerald-100 text-emerald-700 px-2.5 py-0.5 text-xs font-semibold">
                                        <i class="bi bi-check-circle-fill text-[10px]"></i> Lunas
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        {{-- Open Rich Detail Modal --}}
                                        <button type="button" @click="openMemberDetail(row)"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold text-blue-600 bg-blue-50 hover:bg-blue-100 transition-colors shadow-2xs">
                                            <i class="bi bi-person-lines-fill"></i>
                                            <span>Detail</span>
                                        </button>

                                        {{-- WhatsApp Direct Reminder --}}
                                        <template x-if="row.member_phone && row.total_shortage > 0">
                                            <a :href="generateWaLink(row)" target="_blank" title="Kirim Pengingat WhatsApp"
                                                class="inline-flex items-center justify-center h-7 w-7 rounded-lg text-xs font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 transition-colors">
                                                <i class="bi bi-whatsapp"></i>
                                            </a>
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
    {{-- RICH MEMBER DETAIL MODAL (Alpine.js Modal & JS Features) --}}
    {{-- ========================================================================= --}}
    <div x-show="activeModalMember" x-transition.opacity
        class="fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4"
        style="display: none;" @keydown.escape.window="activeModalMember = null">
        
        <div class="relative w-full max-w-2xl bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden flex flex-col max-h-[90vh]" @click.stop>
            {{-- Modal Header --}}
            <div class="p-5 sm:p-6 border-b border-slate-100 bg-slate-50/60 flex items-start justify-between gap-4">
                <div class="flex items-center gap-3.5">
                    <div class="h-12 w-12 rounded-2xl flex items-center justify-center font-extrabold text-base shadow-inner"
                        :class="activeModalMember?.total_shortage > 0 ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-700'"
                        x-text="activeModalMember ? getInitials(activeModalMember.member_name) : ''">
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-base sm:text-lg font-extrabold text-slate-900" x-text="activeModalMember?.member_name"></h3>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold"
                                :class="activeModalMember?.total_shortage > 0 ? 'bg-red-50 text-red-600 border border-red-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200'"
                                x-text="activeModalMember?.total_shortage > 0 ? activeModalMember.unpaid_months_count + ' Bulan Nunggak' : 'Tertib / Lunas'">
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5 flex flex-wrap items-center gap-2">
                            <span class="font-semibold text-slate-700" x-text="activeModalMember?.group_name"></span>
                            <span>&bull;</span>
                            <span class="flex items-center gap-1">
                                <i class="bi bi-telephone text-slate-400"></i>
                                <span x-text="activeModalMember?.member_phone || 'No. HP belum diisi'"></span>
                            </span>
                        </p>
                    </div>
                </div>

                <button @click="activeModalMember = null" class="h-8 w-8 rounded-full bg-slate-200/70 hover:bg-slate-300 text-slate-600 flex items-center justify-center transition-colors">
                    <i class="bi bi-x-lg text-xs"></i>
                </button>
            </div>

            {{-- Modal Body --}}
            <div class="p-5 sm:p-6 overflow-y-auto flex-1 space-y-5">
                {{-- Progress Bar Kepatuhan --}}
                <div class="bg-slate-50 rounded-xl p-4 border border-slate-200">
                    <div class="flex items-center justify-between text-xs font-bold mb-1.5">
                        <span class="text-slate-600">Tingkat Pelunasan Iuran Kas</span>
                        <span :class="activeModalMember?.payment_percentage >= 100 ? 'text-emerald-600' : (activeModalMember?.payment_percentage >= 50 ? 'text-amber-600' : 'text-red-600')"
                            x-text="activeModalMember?.payment_percentage + '%'"></span>
                    </div>
                    <div class="h-3 w-full bg-slate-200 rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-500"
                            :class="activeModalMember?.payment_percentage >= 100 ? 'bg-emerald-500' : (activeModalMember?.payment_percentage >= 50 ? 'bg-amber-500' : 'bg-red-500')"
                            :style="'width: ' + (activeModalMember?.payment_percentage || 0) + '%'"></div>
                    </div>
                </div>

                {{-- 3 KPI Finansial Anggota --}}
                <div class="grid grid-cols-3 gap-3">
                    <div class="bg-white rounded-xl border border-slate-200 p-3 text-center shadow-2xs">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Kewajiban</p>
                        <p class="text-xs sm:text-sm font-extrabold text-slate-800 mt-1" x-text="rupiahFormat(activeModalMember?.total_expected || 0)"></p>
                    </div>
                    <div class="bg-white rounded-xl border border-slate-200 p-3 text-center shadow-2xs">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Sudah Dibayar</p>
                        <p class="text-xs sm:text-sm font-extrabold text-emerald-600 mt-1" x-text="rupiahFormat(activeModalMember?.total_paid || 0)"></p>
                    </div>
                    <div class="bg-white rounded-xl border border-slate-200 p-3 text-center shadow-2xs">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Sisa Tunggakan</p>
                        <p class="text-xs sm:text-sm font-extrabold mt-1"
                            :class="activeModalMember?.total_shortage > 0 ? 'text-red-600' : 'text-emerald-600'"
                            x-text="activeModalMember?.total_shortage > 0 ? '- ' + rupiahFormat(activeModalMember.total_shortage) : 'Rp 0'"></p>
                    </div>
                </div>

                {{-- Tab Filter Rincian Periode --}}
                <div>
                    <div class="flex items-center justify-between border-b border-slate-200 pb-2 mb-3">
                        <h4 class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                            <i class="bi bi-clock-history text-blue-600"></i> Rincian Histori Periode Kas
                        </h4>
                        <div class="flex items-center gap-1 bg-slate-100 p-0.5 rounded-lg text-xs">
                            <button type="button" @click="modalPeriodFilter = 'all'"
                                :class="modalPeriodFilter === 'all' ? 'bg-white font-bold text-slate-800 shadow-2xs' : 'text-slate-500 font-medium'"
                                class="px-2 py-0.5 rounded-md transition-colors">
                                Semua (<span x-text="activeModalMember?.all_periods?.length || 0"></span>)
                            </button>
                            <button type="button" @click="modalPeriodFilter = 'unpaid'"
                                :class="modalPeriodFilter === 'unpaid' ? 'bg-white font-bold text-red-600 shadow-2xs' : 'text-slate-500 font-medium'"
                                class="px-2 py-0.5 rounded-md transition-colors">
                                Nunggak (<span x-text="activeModalMember?.unpaid_periods?.length || 0"></span>)
                            </button>
                        </div>
                    </div>

                    {{-- Period Cards List --}}
                    <div class="grid sm:grid-cols-2 gap-2.5">
                        <template x-for="p in getModalFilteredPeriods()" :key="p.period_id">
                            <div class="rounded-xl border p-3 text-xs shadow-2xs transition-colors"
                                :class="p.is_paid ? 'bg-emerald-50/30 border-emerald-200' : (p.paid_amount > 0 ? 'bg-amber-50/40 border-amber-200' : 'bg-red-50/30 border-red-200')">
                                <div class="flex items-center justify-between mb-1.5">
                                    <span class="font-bold text-slate-800" x-text="p.period_name"></span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold"
                                        :class="p.is_paid ? 'bg-emerald-100 text-emerald-700' : (p.paid_amount > 0 ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700')"
                                        x-text="p.is_paid ? '✓ Lunas' : (p.paid_amount > 0 ? 'Kurang ' + rupiahFormat(p.shortage) : '✕ Belum Bayar')"></span>
                                </div>
                                <div class="space-y-1 text-slate-600 text-[11px]">
                                    <div class="flex justify-between">
                                        <span class="text-slate-400">Kewajiban:</span>
                                        <span class="font-semibold" x-text="rupiahFormat(p.due_amount)"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-slate-400">Dibayar:</span>
                                        <span class="font-semibold text-emerald-600" x-text="rupiahFormat(p.paid_amount)"></span>
                                    </div>
                                    <div x-show="p.shortage > 0" class="flex justify-between border-t border-slate-200/60 pt-1 font-bold text-red-600">
                                        <span>Kekurangan:</span>
                                        <span x-text="'- ' + rupiahFormat(p.shortage)"></span>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div x-show="getModalFilteredPeriods().length === 0" class="py-6 text-center text-xs text-slate-400 bg-slate-50 rounded-xl border border-slate-200">
                        Tidak ada periode yang sesuai dengan filter.
                    </div>
                </div>
            </div>

            {{-- Modal Footer with JS Actions --}}
            <div class="p-4 sm:p-5 border-t border-slate-100 bg-slate-50 flex flex-wrap items-center justify-between gap-2">
                <button type="button" @click="copyBillingText(activeModalMember)"
                    class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-white border border-slate-200 hover:bg-slate-100 transition-colors shadow-2xs">
                    <i class="bi bi-clipboard-check"></i>
                    <span>Salin Rincian Tagihan</span>
                </button>

                <div class="flex items-center gap-2">
                    <template x-if="activeModalMember?.member_phone && activeModalMember.total_shortage > 0">
                        <a :href="generateWaLink(activeModalMember)" target="_blank"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 transition-colors shadow-sm">
                            <i class="bi bi-whatsapp"></i>
                            <span>Ingatkan via WA</span>
                        </a>
                    </template>
                    <a :href="'{{ route('incomes.create') }}?member_id=' + (activeModalMember?.member_id || '')"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 transition-colors shadow-sm">
                        <i class="bi bi-plus-circle-fill"></i>
                        <span>Catat Setoran</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
// 1. Monthly Trend Bar Chart
@if(count($chartLabels) > 0)
const trendCanvas = document.getElementById('trendChart');
if (trendCanvas) {
    const ctx = trendCanvas.getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($chartLabels),
            datasets: [
                {
                    label: 'Pemasukan',
                    data: @json($chartIncome),
                    backgroundColor: 'rgba(16, 185, 129, 0.85)',
                    borderRadius: 8,
                },
                {
                    label: 'Pengeluaran',
                    data: @json($chartExpense),
                    backgroundColor: 'rgba(239, 68, 68, 0.85)',
                    borderRadius: 8,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: (ctx) => ' ' + ctx.dataset.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(ctx.raw)
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
@endif

// 2. Expense Category Doughnut Chart
@if(count($categoryLabels) > 0 && array_sum($categoryData) > 0)
const catCanvas = document.getElementById('categoryChart');
if (catCanvas) {
    const catCtx = catCanvas.getContext('2d');
    new Chart(catCtx, {
        type: 'doughnut',
        data: {
            labels: @json($categoryLabels),
            datasets: [{
                data: @json($categoryData),
                backgroundColor: [
                    '#3b82f6', '#10b981', '#f59e0b', '#ec4899', '#8b5cf6',
                    '#06b6d4', '#f97316', '#64748b', '#14b8a6', '#6366f1'
                ],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 10,
                        padding: 10,
                        font: { size: 10 }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: (ctx) => ' ' + ctx.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(ctx.raw)
                    }
                }
            },
            cutout: '68%'
        }
    });
}
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
        searchQuery: '',
        sortBy: 'total_shortage',
        activeModalMember: null,
        modalPeriodFilter: 'all',

        init() {
            this.rows = this.rows.map(r => ({ ...r, _expanded: false }));
        },

        get filteredRows() {
            let list = this.rows.filter(r => {
                if (this.onlyArrears && r.total_shortage <= 0) return false;
                if (this.searchQuery.trim() !== '') {
                    const q = this.searchQuery.toLowerCase();
                    return r.member_name.toLowerCase().includes(q) || (r.member_phone && r.member_phone.includes(q));
                }
                return true;
            });

            const key = this.sortBy;
            return list.sort((a, b) => {
                if (key === 'member_name') return a.member_name.localeCompare(b.member_name);
                if (key === 'payment_percentage') return a.payment_percentage - b.payment_percentage;
                return b[key] - a[key];
            });
        },

        openMemberDetail(member) {
            this.activeModalMember = member;
            this.modalPeriodFilter = member.total_shortage > 0 ? 'unpaid' : 'all';
        },

        getModalFilteredPeriods() {
            if (!this.activeModalMember) return [];
            if (this.modalPeriodFilter === 'unpaid') {
                return this.activeModalMember.unpaid_periods || [];
            }
            return this.activeModalMember.all_periods || [];
        },

        getInitials(name) {
            if (!name) return '—';
            const parts = name.trim().split(' ');
            if (parts.length === 1) return parts[0].substring(0, 2).toUpperCase();
            return (parts[0][0] + parts[1][0]).toUpperCase();
        },

        generateWaLink(member) {
            if (!member || !member.member_phone) return '#';
            let phone = member.member_phone.replace(/\D/g, '');
            if (phone.startsWith('0')) {
                phone = '62' + phone.substring(1);
            }

            let msg = `Halo ${member.member_name}, kami dari pengurus *${member.group_name}* ingin menginformasikan status iuran kas Anda:\n\n`;
            msg += `📌 *Total Tunggakan:* Rp ${new Intl.NumberFormat('id-ID').format(member.total_shortage)}\n`;
            msg += `📌 *Jumlah Bulan:* ${member.unpaid_months_count} bulan\n\n`;
            msg += `*Rincian Bulan:*\n`;

            (member.unpaid_periods || []).forEach(p => {
                msg += `- ${p.period_name}: Kurang Rp ${new Intl.NumberFormat('id-ID').format(p.shortage)}\n`;
            });

            msg += `\nMohon untuk segera melakukan konfirmasi atau pembayaran kepada pengurus. Terima kasih banyak 🙏`;

            return `https://wa.me/${phone}?text=${encodeURIComponent(msg)}`;
        },

        copyBillingText(member) {
            if (!member) return;
            let text = `Informasi Iuran Kas - ${member.group_name}\n`;
            text += `Nama: ${member.member_name}\n`;
            text += `Total Kewajiban: Rp ${new Intl.NumberFormat('id-ID').format(member.total_expected)}\n`;
            text += `Sudah Dibayar: Rp ${new Intl.NumberFormat('id-ID').format(member.total_paid)}\n`;
            text += `Sisa Tunggakan: Rp ${new Intl.NumberFormat('id-ID').format(member.total_shortage)}\n\n`;

            if (member.unpaid_periods && member.unpaid_periods.length > 0) {
                text += `Rincian Tunggakan:\n`;
                member.unpaid_periods.forEach(p => {
                    text += `- ${p.period_name}: Kurang Rp ${new Intl.NumberFormat('id-ID').format(p.shortage)}\n`;
                });
            } else {
                text += `Status: LUNAS SEMUA ✓\n`;
            }

            window.copyToClipboard(text, 'Rincian tagihan disalin ke clipboard!');
        }
    };
}
</script>
@endpush
