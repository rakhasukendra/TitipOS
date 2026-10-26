@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-6">Detail Titipan</h1>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="bg-white shadow rounded-lg p-6">
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <p class="text-sm text-gray-600">Produk</p>
                        <p class="text-lg font-semibold">{{ $consignment->product->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Toko</p>
                        <p class="text-lg font-semibold">{{ $consignment->store->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Harga Satuan</p>
                        <p class="text-lg font-semibold">Rp {{ number_format($consignment->product->price, 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Tanggal Titipan</p>
                        <p class="text-lg font-semibold">{{ $consignment->consignment_date->format('d M Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Status</p>
                        <p class="text-lg font-semibold">
                            <span class="px-2 py-1 rounded text-sm
                                {{ $consignment->status === 'active' ? 'bg-green-100 text-green-800' : 
                                   ($consignment->status === 'settled' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800') }}">
                                {{ $consignment->status === 'active' ? 'Aktif' : ($consignment->status === 'settled' ? 'Ter-settle' : 'Dikembalikan') }}
                            </span>
                        </p>
                    </div>
                </div>

                <div class="border-t pt-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Ringkasan Stok</h2>
                    <div class="grid grid-cols-4 gap-4">
                        <div class="bg-blue-50 p-4 rounded">
                            <p class="text-sm text-gray-600">Jumlah Titipan</p>
                            <p class="text-2xl font-bold text-blue-600">{{ $consignment->quantity }}</p>
                        </div>
                        <div class="bg-green-50 p-4 rounded">
                            <p class="text-sm text-gray-600">Terjual</p>
                            <p class="text-2xl font-bold text-green-600">{{ $consignment->sales->sum('quantity') }}</p>
                        </div>
                        <div class="bg-orange-50 p-4 rounded">
                            <p class="text-sm text-gray-600">Retur</p>
                            <p class="text-2xl font-bold text-orange-600">{{ $consignment->returns->sum('quantity') }}</p>
                        </div>
                        <div class="bg-red-50 p-4 rounded">
                            <p class="text-sm text-gray-600">Sisa Stok</p>
                            <p class="text-2xl font-bold text-red-600">
                                {{ $consignment->quantity - $consignment->sales->sum('quantity') - $consignment->returns->sum('quantity') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div>
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Ringkasan Nilai</h3>
                <div class="space-y-3">
                    <div class="flex justify-between border-b pb-2">
                        <span class="text-gray-600">Total Penjualan:</span>
                        <span class="font-semibold text-green-600">Rp {{ number_format($consignment->sales->sum('amount'), 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between border-b pb-2">
                        <span class="text-gray-600">Retur (nilai):</span>
                        <span class="font-semibold text-red-600">-Rp {{ number_format($consignment->returns->sum('quantity') * $consignment->product->price, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between pt-2">
                        <span class="text-gray-900 font-semibold">Nilai Kotor:</span>
                        <span class="font-bold text-lg text-blue-600">
                            Rp {{ number_format($consignment->sales->sum('amount') - ($consignment->returns->sum('quantity') * $consignment->product->price), 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Riwayat Penjualan</h2>
            @if($consignment->sales->count() > 0)
                <div class="space-y-3">
                    @foreach($consignment->sales as $sale)
                    <div class="border-l-4 border-green-500 pl-4 py-2">
                        <div class="flex justify-between">
                            <span class="font-medium">{{ $sale->quantity }} pcs</span>
                            <span class="text-green-600 font-semibold">Rp {{ number_format($sale->amount, 0, ',', '.') }}</span>
                        </div>
                        <p class="text-sm text-gray-500">{{ $sale->sale_date->format('d M Y') }}</p>
                    </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-4">Belum ada penjualan</p>
            @endif
        </div>

        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Riwayat Retur</h2>
            @if($consignment->returns->count() > 0)
                <div class="space-y-3">
                    @foreach($consignment->returns as $return)
                    <div class="border-l-4 border-orange-500 pl-4 py-2">
                        <div class="flex justify-between">
                            <span class="font-medium">{{ $return->quantity }} pcs</span>
                            <span class="text-orange-600 font-semibold">-Rp {{ number_format($return->quantity * $consignment->product->price, 0, ',', '.') }}</span>
                        </div>
                        <p class="text-sm text-gray-500">{{ $return->reason ?? 'Tidak ada alasan' }}</p>
                        <p class="text-xs text-gray-400">{{ $return->return_date->format('d M Y') }}</p>
                    </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-4">Belum ada retur</p>
            @endif
        </div>
    </div>

    <div class="mt-6">
        <a href="{{ route('consignments.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded">
            Kembali
        </a>
    </div>
</div>
@endsection
