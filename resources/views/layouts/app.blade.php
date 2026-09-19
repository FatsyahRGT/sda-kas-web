<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — SDA Kas Web</title>
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#2563eb">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="apple-touch-icon" href="/icons/icon-192.svg">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased" x-data="{ sidebarOpen: false }">

{{-- Mobile sidebar backdrop --}}
<div x-show="sidebarOpen" x-transition.opacity
    class="fixed inset-0 z-40 bg-black/50 lg:hidden"
    @click="sidebarOpen = false"></div>

{{-- Sidebar --}}
<aside class="fixed inset-y-0 left-0 z-50 flex w-64 flex-col bg-slate-900 text-slate-100 transition-transform duration-300 lg:translate-x-0"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">

    {{-- Logo --}}
    <div class="flex h-16 items-center gap-3 border-b border-slate-700 px-6">
        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-600 text-white font-bold text-sm">₽</div>
        <span class="text-lg font-semibold">SDA Kas Web</span>
    </div>

    {{-- Nav --}}
    <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-0.5 text-sm">
        @php
            $current = request()->route()->getName();
        @endphp

        <x-nav-link href="{{ route('dashboard') }}" :active="str_starts_with($current ?? '', 'dashboard')" icon="🏠">Dashboard</x-nav-link>
        <x-nav-link href="{{ route('groups.index') }}" :active="str_starts_with($current ?? '', 'groups')" icon="🏦">Grup Kas</x-nav-link>
        <x-nav-link href="{{ route('members.index') }}" :active="str_starts_with($current ?? '', 'members')" icon="👥">Anggota</x-nav-link>
        <x-nav-link href="{{ route('periods.index') }}" :active="str_starts_with($current ?? '', 'periods')" icon="📅">Periode</x-nav-link>

        <div class="mt-3 mb-1 px-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Transaksi</div>
        <x-nav-link href="{{ route('incomes.index') }}" :active="str_starts_with($current ?? '', 'incomes')" icon="💰">Pemasukan</x-nav-link>
        <x-nav-link href="{{ route('expenses.index') }}" :active="str_starts_with($current ?? '', 'expenses')" icon="🧾">Pengeluaran</x-nav-link>
        <x-nav-link href="{{ route('expense-categories.index') }}" :active="str_starts_with($current ?? '', 'expense-categories')" icon="🗂️">Kategori</x-nav-link>

        <div class="mt-3 mb-1 px-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Laporan</div>
        <x-nav-link href="{{ route('reports.index') }}" :active="str_starts_with($current ?? '', 'reports')" icon="📊">Laporan & Export</x-nav-link>

        @can('manage-users')
        <div class="mt-3 mb-1 px-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Admin</div>
        <x-nav-link href="{{ route('users.index') }}" :active="str_starts_with($current ?? '', 'users')" icon="⚙️">Manajemen User</x-nav-link>
        @endcan
    </nav>

    {{-- User info --}}
    <div class="border-t border-slate-700 p-4">
        <div class="flex items-center gap-3">
            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-600 text-sm font-semibold text-white">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium truncate">{{ auth()->user()->name }}</p>
                <p class="text-xs text-slate-400 capitalize">{{ auth()->user()->role }}</p>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}" class="mt-3">
            @csrf
            <button type="submit" class="w-full text-left text-xs text-slate-400 hover:text-red-400 transition-colors">
                → Keluar
            </button>
        </form>
    </div>
</aside>

{{-- Main Content --}}
<div class="lg:pl-64 flex flex-col min-h-full">
    {{-- Top header --}}
    <header class="sticky top-0 z-30 flex h-16 items-center gap-4 border-b border-slate-200 bg-white/80 backdrop-blur px-4 sm:px-6">
        <button @click="sidebarOpen = true" class="lg:hidden text-slate-500 hover:text-slate-700">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
        </button>
        <h1 class="flex-1 text-lg font-semibold text-slate-800">@yield('header', 'Dashboard')</h1>
        <div class="flex items-center gap-3">
            @yield('header-actions')
        </div>
    </header>

    {{-- Flash Messages --}}
    <div class="px-4 sm:px-6 pt-4 space-y-2">
        @if(session('success'))
            <div data-flash class="flex items-start gap-3 rounded-lg bg-emerald-50 border border-emerald-200 p-3 text-sm text-emerald-800">
                <span>✓</span> {{ session('success') }}
            </div>
        @endif
        @if(session('error') || $errors->has('error'))
            <div data-flash class="flex items-start gap-3 rounded-lg bg-red-50 border border-red-200 p-3 text-sm text-red-800">
                <span>✕</span> {{ session('error') ?? $errors->first('error') }}
            </div>
        @endif
        @if(session('plainTextToken'))
            <div class="rounded-lg bg-amber-50 border border-amber-300 p-4 text-sm text-amber-900">
                <p class="font-semibold mb-2">⚠️ Simpan API Token ini sekarang! Tidak akan ditampilkan lagi.</p>
                <code class="block break-all bg-amber-100 p-2 rounded text-xs">{{ session('plainTextToken') }}</code>
            </div>
        @endif
        @if ($errors->any() && !$errors->has('error'))
            <div data-flash class="rounded-lg bg-red-50 border border-red-200 p-3 text-sm text-red-800">
                <p class="font-semibold mb-1">Terdapat kesalahan validasi:</p>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    {{-- Page Content --}}
    <main class="flex-1 p-4 sm:p-6">
        @yield('content')
    </main>

    <footer class="border-t border-slate-200 py-3 px-6 text-xs text-slate-400 text-center">
        SDA Kas Web &copy; {{ date('Y') }}
    </footer>
</div>

<script>
// Alpine.js minimal
(function() {
    document.querySelectorAll('[x-data]').forEach(el => {
        // handled inline
    });
})();
</script>

{{-- Alpine.js CDN (lightweight, no build step needed) --}}
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

@stack('scripts')
</body>
</html>
