@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-6">Laporan Titipan dan Penjualan</h1>

    <div class="grid grid-cols-4 gap-4 mb-8">
        <div class="bg-white shadow rounded-lg p-6">
            <p class="text-sm text-gray-600">Total Titipan (unit)</p>
            <p class="text-3xl font-bold text-blue-600">
                @php
                    $totalConsigned = \App\Models\Consignment::sum('quantity');
                @endphp
                {{ $totalConsigned }}
            </p>
        </div>
        <div class="bg-white shadow rounded-lg p-6">
            <p class="text-sm text-gray-600">Total Terjual (unit)</p>
            <p class="text-3xl font-bold text-green-600">
                @php
                    $totalSold = \App\Models\Sale::sum('quantity');
                @endphp
                {{ $totalSold }}
            </p>
        </div>
        <div class="bg-white shadow rounded-lg p-6">
            <p class="text-sm text-gray-600">Total Retur (unit)</p>
            <p class="text-3xl font-bold text-orange-600">
                @php
                    $totalReturned = \App\Models\ReturnItem::sum('quantity');
                @endphp
                {{ $totalReturned }}
            </p>
        </div>
        <div class="bg-white shadow rounded-lg p-6">
            <p class="text-sm text-gray-600">Sisa Stok (unit)</p>
            <p class="text-3xl font-bold text-red-600">
                {{ $totalConsigned - $totalSold - $totalReturned }}
            </p>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-2xl font-semibold text-gray-900 mb-6">Detail Titipan per Toko</h2>
        
        <table class="w-full">
            <thead>
                <tr class="border-b-2 border-gray-200">
                    <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Toko</th>
                    <th class="text-center py-3 px-4 text-sm font-semibold text-gray-700">Titipan</th>
                    <th class="text-center py-3 px-4 text-sm font-semibold text-gray-700">Terjual</th>
                    <th class="text-center py-3 px-4 text-sm font-semibold text-gray-700">Retur</th>
                    <th class="text-center py-3 px-4 text-sm font-semibold text-gray-700">Sisa</th>
                    <th class="text-right py-3 px-4 text-sm font-semibold text-gray-700">Nilai Penjualan</th>
                    <th class="text-center py-3 px-4 text-sm font-semibold text-gray-700">Detail</th>
                </tr>
            </thead>
            <tbody>
                @foreach(\App\Models\Store::with('consignments.product', 'consignments.sales', 'consignments.returns')->get() as $store)
                    @php
                        $storeConsignments = $store->consignments;
                        $totalConsignedStore = $storeConsignments->sum('quantity');
                        $totalSoldStore = $storeConsignments->sum(function($c) { return $c->sales->sum('quantity'); });
                        $totalReturnedStore = $storeConsignments->sum(function($c) { return $c->returns->sum('quantity'); });
                        $totalValueStore = $storeConsignments->sum(function($c) { return $c->sales->sum('amount'); });
                    @endphp
                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                        <td class="py-4 px-4 font-medium text-gray-900">{{ $store->name }}</td>
                        <td class="py-4 px-4 text-center text-gray-700">{{ $totalConsignedStore }}</td>
                        <td class="py-4 px-4 text-center text-green-600 font-semibold">{{ $totalSoldStore }}</td>
                        <td class="py-4 px-4 text-center text-orange-600 font-semibold">{{ $totalReturnedStore }}</td>
                        <td class="py-4 px-4 text-center text-red-600 font-semibold">{{ $totalConsignedStore - $totalSoldStore - $totalReturnedStore }}</td>
                        <td class="py-4 px-4 text-right font-semibold text-gray-900">Rp {{ number_format($totalValueStore, 0, ',', '.') }}</td>
                        <td class="py-4 px-4 text-center">
                            <a href="#" class="text-blue-600 hover:text-blue-900 text-sm font-medium">Lihat</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Laporan Penjualan Harian</h2>
            @php
                $salesByDate = \App\Models\Sale::selectRaw('DATE(sale_date) as date, SUM(quantity) as qty, SUM(amount) as total')
                    ->groupBy('date')
                    ->orderBy('date', 'desc')
                    ->limit(10)
                    ->get();
            @endphp
            <div class="space-y-3">
                @foreach($salesByDate as $sale)
                <div class="flex justify-between border-b pb-2">
                    <span class="text-gray-700">{{ \Carbon\Carbon::parse($sale->date)->format('d M Y') }}</span>
                    <div class="text-right">
                        <p class="font-semibold">{{ $sale->qty }} unit</p>
                        <p class="text-sm text-gray-500">Rp {{ number_format($sale->total, 0, ',', '.') }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Laporan Retur Harian</h2>
            @php
                $returnsByDate = \App\Models\ReturnItem::selectRaw('DATE(return_date) as date, SUM(quantity) as qty')
                    ->with('consignment.product')
                    ->groupBy('date')
                    ->orderBy('date', 'desc')
                    ->limit(10)
                    ->get();
            @endphp
            <div class="space-y-3">
                @foreach($returnsByDate as $return)
                <div class="flex justify-between border-b pb-2">
                    <span class="text-gray-700">{{ \Carbon\Carbon::parse($return->date)->format('d M Y') }}</span>
                    <p class="font-semibold text-orange-600">{{ $return->qty }} unit</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
