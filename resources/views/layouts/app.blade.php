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
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo.png') }}">
    
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
    <style>
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #94a3b8; border-radius: 9999px; }
    </style>
</head>
<body class="h-full font-sans antialiased text-slate-800" x-data="{ sidebarOpen: false }">

{{-- Mobile sidebar backdrop --}}
<div x-show="sidebarOpen" x-transition.opacity
    class="fixed inset-0 z-40 bg-slate-950/60 backdrop-blur-xs lg:hidden"
    @click="sidebarOpen = false" style="display: none;"></div>

{{-- Sidebar --}}
<aside class="fixed inset-y-0 left-0 z-50 flex w-64 flex-col bg-slate-900 text-slate-100 transition-transform duration-300 lg:translate-x-0"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">

    {{-- Logo --}}
    <div class="flex h-16 items-center gap-3 border-b border-slate-800 px-4 overflow-hidden">
        <img src="{{ asset('images/logo.png') }}" alt="Logo SDA Kas" width="36" height="36" style="width: 36px; height: 36px; max-width: 36px; max-height: 36px; object-fit: contain; flex-shrink: 0;" class="rounded-lg">
        <div class="flex flex-col min-w-0">
            <span class="text-sm font-bold tracking-tight text-white leading-tight truncate">SDA Kas Web</span>
            <span class="text-[11px] text-slate-400 truncate">Sistem Manajemen Kas</span>
        </div>
    </div>

    {{-- Nav --}}
    <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1 text-sm">
        @php
            $current = request()->route()->getName();
        @endphp

        <x-nav-link href="{{ route('dashboard') }}" :active="str_starts_with($current ?? '', 'dashboard')" icon="bi-grid-1x2-fill">Dashboard</x-nav-link>
        <x-nav-link href="{{ route('groups.index') }}" :active="str_starts_with($current ?? '', 'groups')" icon="bi-building">Grup Kas</x-nav-link>
        <x-nav-link href="{{ route('members.index') }}" :active="str_starts_with($current ?? '', 'members')" icon="bi-people-fill">Anggota</x-nav-link>
        <x-nav-link href="{{ route('periods.index') }}" :active="str_starts_with($current ?? '', 'periods')" icon="bi-calendar3">Periode Kas</x-nav-link>

        <div class="pt-4 pb-1 px-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Transaksi</div>
        <x-nav-link href="{{ route('incomes.index') }}" :active="str_starts_with($current ?? '', 'incomes')" icon="bi-arrow-down-left-circle-fill">Pemasukan</x-nav-link>
        <x-nav-link href="{{ route('expenses.index') }}" :active="str_starts_with($current ?? '', 'expenses')" icon="bi-arrow-up-right-circle-fill">Pengeluaran</x-nav-link>
        <x-nav-link href="{{ route('expense-categories.index') }}" :active="str_starts_with($current ?? '', 'expense-categories')" icon="bi-tags-fill">Kategori</x-nav-link>

        <div class="pt-4 pb-1 px-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Laporan</div>
        <x-nav-link href="{{ route('reports.index') }}" :active="str_starts_with($current ?? '', 'reports')" icon="bi-file-earmark-bar-graph-fill">Laporan & Export</x-nav-link>

        @can('manage-users')
        <div class="pt-4 pb-1 px-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Superadmin</div>
        <x-nav-link href="{{ route('users.index') }}" :active="str_starts_with($current ?? '', 'users')" icon="bi-person-gear">Manajemen User</x-nav-link>
        @endcan
    </nav>

    {{-- User info --}}
    <div class="border-t border-slate-800 p-4 bg-slate-950/40">
        <div class="flex items-center gap-3">
            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-600 text-sm font-bold text-white shadow-sm">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold truncate text-white leading-tight">{{ auth()->user()->name }}</p>
                <p class="text-[11px] text-slate-400 capitalize">{{ auth()->user()->role }}</p>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}" class="mt-3">
            @csrf
            <button type="submit" class="flex items-center gap-2 w-full text-left text-xs font-medium text-slate-400 hover:text-red-400 transition-colors py-1">
                <i class="bi bi-box-arrow-right text-sm"></i> Keluar
            </button>
        </form>
    </div>
</aside>

