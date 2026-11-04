@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <h1 class="text-3xl font-bold text-gray-900 mb-6">
        Detail Penjualan
    </h1>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

        <!-- PANEL KIRI -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-6">

            <!-- Informasi Utama -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-6">

                <div>
                    <p class="text-sm text-gray-500 mb-1">Produk</p>
                    <p class="text-lg font-semibold text-gray-900">
                        {{ $sale->consignment->product->name }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-500 mb-1">Toko</p>
                    <p class="text-lg font-semibold text-gray-900">
                        {{ $sale->consignment->store->name }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-500 mb-1">Harga Satuan</p>
                    <p class="text-lg font-semibold text-gray-900">
                        Rp {{ number_format($sale->consignment->product->price, 0, ',', '.') }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-500 mb-1">Tanggal Penjualan</p>
                    <p class="text-lg font-semibold text-gray-900">
                        {{ $sale->sale_date?->format('d M Y') ?? '-' }}
                    </p>
                </div>

            </div>

            <hr class="my-6 border-gray-300">

            <!-- Ringkasan Jumlah -->
            <h2 class="text-xl font-semibold text-gray-900 mb-4">
                Ringkasan Penjualan
            </h2>

            <div class="grid grid-cols-2 gap-4">

                <div class="bg-green-50 rounded-lg p-4">
                    <p class="text-sm text-gray-600 mb-2">
                        Jumlah Terjual
                    </p>
                    <p class="text-2xl font-bold text-green-600">
                        {{ $sale->quantity }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1">pcs</p>
                </div>

                <div class="bg-blue-50 rounded-lg p-4">
                    <p class="text-sm text-gray-600 mb-2">
                        Harga Satuan
                    </p>
                    <p class="text-2xl font-bold text-blue-600">
                        Rp {{ number_format($sale->consignment->product->price, 0, ',', '.') }}
                    </p>
                </div>

            </div>

        </div>

        <!-- PANEL KANAN -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">

            <h2 class="text-lg font-semibold text-gray-900 mb-5">
                Ringkasan Nilai
            </h2>

            <div class="flex justify-between items-center border-b border-gray-300 pb-3 mb-4 gap-3">
                <span class="text-gray-600">Jumlah Terjual:</span>
                <span class="font-semibold text-gray-900">
                    {{ $sale->quantity }} pcs
                </span>
            </div>

            <div class="flex justify-between items-center border-b border-gray-300 pb-3 mb-4 gap-3">
                <span class="text-gray-600">Harga Satuan:</span>
                <span class="font-semibold text-gray-900">
                    Rp {{ number_format($sale->consignment->product->price, 0, ',', '.') }}
                </span>
            </div>

            <div class="flex justify-between items-center pt-2 gap-3">
                <span class="font-semibold text-gray-900">
                    Total Penjualan:
                </span>
                <span class="text-xl font-bold text-blue-600 whitespace-nowrap">
                    Rp {{ number_format($sale->amount, 0, ',', '.') }}
                </span>
            </div>

        </div>

    </div>

    <!-- TOMBOL -->
    <div class="mt-6 flex flex-wrap gap-3">

        <a href="{{ route('sales.index') }}"
           class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-5 py-3 rounded-lg font-medium transition">
            Kembali
        </a>

        <a href="{{ route('consignments.show', $sale->consignment_id) }}"
           class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-3 rounded-lg font-medium transition">
            Lihat Detail Titipan
        </a>

    </div>

</div>
@endsection
