<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - TitipOS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="min-h-screen lg:grid lg:grid-cols-2">
        <div class="relative hidden overflow-hidden bg-ink-900 p-12 lg:flex lg:flex-col lg:justify-between">
            <div class="pointer-events-none absolute -top-32 -left-24 size-96 rounded-full bg-brand-600/25 blur-3xl"></div>
            <div class="pointer-events-none absolute -right-32 bottom-0 size-96 rounded-full bg-sky-500/15 blur-3xl"></div>

            <a href="{{ route('login') }}" class="relative flex items-center gap-3">
                <span class="flex size-9 items-center justify-center rounded-[10px] bg-white/10 text-white ring-1 ring-white/15">
                    <svg class="size-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </span>
                <span class="text-[15px] font-semibold tracking-tight text-white">TitipOS</span>
            </a>

            <div class="relative max-w-md">
                <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-white/40">Sistem Titipan UKM</p>
                <h1 class="mt-4 text-[2.5rem] font-semibold leading-[1.1] tracking-tight text-white">
                    Kelola titipan<br>dengan tenang.
                </h1>
                <p class="mt-4 text-[15px] leading-relaxed text-white/60">
                    Satu tempat untuk produk, penjualan, retur, dan settlement — tanpa catatan yang tercecer.
                </p>

                <ul class="mt-10 space-y-3.5">
                    <li class="flex items-center gap-3 text-sm text-white/70">
                        <svg class="size-[18px] shrink-0 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Pelacakan produk dan penjualan
                    </li>
                    <li class="flex items-center gap-3 text-sm text-white/70">
                        <svg class="size-[18px] shrink-0 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Laporan keuangan yang akurat
                    </li>
                    <li class="flex items-center gap-3 text-sm text-white/70">
                        <svg class="size-[18px] shrink-0 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Settlement transparan untuk semua pihak
                    </li>
                </ul>
            </div>

            <p class="relative text-xs text-white/30">© {{ now()->year }} TitipOS</p>
        </div>

        <div class="flex min-h-screen items-center justify-center px-5 py-12 sm:px-8">
            <div class="w-full max-w-[400px]">
                <div class="mb-10 flex items-center gap-3 lg:hidden">
                    <span class="flex size-9 items-center justify-center rounded-[10px] bg-ink-900 text-white">
                        <svg class="size-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </span>
                    <span class="text-[15px] font-semibold tracking-tight text-ink-900">TitipOS</span>
                </div>

                <div class="mb-8">
                    <h2 class="text-2xl font-semibold tracking-tight text-ink-900">Masuk ke akun Anda</h2>
                    <p class="mt-2 text-sm text-ink-500">Gunakan email dan password yang terdaftar.</p>
                </div>

                @if(session('error'))
                    <div class="mb-6 flex items-start gap-3 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3">
                        <svg class="mt-px size-[18px] shrink-0 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                        </svg>
                        <p class="text-sm font-medium text-rose-700">{{ session('error') }}</p>
                    </div>
                @endif

                <form action="{{ route('login') }}" method="POST" class="space-y-5">
                    @csrf

                    <div>
                        <label for="email" class="label">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email"
                            placeholder="nama@email.com"
                            class="field @error('email') field-error @enderror">
                        @error('email')
                            <p class="error-text">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="label">Password</label>
                        <input id="password" name="password" type="password" required autocomplete="current-password"
                            placeholder="••••••••"
                            class="field @error('password') field-error @enderror">
                        @error('password')
                            <p class="error-text">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary w-full py-3">
                        Masuk
                    </button>
                </form>

                <p class="mt-8 text-center text-sm text-ink-500">
                    Belum punya akun?
                    <a href="{{ route('register') }}" class="font-semibold text-ink-900 underline decoration-hairline decoration-2 underline-offset-4 transition hover:decoration-ink-900">
                        Daftar sekarang
                    </a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
