<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — SDA Kas Web</title>
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#2563eb">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    
    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
<body class="min-h-full font-sans antialiased bg-slate-100 flex flex-col justify-center m-0 p-0 selection:bg-blue-600 selection:text-white">
    <div class="min-h-screen flex flex-col lg:flex-row w-full">
        
        {{-- Left Hero Section (Desktop & Tablet) --}}
        <div class="hidden lg:flex lg:w-1/2 xl:w-7/12 bg-slate-900 flex-col items-center justify-center p-8 sm:p-12 relative overflow-hidden" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);">
            
            {{-- Background Graphic Container (Cleanly Fitted without overlap) --}}
            <div class="w-full max-w-lg bg-white/5 border border-white/10 rounded-3xl p-6 backdrop-blur-md shadow-2xl flex flex-col items-center justify-center">
                <img src="{{ asset('images/bg-login.png') }}" alt="SDA DKI Jakarta" 
                     style="width: 100%; max-height: 65vh; object-fit: contain; display: block;" 
                     class="rounded-2xl">
                
                <div class="mt-4 text-center">
                    <h3 class="text-white font-bold text-base tracking-wide">SISTEM MANAJEMEN KAS TERPADU</h3>
                    <p class="text-xs text-blue-300 mt-0.5">Transparan, Akurat, dan Akuntabel</p>
                </div>
            </div>

            {{-- Footer info --}}
            <div class="mt-6 text-xs text-slate-400 text-center">
                &copy; {{ date('Y') }} SDA Kas Web &bull; Kelapa Gading Jakarta Utara
            </div>
        </div>

        {{-- Right Form Section (Mobile, Tablet, Desktop) --}}
        <div class="flex-1 flex items-center justify-center p-4 sm:p-8 lg:p-12 bg-slate-100 min-h-screen">
            <div class="w-full max-w-sm sm:max-w-md bg-white rounded-3xl shadow-xl shadow-slate-300/50 border border-slate-200 p-6 sm:p-10">
                
                {{-- Logo & Header --}}
                <div class="flex flex-col items-center text-center mb-6">
                    <div style="width: 80px; height: 80px; margin-bottom: 12px; display: flex; align-items: center; justify-content: center;">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo SDA Kas" 
                             style="max-width: 80px; max-height: 80px; width: auto; height: auto; object-fit: contain; display: block;">
                    </div>
                    <h2 class="text-xl sm:text-2xl font-extrabold text-slate-800 tracking-tight">Masuk ke Panel</h2>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">Silakan masukkan kredensial akun Anda</p>
                </div>

                {{-- Alert Error --}}
                @if ($errors->any())
                    <div class="mb-4 rounded-2xl bg-red-50 border border-red-200 p-3.5 text-xs text-red-800" style="display: flex; align-items: center; gap: 10px;">
                        <i class="bi bi-exclamation-triangle-fill text-red-600 text-base" style="flex-shrink: 0;"></i>
                        <div class="font-semibold">{{ $errors->first() }}</div>
                    </div>
                @endif

                {{-- Alert Success --}}
                @if (session('success'))
                    <div class="mb-4 rounded-2xl bg-emerald-50 border border-emerald-200 p-3.5 text-xs text-emerald-800" style="display: flex; align-items: center; gap: 10px;">
                        <i class="bi bi-check-circle-fill text-emerald-600 text-base" style="flex-shrink: 0;"></i>
                        <span class="font-semibold">{{ session('success') }}</span>
                    </div>
                @endif

                {{-- Form --}}
                <form method="POST" action="{{ route('login') }}" style="display: flex; flex-direction: column; gap: 16px;">
                    @csrf
                    
                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Alamat Email</label>
                        <div style="position: relative; display: flex; align-items: center;">
                            <i class="bi bi-envelope text-slate-400" style="position: absolute; left: 14px; font-size: 16px; pointer-events: none; line-height: 1;"></i>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                                placeholder="nama@email.com"
                                style="width: 100%; box-sizing: border-box; padding: 12px 14px 12px 42px; border-radius: 14px; border: 1px solid #cbd5e1; font-size: 14px; color: #1e293b; background-color: #fff; outline: none; transition: border-color 0.2s, box-shadow 0.2s;"
                                class="focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 @error('email') border-red-400 @enderror">
                        </div>
                    </div>

                    {{-- Password --}}
                    <div>
                        <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Kata Sandi</label>
                        <div style="position: relative; display: flex; align-items: center;">
                            <i class="bi bi-lock text-slate-400" style="position: absolute; left: 14px; font-size: 16px; pointer-events: none; line-height: 1;"></i>
                            <input type="password" name="password" id="password" required
                                placeholder="••••••••"
                                style="width: 100%; box-sizing: border-box; padding: 12px 14px 12px 42px; border-radius: 14px; border: 1px solid #cbd5e1; font-size: 14px; color: #1e293b; background-color: #fff; outline: none; transition: border-color 0.2s, box-shadow 0.2s;"
                                class="focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                        </div>
                    </div>

                    {{-- Remember Me --}}
                    <div style="display: flex; align-items: center; justify-content: space-between; font-size: 13px;">
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; color: #475569; user-select: none;">
                            <input type="checkbox" name="remember" id="remember" style="border-radius: 4px; border: 1px solid #cbd5e1; width: 16px; height: 16px;" class="text-blue-600 focus:ring-blue-500/20">
                            <span>Ingat saya</span>
                        </label>
                    </div>

                    {{-- Submit Button --}}
                    <div style="padding-top: 4px;">
                        <button type="submit"
                            style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px; background-color: #2563eb; color: #ffffff; padding: 12px 20px; border-radius: 14px; font-size: 14px; font-weight: 700; border: none; cursor: pointer; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25); transition: background-color 0.2s, transform 0.1s;"
                            class="hover:bg-blue-700 active:scale-[0.99]">
                            <i class="bi bi-box-arrow-in-right text-base leading-none"></i>
                            <span>Masuk ke Panel</span>
                        </button>
                    </div>
                </form>

                <div class="mt-8 pt-5 border-t border-slate-100 text-center">
                    <p class="text-xs text-slate-400" style="display: flex; align-items: center; justify-content: center; gap: 6px;">
                        <i class="bi bi-shield-lock"></i>
                        <span>SDA Kas Web &bull; Autentikasi Terenkripsi</span>
                    </p>
                </div>
            </div>
        </div>

    </div>

    @if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: "{{ session('success') }}",
                timer: 3000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        });
    </script>
    @endif
</body>
</html>
