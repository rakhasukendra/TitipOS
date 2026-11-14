<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - TitipOS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="min-h-screen lg:grid lg:grid-cols-2">
        <div class="relative hidden overflow-hidden bg-ink-900 p-12 lg:flex lg:flex-col lg:justify-between">
            <div class="pointer-events-none absolute -top-32 -left-24 size-96 rounded-full bg-sky-500/20 blur-3xl"></div>
            <div class="pointer-events-none absolute -right-32 bottom-0 size-96 rounded-full bg-brand-600/25 blur-3xl"></div>

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
                    Mulai dari sini,<br>tanpa ribet.
                </h1>
                <p class="mt-4 text-[15px] leading-relaxed text-white/60">
                    Daftarkan usaha Anda dan jalankan alur titipan yang rapi sejak hari pertama.
                </p>

                <div class="mt-10 space-y-4">
                    <div class="rounded-2xl border border-white/10 bg-white/[0.04] p-5">
                        <div class="flex items-center gap-2.5">
                            <svg class="size-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            <p class="text-sm font-semibold text-white">Untuk UMKM</p>
                        </div>
                        <p class="mt-2 text-[13px] leading-relaxed text-white/50">
                            Kelola produk titipan, pantau penjualan, dan terima settlement yang jelas.
                        </p>
                    </div>

                    <div class="rounded-2xl border border-white/10 bg-white/[0.04] p-5">
                        <div class="flex items-center gap-2.5">
                            <svg class="size-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 9l1.5-5h15L21 9M3 9h18M3 9v10a2 2 0 002 2h14a2 2 0 002-2V9M9 21v-6h6v6"/>
                            </svg>
                            <p class="text-sm font-semibold text-white">Untuk Toko</p>
                        </div>
                        <p class="mt-2 text-[13px] leading-relaxed text-white/50">
                            Catat penjualan dan retur, lalu lihat laporan lengkap kapan saja.
                        </p>
                    </div>
                </div>
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
                    <h2 class="text-2xl font-semibold tracking-tight text-ink-900">Buat akun baru</h2>
                    <p class="mt-2 text-sm text-ink-500">Isi data diri untuk memulai.</p>
                </div>

                @if($errors->any())
                    <div class="mb-6 flex items-start gap-3 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3">
                        <svg class="mt-px size-[18px] shrink-0 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                        </svg>
                        <div class="space-y-1">
                            @foreach($errors->all() as $error)
                                <p class="text-sm font-medium text-rose-700">{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                @endif

                <form action="{{ route('register') }}" method="POST" class="space-y-5">
                    @csrf

                    <div>
                        <label for="name" class="label">Nama lengkap</label>
                        <input id="name" name="name" type="text" value="{{ old('name') }}" required autocomplete="name"
                            placeholder="Nama Anda"
                            class="field @error('name') field-error @enderror">
                        @error('name')
                            <p class="error-text">{{ $message }}</p>
                        @enderror
                    </div>

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
                        <label for="role_id" class="label">Tipe akun</label>
                        <select id="role_id" name="role_id" required class="field field-select @error('role_id') field-error @enderror">
                            <option value="">Pilih tipe akun</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('role_id')
                            <p class="error-text">{{ $message }}</p>
                        @enderror
                    </div>

                    <div id="store-fields" class="hidden space-y-5 rounded-xl border border-hairline bg-canvas/60 p-4">
                        <div>
                            <label for="store_name" class="label">Nama toko</label>
                            <input id="store_name" name="store_name" type="text" value="{{ old('store_name') }}"
                                placeholder="Contoh: Toko Berkah"
                                class="field @error('store_name') field-error @enderror">
                            @error('store_name')
                                <p class="error-text">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="store_address" class="label">Alamat toko</label>
                            <input id="store_address" name="store_address" type="text" value="{{ old('store_address') }}"
                                placeholder="Alamat lengkap toko"
                                class="field @error('store_address') field-error @enderror">
                            @error('store_address')
                                <p class="error-text">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="password" class="label">Password</label>
                        <input id="password" name="password" type="password" required autocomplete="new-password"
                            placeholder="••••••••"
                            class="field @error('password') field-error @enderror">
                        @error('password')
                            <p class="error-text">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="label">Konfirmasi password</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                            placeholder="••••••••"
                            class="field">
                    </div>

                    <button type="submit" class="btn btn-primary w-full py-3">
                        Daftar sekarang
                    </button>
                </form>

                <p class="mt-8 text-center text-sm text-ink-500">
                    Sudah punya akun?
                    <a href="{{ route('login') }}" class="font-semibold text-ink-900 underline decoration-hairline decoration-2 underline-offset-4 transition hover:decoration-ink-900">
                        Masuk
                    </a>
                </p>
            </div>
        </div>
    </div>

    <script>
        const roleSelect = document.getElementById('role_id');
        const storeFields = document.getElementById('store-fields');
        const storeRoleId = '{{ $storeRoleId }}';

        function toggleStoreFields() {
            storeFields.classList.toggle('hidden', roleSelect.value !== storeRoleId);
        }

        roleSelect.addEventListener('change', toggleStoreFields);
        toggleStoreFields();
    </script>
</body>
</html>
