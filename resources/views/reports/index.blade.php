@extends('layouts.app')

@section('header', 'Laporan')
@section('subheader', auth()->user()->isAdmin()
    ? 'Rekap titipan, penjualan, dan retur seluruh toko'
    : (auth()->user()->isUmkm()
        ? 'Rekap titipan, penjualan, dan retur produk Anda'
        : 'Rekap titipan, penjualan, dan retur toko Anda'))

@section('content')

<div class="surface overflow-hidden">
    <x-list-header eyebrow="Laporan" title="Ringkasan Operasional" :metrics="$metrics">
        <x-slot:actions>
            <a href="{{ route('reports.export') }}" class="btn btn-secondary btn-sm">
                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                <span>Export Excel</span>
            </a>
        </x-slot:actions>
    </x-list-header>
</div>

<div class="mt-4 surface overflow-hidden">
    <div class="border-b border-hairline px-5 py-4">
        <h2 class="text-sm font-semibold text-ink-900">
            {{ auth()->user()->isAdmin() ? 'Detail Titipan per Toko' : (auth()->user()->isUmkm() ? 'Detail Titipan Produk Anda' : 'Detail Titipan Toko') }}
        </h2>
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
                @forelse($storeRows as $row)
                    <tr class="transition hover:bg-canvas/60">
                        <td class="td font-medium text-ink-900">{{ $row['name'] }}</td>
                        <td class="td text-center tabular-nums">{{ $row['consigned'] }}</td>
                        <td class="td text-center font-semibold tabular-nums text-brand-600">{{ $row['sold'] }}</td>
                        <td class="td text-center font-semibold tabular-nums text-rose-600">{{ $row['returned'] }}</td>
                        <td class="td text-center font-semibold tabular-nums text-ink-900">{{ $row['remaining'] }}</td>
                        <td class="td text-right font-semibold tabular-nums text-ink-900">Rp {{ number_format($row['revenue'], 0, ',', '.') }}</td>
                        <td class="td text-right">
                            <a href="#" class="link-muted">Lihat</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="td text-center text-ink-500">Belum ada data toko</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-2">
    <div class="surface overflow-hidden">
        <div class="border-b border-hairline px-5 py-4">
            <h2 class="text-sm font-semibold text-ink-900">Laporan Penjualan Harian</h2>
        </div>
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
