@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Detail Produk</h1>
    </div>

    <div class="bg-white shadow-sm rounded-2xl border border-gray-100 overflow-hidden">
        <div class="p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-2">Nama Produk</label>
                    <p class="text-lg font-semibold text-gray-900">{{ $product->name }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-2">Pemilik (UMKM)</label>
                    <p class="text-lg font-semibold text-gray-900">{{ $product->umkm->name ?? '-' }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-2">Harga</label>
                    <p class="text-2xl font-bold text-blue-600">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-2">Stok</label>
                    <p class="text-2xl font-bold text-green-600">{{ $product->stock }} <span class="text-sm text-gray-500">pcs</span></p>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-600 mb-2">Deskripsi</label>
                    <p class="text-gray-700">{{ $product->description ?? 'Tidak ada deskripsi' }}</p>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-600 mb-2">Tanggal Dibuat</label>
                    <p class="text-gray-700">{{ $product->created_at->format('d M Y H:i') }}</p>
                </div>
            </div>

            @if($product->consignments->count() > 0)
            <div class="mt-8 pt-8 border-t border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Riwayat Titipan</h3>
                <div class="space-y-3">
                    @foreach($product->consignments as $consignment)
                    <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-900">{{ $consignment->store->name }}</p>
                            <p class="text-sm text-gray-600">{{ $consignment->consignment_date->format('d M Y') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-gray-900">{{ $consignment->quantity }} pcs</p>
                            <span class="text-xs px-2 py-1 rounded
                                {{ $consignment->status === 'active' ? 'bg-green-100 text-green-800' : 
                                   ($consignment->status === 'settled' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800') }}">
                                {{ ucfirst($consignment->status) }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <div class="px-8 py-4 bg-gray-50 flex gap-2">
            <a href="{{ route('products.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg transition">
                Kembali
            </a>
            <a href="{{ route('products.edit', $product) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                Edit Produk
            </a>
        </div>
    </div>
</div>
@endsection
