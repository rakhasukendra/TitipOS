@extends('layouts.app')

@section('header', 'Laporan')
@section('subheader', 'Rekap titipan, penjualan, dan retur seluruh toko')

@section('content')
@php
    $totalConsigned = \App\Models\Consignment::sum('quantity');
    $totalSold = \App\Models\Sale::sum('quantity');
    $totalReturned = \App\Models\ReturnItem::sum('quantity');

    $metrics = [
        ['label' => 'Titipan', 'value' => $totalConsigned . ' unit'],
        ['label' => 'Terjual', 'value' => $totalSold . ' unit'],
        ['label' => 'Retur', 'value' => $totalReturned . ' unit'],
        ['label' => 'Sisa', 'value' => ($totalConsigned - $totalSold - $totalReturned) . ' unit'],
    ];
@endphp

<div class="surface overflow-hidden">
    <x-list-header eyebrow="Laporan" title="Ringkasan Operasional" :metrics="$metrics" />
</div>

<div class="mt-4 surface overflow-hidden">
    <div class="border-b border-hairline px-5 py-4">
        <h2 class="text-sm font-semibold text-ink-900">Detail Titipan per Toko</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full min-w-[860px] text-left">
            <thead class="border-b border-hairline bg-canvas/60">
                <tr>
                    <th class="th">Toko</th>
                    <th class="th text-center">Titipan</th>
                    <th class="th text-center">Terjual</th>
                    <th class="th text-center">Retur</th>
                    <th class="th text-center">Sisa</th>
                    <th class="th text-right">Nilai Penjualan</th>
                    <th class="th text-right">Detail</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-hairline">
                @foreach(\App\Models\Store::with('consignments.product', 'consignments.sales', 'consignments.returns')->get() as $store)
                    @php
                        $storeConsignments = $store->consignments;
                        $totalConsignedStore = $storeConsignments->sum('quantity');
                        $totalSoldStore = $storeConsignments->sum(function($c) { return $c->sales->sum('quantity'); });
                        $totalReturnedStore = $storeConsignments->sum(function($c) { return $c->returns->sum('quantity'); });
                        $totalValueStore = $storeConsignments->sum(function($c) { return $c->sales->sum('amount'); });
                    @endphp
                    <tr class="transition hover:bg-canvas/60">
                        <td class="td font-medium text-ink-900">{{ $store->name }}</td>
                        <td class="td text-center tabular-nums">{{ $totalConsignedStore }}</td>
                        <td class="td text-center font-semibold tabular-nums text-brand-600">{{ $totalSoldStore }}</td>
                        <td class="td text-center font-semibold tabular-nums text-rose-600">{{ $totalReturnedStore }}</td>
                        <td class="td text-center font-semibold tabular-nums text-ink-900">{{ $totalConsignedStore - $totalSoldStore - $totalReturnedStore }}</td>
                        <td class="td text-right font-semibold tabular-nums text-ink-900">Rp {{ number_format($totalValueStore, 0, ',', '.') }}</td>
                        <td class="td text-right">
                            <a href="#" class="link-muted">Lihat</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-2">
    <div class="surface overflow-hidden">
        <div class="border-b border-hairline px-5 py-4">
            <h2 class="text-sm font-semibold text-ink-900">Laporan Penjualan Harian</h2>
        </div>
        @php
            $salesByDate = \App\Models\Sale::selectRaw('DATE(sale_date) as date, SUM(quantity) as qty, SUM(amount) as total')
                ->groupBy('date')
                ->orderBy('date', 'desc')
                ->limit(10)
                ->get();
        @endphp
        @if($salesByDate->isEmpty())
            <p class="px-5 py-10 text-center text-sm text-ink-500">Belum ada data penjualan</p>
        @else
            <div class="divide-y divide-hairline">
                @foreach($salesByDate as $sale)
                <div class="flex items-center justify-between gap-3 px-5 py-3.5">
                    <span class="text-sm text-ink-700">{{ \Carbon\Carbon::parse($sale->date)->format('d M Y') }}</span>
                    <div class="text-right">
                        <p class="text-sm font-medium tabular-nums text-ink-900">{{ $sale->qty }} unit</p>
                        <p class="text-xs tabular-nums text-ink-500">Rp {{ number_format($sale->total, 0, ',', '.') }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="surface overflow-hidden">
        <div class="border-b border-hairline px-5 py-4">
            <h2 class="text-sm font-semibold text-ink-900">Laporan Retur Harian</h2>
        </div>
        @php
            $returnsByDate = \App\Models\ReturnItem::selectRaw('DATE(return_date) as date, SUM(quantity) as qty')
                ->with('consignment.product')
                ->groupBy('date')
                ->orderBy('date', 'desc')
                ->limit(10)
                ->get();
        @endphp
        @if($returnsByDate->isEmpty())
            <p class="px-5 py-10 text-center text-sm text-ink-500">Belum ada data retur</p>
        @else
            <div class="divide-y divide-hairline">
                @foreach($returnsByDate as $return)
                <div class="flex items-center justify-between gap-3 px-5 py-3.5">
                    <span class="text-sm text-ink-700">{{ \Carbon\Carbon::parse($return->date)->format('d M Y') }}</span>
                    <p class="text-sm font-medium tabular-nums text-rose-600">{{ $return->qty }} unit</p>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
