@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Detail Settlement</h1>
        <span class="px-4 py-2 rounded text-sm font-medium
            {{ $settlement->settled_at ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
            {{ $settlement->settled_at ? 'Settled' : 'Pending' }}
        </span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-sm text-gray-600 mb-2">Informasi Toko</h3>
            <p class="text-xl font-bold text-gray-900">{{ $settlement->store->name }}</p>
            <p class="text-sm text-gray-600 mt-1">{{ $settlement->store->address }}</p>
        </div>

        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-sm text-gray-600 mb-2">UMKM</h3>
            <p class="text-xl font-bold text-gray-900">{{ $settlement->umkm->name }}</p>
            <p class="text-sm text-gray-600 mt-1">{{ $settlement->umkm->email }}</p>
        </div>

        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-sm text-gray-600 mb-2">Tanggal Settlement</h3>
            <p class="text-xl font-bold text-gray-900">{{ $settlement->settlement_date->format('d M Y') }}</p>
            @if($settlement->settled_at)
                <p class="text-sm text-gray-600 mt-1">Settled: {{ $settlement->settled_at->format('d M Y H:i') }}</p>
            @endif
        </div>
    </div>

    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <h2 class="text-2xl font-semibold text-gray-900 mb-6">Ringkasan Keuangan</h2>
        
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="border-l-4 border-green-500 pl-4">
                <p class="text-sm text-gray-600">Total Penjualan</p>
                <p class="text-2xl font-bold text-green-600">Rp {{ number_format($settlement->total_sales, 0, ',', '.') }}</p>
            </div>

            <div class="border-l-4 border-red-500 pl-4">
                <p class="text-sm text-gray-600">Total Retur</p>
                <p class="text-2xl font-bold text-red-600">-Rp {{ number_format($settlement->total_returns, 0, ',', '.') }}</p>
            </div>

            <div class="border-l-4 border-orange-500 pl-4">
                <p class="text-sm text-gray-600">Komisi Toko ({{ $settlement->commission_rate }}%)</p>
                <p class="text-2xl font-bold text-orange-600">
                    -Rp {{ number_format(($settlement->total_sales - $settlement->total_returns) * ($settlement->commission_rate / 100), 0, ',', '.') }}
                </p>
            </div>

            <div class="border-l-4 border-blue-500 pl-4">
                <p class="text-sm text-gray-600">Netto untuk UMKM</p>
                <p class="text-2xl font-bold text-blue-600">Rp {{ number_format($settlement->net_amount, 0, ',', '.') }}</p>
            </div>
        </div>

        <div class="border-t pt-4">
            <h3 class="font-semibold text-gray-900 mb-3">Perhitungan:</h3>
            <div class="bg-gray-50 p-4 rounded space-y-2 text-sm">
                <div class="flex justify-between">
                    <span>Penjualan Kotor:</span>
                    <span class="font-mono">Rp {{ number_format($settlement->total_sales, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-red-600">
                    <span>Dikurangi Retur:</span>
                    <span class="font-mono">-Rp {{ number_format($settlement->total_returns, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between border-t pt-2">
                    <span>Subtotal:</span>
                    <span class="font-mono font-semibold">Rp {{ number_format($settlement->total_sales - $settlement->total_returns, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-orange-600">
                    <span>Komisi Toko ({{ $settlement->commission_rate }}%):</span>
                    <span class="font-mono">-Rp {{ number_format(($settlement->total_sales - $settlement->total_returns) * ($settlement->commission_rate / 100), 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between border-t-2 pt-2 text-lg font-bold text-blue-600">
                    <span>Netto (dibayar ke UMKM):</span>
                    <span class="font-mono">Rp {{ number_format($settlement->net_amount, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Detail Titipan</h2>
        
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produk</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Titipan</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Terjual</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Retur</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Nilai</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($consignments as $consignment)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-900">{{ $consignment->product->name }}</div>
                        <div class="text-sm text-gray-500">Rp {{ number_format($consignment->product->price, 0, ',', '.') }}/pcs</div>
                    </td>
                    <td class="px-4 py-3 text-center text-gray-700">{{ $consignment->quantity }}</td>
                    <td class="px-4 py-3 text-center text-green-600 font-semibold">{{ $consignment->sales->sum('quantity') }}</td>
                    <td class="px-4 py-3 text-center text-red-600 font-semibold">{{ $consignment->returns->sum('quantity') }}</td>
                    <td class="px-4 py-3 text-right font-semibold text-gray-900">
                        Rp {{ number_format($consignment->sales->sum('amount'), 0, ',', '.') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="flex gap-2">
        <a href="{{ route('settlements.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded">
            Kembali
        </a>
        @if(!$settlement->settled_at)
        <form action="{{ route('settlements.update', $settlement) }}" method="POST" class="inline">
            @csrf
            @method('PUT')
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
                Tandai Settled
            </button>
        </form>
        @endif
    </div>
</div>
@endsection
