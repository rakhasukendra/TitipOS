@extends('layouts.app')

@section('header', 'Dashboard')

@section('content')
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6 sm:mb-10">
    <div class="bg-white rounded-xl sm:rounded-2xl shadow-sm hover:shadow-md transition p-4 sm:p-6 border border-gray-100">
        <div class="flex items-center justify-between mb-3 sm:mb-4">
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-100 rounded-lg sm:rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
        </div>
        <div class="text-xs sm:text-sm text-gray-600 mb-1">Total Produk</div>
        <div class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $stats['total_products'] }}</div>
        <div class="text-xs text-gray-500 mt-1 sm:mt-2">Produk terdaftar</div>
    </div>

    <div class="bg-white rounded-xl sm:rounded-2xl shadow-sm hover:shadow-md transition p-4 sm:p-6 border border-gray-100">
        <div class="flex items-center justify-between mb-3 sm:mb-4">
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-green-100 rounded-lg sm:rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
        <div class="text-xs sm:text-sm text-gray-600 mb-1">Titipan Aktif</div>
        <div class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $stats['active_consignments'] }}</div>
        <div class="text-xs text-gray-500 mt-1 sm:mt-2">Sedang berjalan</div>
    </div>

    <div class="bg-white rounded-xl sm:rounded-2xl shadow-sm hover:shadow-md transition p-4 sm:p-6 border border-gray-100">
        <div class="flex items-center justify-between mb-3 sm:mb-4">
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-purple-100 rounded-lg sm:rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
            </div>
        </div>
        <div class="text-xs sm:text-sm text-gray-600 mb-1">Penjualan Bulan Ini</div>
        <div class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $stats['monthly_sales'] }}</div>
        <div class="text-xs text-gray-500 mt-1 sm:mt-2">Transaksi tercatat</div>
    </div>

    <div class="bg-gradient-to-br from-orange-400 via-pink-500 to-red-500 rounded-xl sm:rounded-2xl shadow-lg hover:shadow-xl transition p-4 sm:p-6 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-24 h-24 sm:w-32 sm:h-32 bg-white/10 rounded-full -mr-12 -mt-12"></div>
        <div class="absolute bottom-0 left-0 w-20 h-20 sm:w-24 sm:h-24 bg-white/10 rounded-full -ml-10 -mb-10"></div>
        
        <div class="relative z-10">
            <div class="flex items-center justify-between mb-3 sm:mb-4">
                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-white/20 backdrop-blur-lg rounded-xl sm:rounded-2xl flex items-center justify-center shadow-lg">
                    <svg class="w-6 h-6 sm:w-7 sm:h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="text-xs sm:text-sm font-medium text-white mb-1 sm:mb-2 opacity-90">Total Pendapatan</div>
            <div class="text-2xl sm:text-3xl lg:text-4xl font-bold text-white mb-1">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</div>
            <div class="text-xs sm:text-sm text-white/75">Pendapatan kotor</div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
    <div class="bg-white rounded-xl sm:rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-4 sm:px-6 py-4 sm:py-5 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-transparent">
            <div class="flex items-center justify-between">
                <h2 class="text-lg sm:text-xl font-semibold text-gray-900">Titipan Terbaru</h2>
                <a href="{{ route('consignments.index') }}" class="text-xs sm:text-sm text-blue-600 hover:text-blue-700 font-medium">
                    Lihat Semua →
                </a>
            </div>
        </div>
        <div class="p-4 sm:p-6">
            @if($recentConsignments->count() > 0)
                <div class="space-y-3 sm:space-y-4">
                    @foreach($recentConsignments as $consignment)
                    <div class="flex items-center justify-between p-3 sm:p-4 rounded-lg sm:rounded-xl bg-gray-50 hover:bg-gray-100 transition">
                        <div class="flex items-center space-x-3 sm:space-x-4 flex-1 min-w-0">
                            <div class="w-8 h-8 sm:w-10 sm:h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="font-semibold text-gray-900 text-sm sm:text-base truncate">{{ $consignment->product->name }}</div>
                                <div class="text-xs sm:text-sm text-gray-600 truncate">{{ $consignment->store->name }}</div>
                            </div>
                        </div>
                        <div class="text-right ml-3 flex-shrink-0">
                            <div class="font-bold text-gray-900 text-sm sm:text-base">{{ $consignment->quantity }} <span class="text-xs text-gray-500">pcs</span></div>
                            <div class="text-xs text-gray-500">{{ $consignment->consignment_date->format('d M Y') }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 sm:py-12">
                    <svg class="w-12 h-12 sm:w-16 sm:h-16 text-gray-300 mx-auto mb-3 sm:mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                    <p class="text-gray-500 text-sm sm:text-base">Belum ada titipan</p>
                    @if(in_array(auth()->user()->role->slug ?? '', ['umkm', 'admin']))
                        <a href="{{ route('consignments.create') }}" class="inline-block mt-3 sm:mt-4 text-blue-600 hover:text-blue-700 font-medium text-xs sm:text-sm">
                            Buat titipan baru →
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-xl sm:rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-4 sm:px-6 py-4 sm:py-5 border-b border-gray-100 bg-gradient-to-r from-green-50 to-transparent">
            <div class="flex items-center justify-between">
                <h2 class="text-lg sm:text-xl font-semibold text-gray-900">Penjualan Terbaru</h2>
                <a href="{{ route('sales.index') }}" class="text-xs sm:text-sm text-green-600 hover:text-green-700 font-medium">
                    Lihat Semua →
                </a>
            </div>
        </div>
        <div class="p-4 sm:p-6">
            @if($recentSales->count() > 0)
                <div class="space-y-3 sm:space-y-4">
                    @foreach($recentSales as $sale)
                    <div class="flex items-center justify-between p-3 sm:p-4 rounded-lg sm:rounded-xl bg-gray-50 hover:bg-gray-100 transition">
                        <div class="flex items-center space-x-3 sm:space-x-4 flex-1 min-w-0">
                            <div class="w-8 h-8 sm:w-10 sm:h-10 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="font-semibold text-gray-900 text-sm sm:text-base truncate">{{ $sale->consignment->product->name }}</div>
                                <div class="text-xs sm:text-sm text-gray-600 truncate">{{ $sale->consignment->store->name }}</div>
                            </div>
                        </div>
                        <div class="text-right ml-3 flex-shrink-0">
                            <div class="font-bold text-green-600 text-sm sm:text-base">Rp {{ number_format($sale->amount, 0, ',', '.') }}</div>
                            <div class="text-xs text-gray-500">{{ $sale->sale_date->format('d M Y') }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 sm:py-12">
                    <svg class="w-12 h-12 sm:w-16 sm:h-16 text-gray-300 mx-auto mb-3 sm:mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-gray-500 text-sm sm:text-base">Belum ada penjualan</p>
                    @if(in_array(auth()->user()->role->slug ?? '', ['store', 'admin']))
                        <a href="{{ route('sales.create') }}" class="inline-block mt-3 sm:mt-4 text-green-600 hover:text-green-700 font-medium text-xs sm:text-sm">
                            Catat penjualan →
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