{{-- Main Content --}}
<div class="lg:pl-64 flex flex-col min-h-full">
    {{-- Top header --}}
    <header class="sticky top-0 z-30 flex h-16 items-center gap-4 border-b border-slate-200 bg-white/90 backdrop-blur px-4 sm:px-6">
        <button @click="sidebarOpen = true" class="lg:hidden text-slate-600 hover:text-slate-900 p-1 rounded-lg hover:bg-slate-100">
            <i class="bi bi-list text-2xl leading-none"></i>
        </button>
        <h1 class="flex-1 text-base sm:text-lg font-bold text-slate-800 flex items-center gap-2">
            @yield('header', 'Dashboard')
        </h1>
        <div class="flex items-center gap-3">
            @yield('header-actions')
        </div>
    </header>

    {{-- Flash Messages (fallback) --}}
    <div class="px-4 sm:px-6 pt-4 space-y-2">
        @if(session('plainTextToken'))
            <div class="rounded-xl bg-amber-50 border border-amber-300 p-4 text-sm text-amber-900 shadow-sm">
                <p class="font-semibold mb-2 flex items-center gap-1.5"><i class="bi bi-exclamation-triangle-fill text-amber-600"></i> Simpan API Token ini sekarang! Tidak akan ditampilkan lagi.</p>
                <code class="block break-all bg-amber-100 p-2.5 rounded-lg text-xs font-mono select-all">{{ session('plainTextToken') }}</code>
            </div>
        @endif
        @if ($errors->any() && !$errors->has('error'))
            <div class="rounded-xl bg-red-50 border border-red-200 p-3.5 text-sm text-red-800">
                <p class="font-semibold mb-1 flex items-center gap-1.5"><i class="bi bi-x-circle-fill text-red-600"></i> Terdapat kesalahan validasi:</p>
                <ul class="list-disc list-inside space-y-0.5 text-xs">
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

    <footer class="border-t border-slate-200 py-3.5 px-6 text-xs text-slate-400 text-center flex flex-col sm:flex-row items-center justify-between gap-2">
        <span>SDA Kas Web &copy; {{ date('Y') }}</span>
        <span class="text-slate-400 flex items-center gap-1"><i class="bi bi-shield-check text-emerald-600"></i> Sistem Kas Terpadu</span>
    </footer>
</div>

{{-- SweetAlert2 CDN --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

{{-- Alpine.js CDN --}}
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

<script>
// Global SweetAlert2 Form Confirmation Listener
document.addEventListener('DOMContentLoaded', () => {
    // Intercept delete forms
    document.addEventListener('submit', function(e) {
        const form = e.target;
        if (form.matches('form[onsubmit*="confirm"], form.form-delete, form[data-confirm]')) {
            if (form.dataset.confirmed === 'true') {
                return true;
            }
            e.preventDefault();
            e.stopImmediatePropagation();

            const title = form.getAttribute('data-confirm-title') || 'Apakah Anda yakin?';
            const text = form.getAttribute('data-confirm-text') || 'Data ini akan dihapus secara permanen.';

            Swal.fire({
                title: title,
                text: text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                confirmButtonText: '<i class="bi bi-trash3 me-1"></i> Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-2xl shadow-xl border border-slate-100',
                    confirmButton: 'rounded-xl px-4 py-2 text-sm font-semibold',
                    cancelButton: 'rounded-xl px-4 py-2 text-sm font-semibold'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.dataset.confirmed = 'true';
                    form.removeAttribute('onsubmit');
                    form.submit();
                }
            });
            return false;
        }
    }, true);

    // Format nominal input as currency while typing
    document.querySelectorAll('input[data-rupiah]').forEach(input => {
        input.addEventListener('input', () => {
            let val = input.value.replace(/\D/g, '');
            input.value = val;
        });
    });

    // Flash notifications via SweetAlert Toast
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: "{{ session('success') }}",
            timer: 3000,
            timerProgressBar: true,
            showConfirmButton: false,
            toast: true,
            position: 'top-end',
            customClass: { popup: 'rounded-xl shadow-lg border border-slate-100' }
        });
    @endif

    @if(session('error') || $errors->has('error'))
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: "{{ session('error') ?? $errors->first('error') }}",
            timer: 4000,
            timerProgressBar: true,
            showConfirmButton: false,
            toast: true,
            position: 'top-end',
            customClass: { popup: 'rounded-xl shadow-lg border border-slate-100' }
        });
    @endif
});

// Service Worker Registration
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js').catch((err) => {
            console.warn('SW registration failed:', err);
        });
    });
}
</script>

@stack('scripts')
</body>
</html>
