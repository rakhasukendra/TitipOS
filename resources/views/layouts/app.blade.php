<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TitipOS - Sistem Titipan UKM</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex flex-col">
        <nav class="bg-white border-b border-gray-100 shadow-sm sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-purple-600 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                        <span class="font-bold text-xl text-gray-900">TitipOS</span>
                    </div>
                    
                    @auth
                    <div class="hidden md:flex items-center space-x-1">
                        <a href="{{ route('dashboard') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('dashboard') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                            Dashboard
                        </a>
                        
                        @if(in_array(auth()->user()->role->slug ?? '', ['umkm', 'admin']))
                            <a href="{{ route('products.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('products.*') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                                Produk
                            </a>
                        @endif
                        
                        <a href="{{ route('consignments.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('consignments.*') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                            Titipan
                        </a>
                        <a href="{{ route('sales.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('sales.*') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                            Penjualan
                        </a>
                        
                        @if(in_array(auth()->user()->role->slug ?? '', ['store', 'admin']))
                            <a href="{{ route('returns.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('returns.*') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                                Retur
                            </a>
                        @endif
                        
                        @if(auth()->user()->role->slug === 'admin')
                            <a href="{{ route('reports.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('reports.*') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                                Laporan
                            </a>
                            <a href="{{ route('settlements.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('settlements.*') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                                Settlement
                            </a>
                        @endif
                    </div>
                    @endauth

                    <div class="flex items-center space-x-4">
                        @auth
                            <div class="hidden md:flex items-center space-x-3 border-r border-gray-200 pr-4">
                                <div class="w-9 h-9 bg-gradient-to-br from-blue-500 to-purple-500 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                </div>
                                <div class="hidden lg:block">
                                    <div class="text-sm font-semibold text-gray-900">{{ auth()->user()->name }}</div>
                                    <div class="text-xs text-gray-500">{{ auth()->user()->role->name ?? 'User' }}</div>
                                </div>
                            </div>
                            
                            <div class="md:hidden">
                                <button id="mobile-menu-btn" class="p-2 rounded-lg text-gray-700 hover:bg-gray-100">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                                    </svg>
                                </button>
                            </div>
                            
                            <form action="{{ route('logout') }}" method="POST" class="hidden md:block">
                                @csrf
                                <button type="submit" class="text-gray-600 hover:text-gray-900 text-sm font-medium transition">
                                    Logout
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="text-gray-700 hover:text-gray-900 text-sm font-medium">Login</a>
                            <a href="{{ route('register') }}" class="bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                                Daftar
                            </a>
                        @endauth
                    </div>
                </div>

                @auth
                <div id="mobile-menu" class="hidden md:hidden pb-4 border-t border-gray-100 mt-2">
                    <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg {{ request()->routeIs('dashboard') ? 'bg-blue-100 text-blue-700' : '' }}">
                        Dashboard
                    </a>
                    
                    @if(in_array(auth()->user()->role->slug ?? '', ['umkm', 'admin']))
                        <a href="{{ route('products.index') }}" class="block px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg {{ request()->routeIs('products.*') ? 'bg-blue-100 text-blue-700' : '' }}">
                            Produk
                        </a>
                    @endif
                    
                    <a href="{{ route('consignments.index') }}" class="block px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg {{ request()->routeIs('consignments.*') ? 'bg-blue-100 text-blue-700' : '' }}">
                        Titipan
                    </a>
                    <a href="{{ route('sales.index') }}" class="block px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg {{ request()->routeIs('sales.*') ? 'bg-blue-100 text-blue-700' : '' }}">
                        Penjualan
                    </a>
                    
                    @if(in_array(auth()->user()->role->slug ?? '', ['store', 'admin']))
                        <a href="{{ route('returns.index') }}" class="block px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg {{ request()->routeIs('returns.*') ? 'bg-blue-100 text-blue-700' : '' }}">
                            Retur
                        </a>
                    @endif
                    
                    @if(auth()->user()->role->slug === 'admin')
                        <a href="{{ route('reports.index') }}" class="block px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg {{ request()->routeIs('reports.*') ? 'bg-blue-100 text-blue-700' : '' }}">
                            Laporan
                        </a>
                        <a href="{{ route('settlements.index') }}" class="block px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg {{ request()->routeIs('settlements.*') ? 'bg-blue-100 text-blue-700' : '' }}">
                            Settlement
                        </a>
                    @endif
                    
                    <div class="border-t border-gray-100 mt-2 pt-2">
                        <div class="px-4 py-2 text-sm text-gray-700">{{ auth()->user()->name }}</div>
                        <div class="px-4 py-1 text-xs text-gray-500">{{ auth()->user()->role->name ?? 'User' }}</div>
                        <form action="{{ route('logout') }}" method="POST" class="mt-2">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
                @endauth
            </div>
        </nav>

        <main class="flex-grow">
            @if(session('success'))
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center space-x-3">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif
            @yield('content')
        </main>

        <footer class="bg-white border-t border-gray-100 mt-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="text-center text-gray-600 text-sm">
                    <p>&copy; 2026 TitipOS - Sistem Manajemen Titipan UKM</p>
                </div>
            </div>
        </footer>
    </div>

    <script>
        document.getElementById('mobile-menu-btn')?.addEventListener('click', function() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        });
    </script>
</body>
</html>
