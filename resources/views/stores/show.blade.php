@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">
                {{ $store->name }}
            </h1>
            <p class="text-gray-500 mt-1">Detail informasi mitra toko.</p>
        </div>

        <a href="{{ route('stores.edit', $store) }}"
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
            Edit Toko
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">

        <div class="bg-white shadow rounded-xl p-6">
            <h2 class="font-semibold text-lg text-gray-900 mb-4">
                Informasi Toko
            </h2>

            <div class="space-y-3 text-sm">
                <p>
                    <span class="text-gray-500">Nama Toko:</span>
                    <strong>{{ $store->name }}</strong>
                </p>

                <p>
                    <span class="text-gray-500">Alamat:</span>
                    {{ $store->address ?? '-' }}
                </p>

                <p>
                    <span class="text-gray-500">Telepon:</span>
                    {{ $store->phone ?? '-' }}
                </p>

                <p>
                    <span class="text-gray-500">Email:</span>
                    {{ $store->email ?? '-' }}
                </p>

                <p>
                    <span class="text-gray-500">Store Manager:</span>
                    {{ $store->manager->name ?? 'Belum ditentukan' }}
                </p>
            </div>
        </div>

        <div class="bg-white shadow rounded-xl p-6">
            <h2 class="font-semibold text-lg text-gray-900 mb-4">
                Ringkasan Titipan
            </h2>

            <div class="text-3xl font-bold text-blue-600">
                {{ $store->consignments->count() }}
            </div>

            <p class="text-gray-500 text-sm mt-1">
                Total transaksi titipan
            </p>
        </div>

    </div>

    <div class="bg-white shadow rounded-xl overflow-hidden">

        <div class="p-6 border-b">
            <h2 class="font-semibold text-lg text-gray-900">
                Riwayat Titipan Toko
            </h2>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">

                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs text-gray-500 uppercase">Produk</th>
                        <th class="px-6 py-3 text-left text-xs text-gray-500 uppercase">Jumlah</th>
                        <th class="px-6 py-3 text-left text-xs text-gray-500 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200">

                    @forelse($store->consignments as $consignment)
                    <tr>

                        <td class="px-6 py-4 text-sm font-medium text-gray-900">
                            {{ $consignment->product->name ?? '-' }}
                        </td>

                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $consignment->quantity }}
                        </td>

                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $consignment->consignment_date?->format('d/m/Y') ?? '-' }}
                        </td>

                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ ucfirst($consignment->status) }}
                        </td>

                        <td class="px-6 py-4 text-sm">
                            <a href="{{ route('consignments.show', $consignment) }}"
                               class="text-blue-600 hover:text-blue-800">
                                Detail
                            </a>
                        </td>

                    </tr>

                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                            Belum ada transaksi titipan untuk toko ini.
                        </td>
                    </tr>
                    @endforelse

                </tbody>
            </table>
        </div>

    </div>

    <div class="mt-6">
        <a href="{{ route('stores.index') }}"
           class="text-blue-600 hover:text-blue-800">
            ← Kembali ke daftar toko
        </a>
    </div>

</div>
@endsection
