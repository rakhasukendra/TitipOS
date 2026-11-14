@extends('layouts.app')

@section('header', 'Detail Produk')
@section('subheader', 'Informasi lengkap dan riwayat titipan produk')

@section('content')
<div class="max-w-4xl">
    <a href="{{ route('products.index') }}" class="mb-4 inline-flex items-center gap-1.5 link-muted">
        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Kembali ke daftar produk
    </a>

    <div class="surface overflow-hidden">
        <div class="flex flex-wrap items-start justify-between gap-4 border-b border-hairline p-6 sm:p-8">
            <div>
                <h2 class="text-xl font-semibold tracking-tight text-ink-900">{{ $product->name }}</h2>
                <p class="mt-1 text-sm text-ink-500">{{ $product->umkm->name ?? '-' }}</p>
            </div>
            <a href="{{ route('products.edit', $product) }}" class="btn btn-secondary btn-sm">Edit Produk</a>
        </div>

        <div class="grid grid-cols-1 divide-y divide-hairline sm:grid-cols-2 sm:divide-y-0">
            <div class="p-6 sm:p-8">
                <p class="section-label">Harga satuan</p>
                <p class="mt-2 text-2xl font-semibold tabular-nums tracking-tight text-ink-900">
                    Rp {{ number_format($product->price, 0, ',', '.') }}
                </p>
            </div>
            <div class="border-hairline p-6 sm:border-l sm:p-8">
                <p class="section-label">Stok tersedia</p>
                <p class="mt-2 text-2xl font-semibold tabular-nums tracking-tight text-ink-900">
                    {{ $product->stock }} <span class="text-sm font-normal text-ink-500">pcs</span>
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 border-t border-hairline p-6 sm:grid-cols-2 sm:p-8">
            <div>
                <p class="section-label">Deskripsi</p>
                <p class="mt-2 text-sm leading-relaxed text-ink-700">{{ $product->description ?? 'Tidak ada deskripsi' }}</p>
            </div>
            <div>
                <p class="section-label">Tanggal dibuat</p>
                <p class="mt-2 text-sm text-ink-700">{{ $product->created_at->format('d M Y H:i') }}</p>
            </div>
        </div>
    </div>

    @if($product->consignments->count() > 0)
    <div class="mt-4 surface overflow-hidden">
        <div class="border-b border-hairline px-5 py-4">
            <h3 class="text-sm font-semibold text-ink-900">Riwayat Titipan</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[560px] text-left">
                <thead class="border-b border-hairline bg-canvas/60">
                    <tr>
                        <th class="th">Toko</th>
                        <th class="th">Tanggal</th>
                        <th class="th">Jumlah</th>
                        <th class="th text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-hairline">
                    @foreach($product->consignments as $consignment)
                    <tr class="transition hover:bg-canvas/60">
                        <td class="td font-medium text-ink-900">{{ $consignment->store->name }}</td>
                        <td class="td">{{ $consignment->consignment_date->format('d M Y') }}</td>
                        <td class="td tabular-nums">{{ $consignment->quantity }} <span class="text-xs text-ink-500">pcs</span></td>
                        <td class="td text-right">
                            <span class="badge {{ $consignment->statusBadgeClass() }}">
                                {{ $consignment->statusLabel() }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection
