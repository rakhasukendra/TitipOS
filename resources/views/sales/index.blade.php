@extends('layouts.app')

@section('header', 'Penjualan')
@section('subheader', 'Transaksi penjualan dari barang titipan')

@section('content')
@php
    $metrics = [
        ['label' => 'Transaksi', 'value' => $stats['total'] . ' kali'],
        ['label' => 'Unit terjual', 'value' => $stats['units'] . ' pcs'],
        ['label' => 'Omzet', 'value' => 'Rp ' . number_format($stats['revenue'], 0, ',', '.')],
        ['label' => 'Rata-rata', 'value' => 'Rp ' . number_format($stats['average'], 0, ',', '.')],
    ];
@endphp

<div class="surface overflow-hidden">
    <x-list-header eyebrow="Penjualan" title="Riwayat Penjualan" :metrics="$metrics">
        <x-slot:actions>
        @if(in_array(auth()->user()->role->slug ?? '', ['store', 'admin']))
        <a href="{{ route('sales.create') }}" class="btn btn-primary btn-sm">
            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Catat Penjualan
        </a>
        @endif
        </x-slot:actions>
    </x-list-header>
    <div class="overflow-x-auto">
        <table class="w-full min-w-[820px] text-left">
            <thead class="border-b border-hairline bg-canvas/60">
                <tr>
                    <th class="th">Produk</th>
                    <th class="th">Toko</th>
                    <th class="th">Jumlah</th>
                    <th class="th">Total</th>
                    <th class="th">Tanggal</th>
                    <th class="th text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-hairline">
                @forelse($sales as $sale)
                <tr class="transition hover:bg-canvas/60">
                    <td class="td font-medium text-ink-900">{{ $sale->consignment->product->name }}</td>
                    <td class="td">{{ $sale->consignment->store->name }}</td>
                    <td class="td tabular-nums">{{ $sale->quantity }} <span class="text-xs text-ink-500">pcs</span></td>
                    <td class="td font-semibold tabular-nums text-brand-600">Rp {{ number_format($sale->amount, 0, ',', '.') }}</td>
                    <td class="td text-ink-500">{{ $sale->sale_date->format('d M Y') }}</td>
                    <td class="td">
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('sales.show', $sale) }}" class="link-muted">Detail</a>
                            @if(in_array(auth()->user()->role->slug ?? '', ['store', 'admin']))
                            <form action="{{ route('sales.destroy', $sale) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="link-danger" onclick="return confirm('Yakin hapus penjualan ini?')">Hapus</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-16 text-center">
                        <div class="mx-auto flex size-12 items-center justify-center rounded-full border border-dashed border-hairline text-ink-400">
                            <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                        </div>
                        <p class="mt-3 text-sm font-medium text-ink-900">Belum ada penjualan</p>
                        <p class="mt-1 text-xs text-ink-500">Setiap penjualan yang dicatat akan muncul di sini.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6">
    {{ $sales->links() }}
</div>
@endsection
