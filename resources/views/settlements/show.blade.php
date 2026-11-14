@extends('layouts.app')

@section('header', 'Detail Settlement')
@section('subheader', 'Rincian perhitungan pembayaran ke UMKM')

@section('content')
<div class="max-w-5xl">
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('settlements.index') }}" class="inline-flex items-center gap-1.5 link-muted">
            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke daftar settlement
        </a>
        <span class="badge {{ $settlement->settled_at ? 'badge-success' : 'badge-warning' }}">
            {{ $settlement->settled_at ? 'Settled' : 'Pending' }}
        </span>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="surface p-5">
            <p class="section-label">Toko</p>
            <p class="mt-2 text-base font-semibold text-ink-900">{{ $settlement->store->name }}</p>
            <p class="mt-1 text-xs text-ink-500">{{ $settlement->store->address }}</p>
        </div>

        <div class="surface p-5">
            <p class="section-label">UMKM</p>
            <p class="mt-2 text-base font-semibold text-ink-900">{{ $settlement->umkm->name }}</p>
            <p class="mt-1 text-xs text-ink-500">{{ $settlement->umkm->email }}</p>
        </div>

        <div class="surface p-5">
            <p class="section-label">Tanggal settlement</p>
            <p class="mt-2 text-base font-semibold text-ink-900">{{ $settlement->settlement_date->format('d M Y') }}</p>
            @if($settlement->settled_at)
                <p class="mt-1 text-xs text-ink-500">Settled {{ $settlement->settled_at->format('d M Y H:i') }}</p>
            @endif
        </div>
    </div>

    <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-3">
        <div class="space-y-4 lg:col-span-2">
            <div class="surface overflow-hidden">
                <div class="border-b border-hairline px-5 py-4">
                    <h2 class="text-sm font-semibold text-ink-900">Ringkasan Keuangan</h2>
                </div>

                <div class="grid grid-cols-2 divide-x divide-y divide-hairline lg:grid-cols-4 lg:divide-y-0">
                    <div class="p-5">
                        <p class="section-label">Penjualan</p>
                        <p class="mt-2 text-lg font-semibold tabular-nums tracking-tight text-ink-900">Rp {{ number_format($settlement->total_sales, 0, ',', '.') }}</p>
                    </div>
                    <div class="p-5">
                        <p class="section-label">Retur</p>
                        <p class="mt-2 text-lg font-semibold tabular-nums tracking-tight text-rose-600">-Rp {{ number_format($settlement->total_returns, 0, ',', '.') }}</p>
                    </div>
                    <div class="p-5">
                        <p class="section-label">Komisi {{ $settlement->commission_rate }}%</p>
                        <p class="mt-2 text-lg font-semibold tabular-nums tracking-tight text-ink-900">
                            -Rp {{ number_format(($settlement->total_sales - $settlement->total_returns) * ($settlement->commission_rate / 100), 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="bg-brand-50/60 p-5">
                        <p class="section-label text-brand-700/70">Netto UMKM</p>
                        <p class="mt-2 text-lg font-semibold tabular-nums tracking-tight text-brand-700">Rp {{ number_format($settlement->net_amount, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <div class="surface overflow-hidden">
                <div class="border-b border-hairline px-5 py-4">
                    <h2 class="text-sm font-semibold text-ink-900">Detail Titipan</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[620px] text-left">
                        <thead class="border-b border-hairline bg-canvas/60">
                            <tr>
                                <th class="th">Produk</th>
                                <th class="th text-center">Titipan</th>
                                <th class="th text-center">Terjual</th>
                                <th class="th text-center">Retur</th>
                                <th class="th text-right">Nilai</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-hairline">
                            @foreach($consignments as $consignment)
                            <tr class="transition hover:bg-canvas/60">
                                <td class="td">
                                    <p class="font-medium text-ink-900">{{ $consignment->product->name }}</p>
                                    <p class="mt-0.5 text-xs text-ink-500">Rp {{ number_format($consignment->product->price, 0, ',', '.') }}/pcs</p>
                                </td>
                                <td class="td text-center tabular-nums">{{ $consignment->quantity }}</td>
                                <td class="td text-center font-semibold tabular-nums text-brand-600">{{ $consignment->sales->sum('quantity') }}</td>
                                <td class="td text-center font-semibold tabular-nums text-rose-600">{{ $consignment->returns->sum('quantity') }}</td>
                                <td class="td text-right font-semibold tabular-nums text-ink-900">
                                    Rp {{ number_format($consignment->sales->sum('amount'), 0, ',', '.') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="space-y-4">
            <div class="surface overflow-hidden">
                <div class="border-b border-hairline px-5 py-4">
                    <h3 class="text-sm font-semibold text-ink-900">Perhitungan</h3>
                </div>
                <div class="space-y-3 p-5 text-sm">
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-ink-500">Penjualan kotor</span>
                        <span class="tabular-nums text-ink-900">Rp {{ number_format($settlement->total_sales, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-ink-500">Dikurangi retur</span>
                        <span class="tabular-nums text-rose-600">-Rp {{ number_format($settlement->total_returns, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-3 border-t border-hairline pt-3">
                        <span class="font-medium text-ink-900">Subtotal</span>
                        <span class="font-medium tabular-nums text-ink-900">Rp {{ number_format($settlement->total_sales - $settlement->total_returns, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-ink-500">Komisi toko ({{ $settlement->commission_rate }}%)</span>
                        <span class="tabular-nums text-rose-600">-Rp {{ number_format(($settlement->total_sales - $settlement->total_returns) * ($settlement->commission_rate / 100), 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-end justify-between gap-3 border-t border-hairline pt-3">
                        <span class="font-medium text-ink-900">Netto</span>
                        <span class="text-xl font-semibold tabular-nums tracking-tight text-brand-600">Rp {{ number_format($settlement->net_amount, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <div class="surface p-5">
                <p class="text-xs leading-relaxed text-ink-500">
                    Netto adalah jumlah yang dibayarkan ke UMKM setelah dikurangi retur dan komisi toko.
                </p>
                @if(!$settlement->settled_at)
                <form action="{{ route('settlements.update', $settlement) }}" method="POST" class="mt-4">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="btn btn-success w-full">Tandai Settled</button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
