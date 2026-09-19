<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — SDA Kas Web</title>
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#2563eb">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex items-center justify-center font-sans antialiased">
    <div class="w-full max-w-sm px-4">
        <div class="bg-white rounded-2xl shadow-lg border border-slate-200 p-8">
            {{-- Logo --}}
            <div class="flex flex-col items-center mb-8">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-600 text-white font-bold text-2xl mb-3">₽</div>
                <h1 class="text-xl font-bold text-slate-800">SDA Kas Web</h1>
                <p class="text-sm text-slate-500 mt-1">Sistem Manajemen Uang Kas</p>
            </div>

            @if ($errors->any())
                <div class="mb-4 rounded-lg bg-red-50 border border-red-200 p-3 text-sm text-red-800">
                    {{ $errors->first() }}
                </div>
            @endif

            @if (session('success'))
                <div class="mb-4 rounded-lg bg-emerald-50 border border-emerald-200 p-3 text-sm text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-800 placeholder-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 @error('email') border-red-400 @enderror">
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Password</label>
                    <input type="password" name="password" id="password" required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-800 placeholder-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="remember" id="remember" class="rounded border-slate-300">
                    <label for="remember" class="text-sm text-slate-600">Ingat saya</label>
                </div>
                <button type="submit"
                    class="w-full rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-colors">
                    Masuk
                </button>
            </form>
        </div>
        <p class="mt-4 text-center text-xs text-slate-400">SDA Kas Web &copy; {{ date('Y') }}</p>
    </div>
</body>
</html>
