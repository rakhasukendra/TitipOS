<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TitipOS - Platform Manajemen Titipan UKM</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        .animate-float { animation: float 3s ease-in-out infinite; }
        .glass { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.3); }
        .glass-dark { background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.1); }
    </style>
</head>
<body class="bg-[#FDFCF5]">
    <nav class="fixed w-full top-0 z-50 glass shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <a href="/" class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-teal-600 to-cyan-600 rounded-xl flex items-center justify-center shadow-lg shadow-teal-500/20 transition-transform group-hover:rotate-12">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <span class="font-bold text-lg sm:text-xl text-[#1F2937] hidden sm:inline">TitipOS</span>
                </a>
                
                <div class="flex items-center space-x-2 sm:space-x-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="text-[#1F2937] hover:text-teal-600 font-medium text-sm sm:text-base px-3 sm:px-4 py-2 rounded-lg hover:bg-teal-50 transition-all duration-300">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-[#1F2937] hover:text-teal-600 font-medium text-sm sm:text-base px-3 sm:px-4 py-2 rounded-lg hover:bg-teal-50 transition-all duration-300">
                            Login
                        </a>
                        <a href="{{ route('register') }}" class="bg-teal-600 hover:bg-teal-700 text-white px-4 sm:px-6 py-2 rounded-lg font-medium text-sm sm:text-base shadow-lg shadow-teal-500/30 transition-all duration-300 hover:shadow-xl hover:shadow-teal-500/40">
                            Daftar
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main>
        <section class="relative min-h-screen flex items-center pt-16 pb-20 px-4 sm:px-6 lg:px-8 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-teal-50 via-cyan-50 to-blue-50"></div>
            <div class="absolute top-0 right-0 w-96 h-96 bg-teal-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-float"></div>
            <div class="absolute top-20 right-20 w-72 h-72 bg-cyan-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-float delay-200"></div>
            <div class="absolute bottom-0 left-0 w-80 h-80 bg-blue-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-float delay-400"></div>
            
            <div class="max-w-7xl mx-auto relative z-10 w-full">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <div class="text-center lg:text-left">
                        <div class="inline-block mb-6 px-6 py-2.5 rounded-full bg-teal-50 border border-teal-100 text-teal-700 font-medium text-sm animate-float">
                            🏆 Platform #1 untuk UMKM Indonesia
                        </div>
                        
                        <h1 class="text-5xl sm:text-6xl lg:text-7xl font-bold mb-6 leading-tight text-[#1F2937]">
                            Kelola Titipan <br class="hidden lg:block"/>
                            <span class="bg-gradient-to-r from-teal-600 via-cyan-600 to-blue-600 bg-clip-text text-transparent">Jadi Lebih Mudah</span>
                        </h1>
                        
                        <p class="text-lg sm:text-xl text-[#4B5563] mb-8 max-w-xl mx-auto lg:mx-0 leading-relaxed">
                            Platform manajemen titipan yang efisien untuk UKM. Kelola produk, catat penjualan, dan dapatkan laporan transparan dalam satu sistem.
                        </p>
                        
                        <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                            @auth
                                <a href="{{ url('/dashboard') }}" class="group relative bg-teal-600 hover:bg-teal-700 text-white px-8 py-4 rounded-xl font-semibold text-lg shadow-xl shadow-teal-500/30 transition-all duration-300 hover:shadow-2xl hover:shadow-teal-500/40 hover:-translate-y-1">
                                    <span class="relative z-10 flex items-center space-x-2">
                                        <span>Buka Dashboard</span>
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                        </svg>
                                    </span>
                                </a>
                            @else
                                <a href="{{ route('register') }}" class="group relative bg-teal-600 hover:bg-teal-700 text-white px-8 py-4 rounded-xl font-semibold text-lg shadow-xl shadow-teal-500/30 transition-all duration-300 hover:shadow-2xl hover:shadow-teal-500/40 hover:-translate-y-1">
                                    <span class="relative z-10 flex items-center space-x-2">
                                        <span>Mulai Gratis Sekarang</span>
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                        </svg>
                                    </span>
                                </a>
                                <a href="#fitur" class="glass text-[#1F2937] hover:bg-white px-8 py-4 rounded-xl font-semibold text-lg shadow-lg transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
                                    Lihat Fitur
                                </a>
                            @endauth
                        </div>
                        
                        <div class="mt-12 flex items-center justify-center lg:justify-start space-x-10">
                            <div class="text-center">
                                <div class="text-3xl sm:text-4xl font-bold text-[#1F2937] mb-1">500+</div>
                                <div class="text-sm text-[#6B7280] font-medium">UMKM Terdaftar</div>
                            </div>
                            <div class="w-px h-12 bg-[#E5E7EB]"></div>
                            <div class="text-center">
                                <div class="text-3xl sm:text-4xl font-bold text-[#1F2937] mb-1">10K+</div>
                                <div class="text-sm text-[#6B7280] font-medium">Transaksi Bulanan</div>
                            </div>
                            <div class="w-px h-12 bg-[#E5E7EB]"></div>
                            <div class="text-center">
                                <div class="text-3xl sm:text-4xl font-bold text-[#1F2937] mb-1">98%</div>
                                <div class="text-sm text-[#6B7280] font-medium">Kepuasan Pengguna</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="hidden lg:flex justify-center">
                        <div class="relative">
                            <div class="absolute inset-0 bg-gradient-to-br from-teal-200 via-cyan-200 to-blue-200 rounded-3xl blur-2xl opacity-40"></div>
                            <div class="relative glass rounded-3xl p-8 shadow-2xl">
                                <div class="space-y-6">
                                    <div class="flex items-center space-x-4 glass p-4 rounded-2xl hover:bg-white transition-colors duration-300 shadow-sm">
                                        <div class="w-14 h-14 bg-teal-600 rounded-2xl flex items-center justify-center shadow-lg shadow-teal-500/30">
                                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="font-bold text-[#1F2937]">Cepat & Efisien</div>
                                            <div class="text-sm text-[#6B7280]">Real-time tracking</div>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center space-x-4 glass p-4 rounded-2xl hover:bg-white transition-colors duration-300 shadow-sm">
                                        <div class="w-14 h-14 bg-cyan-600 rounded-2xl flex items-center justify-center shadow-lg shadow-cyan-500/30">
                                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="font-bold text-[#1F2937]">Aman & Terpercaya</div>
                                            <div class="text-sm text-[#6B7280]">Data terenkripsi</div>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center space-x-4 glass p-4 rounded-2xl hover:bg-white transition-colors duration-300 shadow-sm">
                                        <div class="w-14 h-14 bg-blue-600 rounded-2xl flex items-center justify-center shadow-lg shadow-blue-500/30">
                                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="font-bold text-[#1F2937]">Laporan Detail</div>
                                            <div class="text-sm text-[#6B7280]">Analytics lengkap</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="fitur" class="py-20 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-16">
                    <div class="inline-block mb-4 px-6 py-2.5 rounded-full bg-teal-50 border border-teal-100 text-teal-700 font-medium text-sm">
                        ⭐ Fitur Unggulan
                    </div>
                    <h2 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-[#1F2937] mb-4">
                        Semua yang Anda Butuhkan
                    </h2>
                    <p class="text-xl text-[#4B5563] max-w-2xl mx-auto">Fitur lengkap untuk kesuksesan bisnis titipan Anda</p>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="bg-white rounded-3xl p-8 shadow-lg shadow-[#E0E7FF]/50 hover:shadow-xl hover:shadow-[#E0E7FF]/70 transition-all duration-300 hover:-translate-y-2">
                        <div class="w-16 h-16 bg-teal-100 rounded-2xl flex items-center justify-center mb-6">
                            <svg class="w-8 h-8 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-[#1F2937] mb-3">Manajemen Produk</h3>
                        <p class="text-[#4B5563] leading-relaxed">Kelola produk titipan dengan informasi lengkap, harga, dan stok real-time</p>
                    </div>

                    <div class="bg-white rounded-3xl p-8 shadow-lg shadow-[#E0E7FF]/50 hover:shadow-xl hover:shadow-[#E0E7FF]/70 transition-all duration-300 hover:-translate-y-2">
                        <div class="w-16 h-16 bg-cyan-100 rounded-2xl flex items-center justify-center mb-6">
                            <svg class="w-8 h-8 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-[#1F2937] mb-3">Pencatatan Penjualan</h3>
                        <p class="text-[#4B5563] leading-relaxed">Catat setiap transaksi dengan mudah, akurat, dan terintegrasi dengan inventory</p>
                    </div>

                    <div class="bg-white rounded-3xl p-8 shadow-lg shadow-[#E0E7FF]/50 hover:shadow-xl hover:shadow-[#E0E7FF]/70 transition-all duration-300 hover:-translate-y-2">
                        <div class="w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center mb-6">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-[#1F2937] mb-3">Settlement Otomatis</h3>
                        <p class="text-[#4B5563] leading-relaxed">Proses settlement transparan dan otomatis dengan perhitungan bagi hasil yang akurat</p>
                    </div>

                    <div class="bg-white rounded-3xl p-8 shadow-lg shadow-[#E0E7FF]/50 hover:shadow-xl hover:shadow-[#E0E7FF]/70 transition-all duration-300 hover:-translate-y-2">
                        <div class="w-16 h-16 bg-emerald-100 rounded-2xl flex items-center justify-center mb-6">
                            <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-[#1F2937] mb-3">Analytics Dashboard</h3>
                        <p class="text-[#4B5563] leading-relaxed">Dashboard dengan visualisasi data, insights bisnis, dan rekomendasi cerdas</p>
                    </div>

                    <div class="bg-white rounded-3xl p-8 shadow-lg shadow-[#E0E7FF]/50 hover:shadow-xl hover:shadow-[#E0E7FF]/70 transition-all duration-300 hover:-translate-y-2">
                        <div class="w-16 h-16 bg-teal-100 rounded-2xl flex items-center justify-center mb-6">
                            <svg class="w-8 h-8 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-[#1F2937] mb-3">Mobile Friendly</h3>
                        <p class="text-[#4B5563] leading-relaxed">Akses dari mana saja, kapan saja dengan tampilan mobile yang responsif</p>
                    </div>

                    <div class="bg-white rounded-3xl p-8 shadow-lg shadow-[#E0E7FF]/50 hover:shadow-xl hover:shadow-[#E0E7FF]/70 transition-all duration-300 hover:-translate-y-2">
                        <div class="w-16 h-16 bg-cyan-100 rounded-2xl flex items-center justify-center mb-6">
                            <svg class="w-8 h-8 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-[#1F2937] mb-3">Multi-Role</h3>
                        <p class="text-[#4B5563] leading-relaxed">Permission system lengkap untuk UMKM, Toko, dan Admin</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-20 px-4 sm:px-6 lg:px-8 bg-[#F8FAFC]">
            <div class="max-w-7xl mx-auto">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 sm:gap-8 mb-16 text-center">
                    <div class="glass rounded-3xl p-8">
                        <div class="text-4xl sm:text-5xl font-bold text-teal-600 mb-3">500+</div>
                        <div class="text-lg text-[#4B5563]">UMKM Terdaftar</div>
                    </div>
                    <div class="glass rounded-3xl p-8">
                        <div class="text-4xl sm:text-5xl font-bold text-cyan-600 mb-3">10K+</div>
                        <div class="text-lg text-[#4B5563]">Transaksi Bulanan</div>
                    </div>
                    <div class="glass rounded-3xl p-8">
                        <div class="text-4xl sm:text-5xl font-bold text-blue-600 mb-3">98%</div>
                        <div class="text-lg text-[#4B5563]">Kepuasan Pengguna</div>
                    </div>
                </div>

                <div class="text-center mb-16">
                    <h2 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-[#1F2937] mb-4">
                        Bagaimana TitipOS Bekerja?
                    </h2>
                    <p class="text-xl text-[#4B5563] max-w-2xl mx-auto">Proses sederhana untuk kesuksesan bisnis Anda</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="text-center group">
                        <div class="w-20 h-20 mx-auto mb-6 bg-gradient-to-br from-teal-500 to-cyan-600 rounded-2xl flex items-center justify-center text-white font-bold text-2xl shadow-xl shadow-teal-500/30 group-hover:scale-110 transition-transform duration-300">
                            1
                        </div>
                        <h3 class="font-bold text-xl text-[#1F2937] mb-3">Daftar Akun</h3>
                        <p class="text-[#4B5563] leading-relaxed">Buat akun sesuai tipe bisnis Anda (UMKM atau Toko)</p>
                    </div>
                    <div class="text-center group">
                        <div class="w-20 h-20 mx-auto mb-6 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-2xl flex items-center justify-center text-white font-bold text-2xl shadow-xl shadow-cyan-500/30 group-hover:scale-110 transition-transform duration-300">
                            2
                        </div>
                        <h3 class="font-bold text-xl text-[#1F2937] mb-3">Kelola Produk</h3>
                        <p class="text-[#4B5563] leading-relaxed">Masukkan data produk dan monitor stok real-time</p>
                    </div>
                    <div class="text-center group">
                        <div class="w-20 h-20 mx-auto mb-6 bg-gradient-to-br from-blue-500 to-teal-600 rounded-2xl flex items-center justify-center text-white font-bold text-2xl shadow-xl shadow-blue-500/30 group-hover:scale-110 transition-transform duration-300">
                            3
                        </div>
                        <h3 class="font-bold text-xl text-[#1F2937] mb-3">Catat Transaksi</h3>
                        <p class="text-[#4B5563] leading-relaxed">Catat setiap penjualan dan retur dengan mudah</p>
                    </div>
                    <div class="text-center group">
                        <div class="w-20 h-20 mx-auto mb-6 bg-gradient-to-br from-teal-600 to-emerald-600 rounded-2xl flex items-center justify-center text-white font-bold text-2xl shadow-xl shadow-teal-500/30 group-hover:scale-110 transition-transform duration-300">
                            4
                        </div>
                        <h3 class="font-bold text-xl text-[#1F2937] mb-3">Lihat Laporan</h3>
                        <p class="text-[#4B5563] leading-relaxed">Dapatkan settlement dan laporan yang transparan</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-20 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto">
                <div class="relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-r from-teal-600 via-cyan-600 to-blue-600 rounded-3xl"></div>
                    <div class="relative glass rounded-3xl p-12 lg:p-20 text-center">
                        <div class="max-w-3xl mx-auto">
                            <h2 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-white mb-6">
                                Siap Tingkatkan<br/>Bisnis Anda? 🚀
                            </h2>
                            <p class="text-xl text-teal-50 mb-10 max-w-2xl mx-auto">
                                Bergabunglah dengan 500+ UMKM yang telah meningkatkan efisiensi bisnis mereka dengan TitipOS
                            </p>
                            @auth
                                <a href="{{ url('/dashboard') }}" class="inline-block bg-white text-teal-600 px-10 py-5 rounded-xl font-bold text-lg shadow-2xl hover:shadow-white/50 hover:scale-105 transition-all duration-300">
                                    Buka Dashboard Sekarang
                                </a>
                            @else
                                <a href="{{ route('register') }}" class="inline-block bg-white text-teal-600 px-10 py-5 rounded-xl font-bold text-lg shadow-2xl hover:shadow-white/50 hover:scale-105 transition-all duration-300">
                                    Daftar Gratis Sekarang
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="bg-[#1F2937] text-[#9CA3AF] py-12 sm:py-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
                <div>
                    <div class="flex items-center space-x-2 mb-6">
                        <div class="w-10 h-10 bg-gradient-to-br from-teal-500 to-cyan-600 rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                        <span class="font-bold text-white text-xl">TitipOS</span>
                    </div>
                    <p class="text-sm leading-relaxed mb-4">Platform manajemen titipan terbaik untuk UMKM Indonesia 🇮🇩</p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-[#9CA3AF] hover:text-teal-400 transition">Facebook</a>
                        <a href="#" class="text-[#9CA3AF] hover:text-teal-400 transition">Instagram</a>
                        <a href="#" class="text-[#9CA3AF] hover:text-teal-400 transition">Twitter</a>
                    </div>
                </div>
                <div>
                    <div class="font-bold text-white mb-4">Produk</div>
                    <ul class="space-y-3 text-sm">
                        <li><a href="#" class="hover:text-teal-400 transition">Fitur</a></li>
                        <li><a href="#" class="hover:text-teal-400 transition">Pricing</a></li>
                        <li><a href="#" class="hover:text-teal-400 transition">Security</a></li>
                    </ul>
                </div>
                <div>
                    <div class="font-bold text-white mb-4">Perusahaan</div>
                    <ul class="space-y-3 text-sm">
                        <li><a href="#" class="hover:text-teal-400 transition">Tentang</a></li>
                        <li><a href="#" class="hover:text-teal-400 transition">Blog</a></li>
                        <li><a href="#" class="hover:text-teal-400 transition">Kontak</a></li>
                    </ul>
                </div>
                <div>
                    <div class="font-bold text-white mb-4">Legal</div>
                    <ul class="space-y-3 text-sm">
                        <li><a href="#" class="hover:text-teal-400 transition">Privacy</a></li>
                        <li><a href="#" class="hover:text-teal-400 transition">Terms</a></li>
                        <li><a href="#" class="hover:text-teal-400 transition">Cookie Policy</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-[#374151] pt-8 text-center text-sm">
                <p>&copy; 2026 TitipOS. Made with love for Indonesian UMKM</p>
            </div>
        </div>
    </footer>
</body>
</html>
