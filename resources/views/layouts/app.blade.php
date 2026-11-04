<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TitipOS - Sistem Titipan UKM</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-[#F8FAFC]">
    @auth
    <div class="flex h-screen overflow-hidden">
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-72 bg-gradient-to-b from-teal-600 via-cyan-600 to-blue-600 transform -translate-x-full lg:translate-x-0 lg:static transition-transform duration-300 shadow-2xl">
            <div class="flex flex-col h-full">
                <div class="flex items-center justify-between h-20 px-6 border-b border-white/10">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-3">
                        <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center shadow-xl">
                            <svg class="w-7 h-7 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                        <span class="font-bold text-xl text-white">TitipOS</span>
                    </a>
                    <button id="sidebar-close" class="lg:hidden p-2 rounded-lg text-white hover:bg-white/10 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <nav class="flex-1 px-4 py-6 overflow-y-auto">
                    <div class="space-y-2">
                        <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-300 {{ request()->routeIs('dashboard') ? 'bg-white text-teal-600 shadow-lg' : 'text-white hover:bg-white/10' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            <span class="font-semibold">Dashboard</span>
                        </a>

                        @if(in_array(auth()->user()->role->slug ?? '', ['umkm', 'admin']))
                        <a href="{{ route('products.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-300 {{ request()->routeIs('products.*') ? 'bg-white text-teal-600 shadow-lg' : 'text-white hover:bg-white/10' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            <span class="font-semibold">Produk</span>
                        </a>
                        @endif

                        {{-- Menu Mitra Toko khusus Admin --}}
                        @if(auth()->user()->role->slug === 'admin')
                        <a href="{{ route('stores.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-300 {{ request()->routeIs('stores.*') ? 'bg-white text-teal-600 shadow-lg' : 'text-white hover:bg-white/10' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 21h18M5 21V7l8-4v18M19 21V11l-6-4M9 9v.01M9 12v.01M9 15v.01M9 18v.01M15 14v.01M15 17v.01"/>
                            </svg>
                            <span class="font-semibold">Mitra Toko</span>
                        </a>
                        @endif

                        <a href="{{ route('consignments.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-300 {{ request()->routeIs('consignments.*') ? 'bg-white text-teal-600 shadow-lg' : 'text-white hover:bg-white/10' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <span class="font-semibold">Titipan</span>
                        </a>

                        <a href="{{ route('sales.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-300 {{ request()->routeIs('sales.*') ? 'bg-white text-teal-600 shadow-lg' : 'text-white hover:bg-white/10' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                            <span class="font-semibold">Penjualan</span>
                        </a>

                        @if(in_array(auth()->user()->role->slug ?? '', ['store', 'admin']))
                        <a href="{{ route('returns.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-300 {{ request()->routeIs('returns.*') ? 'bg-white text-teal-600 shadow-lg' : 'text-white hover:bg-white/10' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                            </svg>
                            <span class="font-semibold">Retur</span>
                        </a>
                        @endif

                        @if(auth()->user()->role->slug === 'admin')
                        <a href="{{ route('reports.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-300 {{ request()->routeIs('reports.*') ? 'bg-white text-teal-600 shadow-lg' : 'text-white hover:bg-white/10' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            <span class="font-semibold">Laporan</span>
                        </a>

                        <a href="{{ route('settlements.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-300 {{ request()->routeIs('settlements.*') ? 'bg-white text-teal-600 shadow-lg' : 'text-white hover:bg-white/10' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="font-semibold">Settlement</span>
                        </a>
                        @endif
                    </div>
                </nav>

                <div class="p-4 border-t border-white/10">
                    <div class="bg-white/10 backdrop-blur-lg rounded-xl p-4 mb-3">
                        <div class="flex items-center space-x-3 mb-3">
                            <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center text-teal-600 font-bold text-lg shadow-lg">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-sm font-bold text-white truncate">{{ auth()->user()->name }}</div>
                                <div class="text-xs text-white/80">{{ auth()->user()->role->name ?? 'User' }}</div>
                            </div>
                        </div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-center space-x-2 px-4 py-3 rounded-xl bg-white/10 text-white hover:bg-white hover:text-red-600 font-semibold transition-all duration-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-white border-b border-[#E5E7EB] h-20 flex items-center justify-between px-6 shadow-sm">
                <div class="flex items-center space-x-4">
                    <button id="sidebar-toggle" class="lg:hidden p-2 rounded-lg text-[#4B5563] hover:bg-[#F3F4F6] transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    <h1 class="text-2xl font-bold text-[#1F2937]">
                        @yield('header', 'Dashboard')
                    </h1>
                </div>
                <div class="text-sm text-[#6B7280] font-medium">
                    {{ now()->format('d M Y, H:i') }}
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-6 bg-[#F8FAFC]">
                @if(session('success'))
                    <div class="mb-6 bg-teal-50 border border-teal-200 text-teal-700 px-6 py-4 rounded-xl flex items-center space-x-3 shadow-sm">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="font-semibold">{{ session('success') }}</span>
                    </div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>

    <div id="sidebar-overlay" class="hidden fixed inset-0 bg-black/50 z-40 lg:hidden backdrop-blur-sm"></div>

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
