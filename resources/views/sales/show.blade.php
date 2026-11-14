@extends('layouts.app')

@section('header', 'Detail Penjualan')
@section('subheader', 'Rincian transaksi penjualan barang titipan')

@section('content')
<div class="max-w-3xl">
    <a href="{{ route('sales.index') }}" class="mb-4 inline-flex items-center gap-1.5 link-muted">
        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Kembali ke daftar penjualan
    </a>

    <div class="surface overflow-hidden">
        <div class="border-b border-hairline p-6 sm:p-8">
            <p class="section-label">Total transaksi</p>
            <p class="mt-2 text-3xl font-semibold tabular-nums tracking-tight text-ink-900">
                Rp {{ number_format($sale->amount, 0, ',', '.') }}
            </p>
            <p class="mt-1 text-sm text-ink-500">{{ $sale->sale_date->format('d M Y') }}</p>
        </div>

        <dl class="divide-y divide-hairline">
            <div class="flex items-center justify-between gap-3 px-6 py-4 sm:px-8">
                <dt class="text-sm text-ink-500">Produk</dt>
                <dd class="text-sm font-medium text-ink-900">{{ $sale->consignment->product->name }}</dd>
            </div>
            <div class="flex items-center justify-between gap-3 px-6 py-4 sm:px-8">
                <dt class="text-sm text-ink-500">Toko</dt>
                <dd class="text-sm font-medium text-ink-900">{{ $sale->consignment->store->name }}</dd>
            </div>
            <div class="flex items-center justify-between gap-3 px-6 py-4 sm:px-8">
                <dt class="text-sm text-ink-500">Harga satuan</dt>
                <dd class="text-sm font-medium tabular-nums text-ink-900">Rp {{ number_format($sale->consignment->product->price, 0, ',', '.') }}</dd>
            </div>
            <div class="flex items-center justify-between gap-3 px-6 py-4 sm:px-8">
                <dt class="text-sm text-ink-500">Jumlah terjual</dt>
                <dd class="text-sm font-medium tabular-nums text-ink-900">{{ $sale->quantity }} pcs</dd>
            </div>
        </dl>

        <div class="flex flex-wrap items-center gap-2 border-t border-hairline bg-canvas/40 px-6 py-4 sm:px-8">
            <a href="{{ route('consignments.show', $sale->consignment) }}" class="btn btn-secondary btn-sm">Lihat titipan</a>
            <a href="{{ route('sales.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
        </div>
    </div>
</div>
@endsection
