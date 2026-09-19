<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Kas: {{ $group->name }} — SDA Kas</title>
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#2563eb">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full font-sans antialiased text-slate-800 p-4 sm:p-6 md:p-8" x-data="{ modalPhoto: null }">
    <div class="max-w-5xl mx-auto">
        {{-- Public Header --}}
        <header class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-600 text-white font-bold text-2xl shadow-md">
                        ₽
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h1 class="text-xl sm:text-2xl font-bold text-slate-900">{{ $group->name }}</h1>
                            <span class="inline-flex items-center rounded-full bg-emerald-50 border border-emerald-200 px-2.5 py-0.5 text-xs font-semibold text-emerald-700">
                                Transparan & Publik
                            </span>
                        </div>
                        @if($group->description)
                            <p class="text-xs sm:text-sm text-slate-500 mt-1">{{ $group->description }}</p>
                        @endif
                    </div>
                </div>

                {{-- Period Selector (Only closed periods) --}}
                @if($closedPeriods->isNotEmpty())
                <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 rounded-xl p-2">
                    <label for="period-select" class="text-xs font-medium text-slate-600 whitespace-nowrap">Pilih Bulan:</label>
                    <select id="period-select" onchange="window.location.href = this.value"
                        class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs sm:text-sm font-semibold text-slate-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                        @foreach($closedPeriods as $cp)
                            <option value="{{ route('public.group.period', ['slug' => $group->slug, 'year' => $cp->year, 'month' => $cp->month]) }}"
                                {{ $selectedPeriod && $selectedPeriod->id === $cp->id ? 'selected' : '' }}>
                                {{ $cp->period_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif
            </div>
        </header>

        @if($selectedPeriod)
        {{-- Balance & KPI Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Pemasukan Bulan Ini</p>
                <p class="mt-1 text-2xl font-bold text-emerald-600">
                    Rp {{ number_format($selectedPeriod->total_income, 0, ',', '.') }}
                </p>
                <p class="text-xs text-slate-400 mt-1">{{ $selectedPeriod->incomes->count() }} setoran anggota</p>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Pengeluaran Bulan Ini</p>
                <p class="mt-1 text-2xl font-bold text-red-500">
                    Rp {{ number_format($selectedPeriod->total_expense, 0, ',', '.') }}
                </p>
                <p class="text-xs text-slate-400 mt-1">{{ $selectedPeriod->expenses->count() }} transaksi pengeluaran</p>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Sisa Saldo Kas</p>
                <p class="mt-1 text-2xl font-bold {{ $selectedPeriod->balance >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                    Rp {{ number_format($selectedPeriod->balance, 0, ',', '.') }}
                </p>
                <p class="text-xs text-slate-400 mt-1">Periode {{ $selectedPeriod->period_name }}</p>
            </div>
        </div>

        {{-- Main Tables (Incomes & Expenses) --}}
        <div class="grid lg:grid-cols-2 gap-6 mb-6">
            {{-- Pemasukan List --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-slate-800 flex items-center gap-2">
                        <span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span>
                        Daftar Pemasukan (Setoran Anggota)
                    </h2>
                    <span class="text-xs text-slate-400 font-mono">{{ $selectedPeriod->incomes->count() }} Orang</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="text-left font-medium text-slate-500 px-4 py-2.5">No</th>
                                <th class="text-left font-medium text-slate-500 px-3 py-2.5">Nama</th>
                                <th class="text-left font-medium text-slate-500 px-3 py-2.5">Tanggal</th>
                                <th class="text-right font-medium text-slate-500 px-4 py-2.5">Nominal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($selectedPeriod->incomes as $idx => $inc)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-2.5 text-slate-400">{{ $idx + 1 }}</td>
                                <td class="px-3 py-2.5 font-medium text-slate-800">
                                    {{ $inc->member->name ?? '—' }}
                                    @if($inc->note)
                                        <p class="text-[10px] text-slate-400">{{ $inc->note }}</p>
                                    @endif
                                </td>
                                <td class="px-3 py-2.5 text-slate-500 whitespace-nowrap">{{ $inc->transaction_date->format('d M') }}</td>
                                <td class="px-4 py-2.5 text-right font-semibold text-emerald-600 whitespace-nowrap">
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
                                <td colspan="3" class="px-4 py-2.5 text-right text-slate-700">Total:</td>
                                <td class="px-4 py-2.5 text-right text-emerald-700">
                                    Rp {{ number_format($selectedPeriod->total_income, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>

            {{-- Pengeluaran List --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-slate-800 flex items-center gap-2">
                        <span class="h-2.5 w-2.5 rounded-full bg-red-500"></span>
                        Daftar Pengeluaran
                    </h2>
                    <span class="text-xs text-slate-400 font-mono">{{ $selectedPeriod->expenses->count() }} Item</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="text-left font-medium text-slate-500 px-4 py-2.5">No</th>
                                <th class="text-left font-medium text-slate-500 px-3 py-2.5">Keperluan</th>
                                <th class="text-left font-medium text-slate-500 px-3 py-2.5">Tanggal</th>
                                <th class="text-right font-medium text-slate-500 px-4 py-2.5">Nominal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($selectedPeriod->expenses as $idx => $exp)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-2.5 text-slate-400">{{ $idx + 1 }}</td>
                                <td class="px-3 py-2.5 font-medium text-slate-800">
                                    {{ $exp->item_name }}
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <span class="text-[10px] text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded">{{ $exp->category->name ?? 'Umum' }}</span>
                                        @if($exp->note)
                                            <span class="text-[10px] text-slate-400 truncate max-w-xs">{{ $exp->note }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-3 py-2.5 text-slate-500 whitespace-nowrap">{{ $exp->transaction_date->format('d M') }}</td>
                                <td class="px-4 py-2.5 text-right font-semibold text-red-600 whitespace-nowrap">
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
                                <td colspan="3" class="px-4 py-2.5 text-right text-slate-700">Total:</td>
                                <td class="px-4 py-2.5 text-right text-red-700">
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
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 mb-6">
            <h2 class="text-sm font-semibold text-slate-800 mb-4 flex items-center gap-2">
                📸 Dokumentasi & Foto Bukti Nota Pengeluaran
            </h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                @foreach($attachments as $att)
                <div class="group relative rounded-xl border border-slate-200 overflow-hidden bg-slate-50 aspect-square cursor-pointer"
                    @click="modalPhoto = '{{ asset('storage/' . $att->file_path) }}'">
                    <img src="{{ asset('storage/' . $att->file_path) }}" alt="Bukti" class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-200">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-2 text-[11px] text-white">
                        <span class="truncate font-medium">{{ $att->expense->item_name ?? 'Bukti Nota' }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        @else
        {{-- No closed periods state --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-12 text-center">
            <div class="text-4xl mb-3">📂</div>
            <h2 class="text-lg font-bold text-slate-800">Belum Ada Laporan Kas yang Dipublikasikan</h2>
            <p class="text-sm text-slate-500 mt-1 max-w-md mx-auto">
                Laporan kas bulanan untuk grup <strong>{{ $group->name }}</strong> belum ada yang berstatus selesai/tutup buku (*closed*).
            </p>
        </div>
        @endif

        {{-- Public Footer --}}
        <footer class="mt-8 text-center text-xs text-slate-400 pb-4">
            Dikelola dengan sistem transparansi kas &bull; <a href="{{ route('login') }}" class="text-slate-500 hover:underline">Login Pengurus</a>
        </footer>

        {{-- Lightbox Modal --}}
        <div x-show="modalPhoto" x-transition.opacity
            class="fixed inset-0 z-50 bg-black/80 flex items-center justify-center p-4"
            @click="modalPhoto = null" style="display: none;">
            <div class="relative max-w-3xl max-h-[90vh] bg-white rounded-xl overflow-hidden p-2" @click.stop>
                <button @click="modalPhoto = null" class="absolute top-3 right-3 h-8 w-8 rounded-full bg-black/50 text-white flex items-center justify-center text-sm hover:bg-black/70">✕</button>
                <img :src="modalPhoto" alt="Preview Bukti" class="max-h-[80vh] w-auto mx-auto rounded">
            </div>
        </div>
    </div>

    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
