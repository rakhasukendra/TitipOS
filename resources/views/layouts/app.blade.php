<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'TitipOS - Sistem Titipan UKM')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @auth
    <div class="flex h-screen overflow-hidden">
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 flex w-[272px] -translate-x-full flex-col border-r border-hairline bg-white transition-transform duration-300 ease-out lg:static lg:translate-x-0">
            <div class="flex h-16 shrink-0 items-center justify-between border-b border-hairline px-5">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                    <span class="flex size-9 items-center justify-center rounded-[10px] bg-ink-900 text-white">
                        <svg class="size-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </span>
                    <span class="leading-tight">
                        <span class="block text-[15px] font-semibold tracking-tight text-ink-900">TitipOS</span>
                        <span class="block text-[11px] text-ink-400">Sistem Titipan UKM</span>
                    </span>
                </a>
                <button id="sidebar-close" type="button" class="rounded-lg p-2 text-ink-500 transition hover:bg-canvas hover:text-ink-900 lg:hidden" aria-label="Tutup menu">
                    <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <nav class="flex-1 overflow-y-auto px-3 py-5">
                <p class="px-3 pb-2 section-label">Menu</p>
                <div class="space-y-1">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'nav-link-active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        <span>Dashboard</span>
                    </a>

                    @if(in_array(auth()->user()->role->slug ?? '', ['umkm', 'admin']))
                    <a href="{{ route('products.index') }}" class="nav-link {{ request()->routeIs('products.*') ? 'nav-link-active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        <span>Produk</span>
                    </a>
                    @endif

                    <a href="{{ route('consignments.index') }}" class="nav-link {{ request()->routeIs('consignments.*') ? 'nav-link-active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                        <span>{{ auth()->user()->isStoreManager() ? 'Titipan Masuk' : (auth()->user()->isAdmin() ? 'Semua Titipan' : 'Titipan Saya') }}</span>
                    </a>

                    <a href="{{ route('sales.index') }}" class="nav-link {{ request()->routeIs('sales.*') ? 'nav-link-active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        <span>Penjualan</span>
                    </a>

                    @if(in_array(auth()->user()->role->slug ?? '', ['store', 'admin']))
                    <a href="{{ route('returns.index') }}" class="nav-link {{ request()->routeIs('returns.*') ? 'nav-link-active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                        </svg>
                        <span>Retur</span>
                    </a>
                    @endif
                </div>

                @if(auth()->user()->role->slug === 'admin')
                <p class="px-3 pt-6 pb-2 section-label">Manajemen</p>
                <div class="space-y-1">
                    <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'nav-link-active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <span>Laporan</span>
                    </a>

                    <a href="{{ route('settlements.index') }}" class="nav-link {{ request()->routeIs('settlements.*') ? 'nav-link-active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Settlement</span>
                    </a>
                </div>
                @endif
            </nav>

            <div class="border-t border-hairline p-3">
                <div class="flex items-center gap-3 rounded-xl px-2 py-2">
                    <span class="flex size-9 shrink-0 items-center justify-center rounded-full border border-hairline bg-canvas text-[13px] font-semibold text-ink-700">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </span>
                    <span class="min-w-0 flex-1 leading-tight">
                        <span class="block truncate text-[13px] font-semibold text-ink-900">{{ auth()->user()->name }}</span>
                        <span class="block truncate text-[11px] text-ink-500">{{ auth()->user()->role->name ?? 'Pengguna' }}</span>
                    </span>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="rounded-lg p-2 text-ink-400 transition hover:bg-rose-50 hover:text-rose-600" title="Keluar" aria-label="Keluar">
                            <svg class="size-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <div class="flex flex-1 flex-col overflow-hidden">
            <header class="sticky top-0 z-30 flex h-16 shrink-0 items-center justify-between gap-4 border-b border-hairline bg-canvas/85 px-4 backdrop-blur sm:px-6 lg:px-8">
                <div class="flex min-w-0 items-center gap-3">
                    <button id="sidebar-toggle" type="button" class="rounded-lg p-2 text-ink-500 transition hover:bg-white hover:text-ink-900 lg:hidden" aria-label="Buka menu">
                        <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    <div class="min-w-0">
                        <h1 class="truncate text-[15px] font-semibold tracking-tight text-ink-900">@yield('header', 'Dashboard')</h1>
                        <p class="truncate text-xs text-ink-500">@yield('subheader', auth()->user()->isStoreManager() ? 'Aktivitas titipan masuk toko Anda' : 'Ringkasan aktivitas titipan Anda')</p>
                    </div>
                </div>
                <div class="flex shrink-0 items-center gap-3">
                    <span class="hidden text-xs font-medium text-ink-500 sm:inline">{{ now()->format('d M Y, H:i') }}</span>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto">
                <div class="mx-auto w-full max-w-7xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
                    @if(session('success'))
                        <div class="mb-5 flex items-start gap-3 rounded-2xl border border-brand-100 bg-brand-50 px-4 py-3.5">
                            <svg class="mt-px size-[18px] shrink-0 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-sm font-medium text-brand-700">{{ session('success') }}</p>
                        </div>
                    @endif
                    @if($errors->any())
                        <div class="mb-5 flex items-start gap-3 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3.5">
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

                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <div id="sidebar-overlay" class="fixed inset-0 z-40 hidden bg-ink-900/30 backdrop-blur-sm lg:hidden"></div>

    <script>
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const sidebarClose = document.getElementById('sidebar-close');
        const sidebarOverlay = document.getElementById('sidebar-overlay');

        function openSidebar() {
            sidebar.classList.remove('-translate-x-full');
            sidebarOverlay.classList.remove('hidden');
        }

        function closeSidebar() {
            sidebar.classList.add('-translate-x-full');
            sidebarOverlay.classList.add('hidden');
        }

        sidebarToggle?.addEventListener('click', openSidebar);
        sidebarClose?.addEventListener('click', closeSidebar);
        sidebarOverlay?.addEventListener('click', closeSidebar);
    </script>
    @else
    @yield('content')
    @endauth
</body>
</html>
