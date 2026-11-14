@extends('layouts.app')

@section('header', 'Detail Titipan')
@section('subheader', 'Ringkasan stok, nilai, dan riwayat transaksi')

@section('content')
@php
    $user = auth()->user();
    $canReview = $consignment->isPending()
        && ($user->isAdmin() || ($user->isStoreManager() && $consignment->store_id === $user->store_id));
    $canChangeStatus = ($user->isAdmin() || ($user->isStoreManager() && $consignment->store_id === $user->store_id))
        && $consignment->settlement_id === null
        && array_key_exists($consignment->status, $statusOptions);
@endphp

<div class="mb-4">
    <a href="{{ route('consignments.index') }}" class="inline-flex items-center gap-1.5 link-muted">
        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        {{ $user->isStoreManager() ? 'Kembali ke titipan masuk' : 'Kembali ke daftar titipan' }}
    </a>
</div>

@if($consignment->isPending())
<div class="mb-4 flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3.5">
    <div>
        <p class="text-sm font-medium text-amber-800">Menunggu persetujuan toko</p>
        <p class="mt-0.5 text-xs text-amber-700">
            {{ $canReview
                ? 'Barang ini belum bisa dijual sampai Anda menerima ajuan titipan.'
                : 'Barang ini belum bisa dijual di toko sampai ajuan diterima.' }}
        </p>
    </div>
    @if($canReview)
    <div class="flex items-center gap-2">
        <form action="{{ route('consignments.approve', $consignment) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-success btn-sm">Terima Ajuan</button>
        </form>
        <form action="{{ route('consignments.reject', $consignment) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-secondary btn-sm" onclick="return confirm('Tolak ajuan titipan ini? Stok akan dikembalikan ke UMKM.')">Tolak</button>
        </form>
    </div>
    @endif
</div>
@elseif($consignment->status === 'rejected')
<div class="mb-4 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3.5">
    <p class="text-sm font-medium text-rose-700">Ajuan titipan ditolak</p>
    <p class="mt-0.5 text-xs text-rose-600">Stok barang sudah dikembalikan ke UMKM, jadi titipan ini tidak bisa dijual atau diretur.</p>
</div>
@endif

<div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
    <div class="space-y-4 lg:col-span-2">
        <div class="surface overflow-hidden">
            <div class="flex flex-wrap items-start justify-between gap-4 border-b border-hairline p-6">
                <div>
                    <h2 class="text-xl font-semibold tracking-tight text-ink-900">{{ $consignment->product->name }}</h2>
                    <p class="mt-1 text-sm text-ink-500">{{ $consignment->store->name }} · {{ $consignment->consignment_date->format('d M Y') }}</p>
                </div>
                <span class="badge {{ $consignment->statusBadgeClass() }}">
                    {{ $consignment->statusLabel() }}
                </span>
            </div>

            <div class="grid grid-cols-2 divide-x divide-y divide-hairline sm:grid-cols-4 sm:divide-y-0">
                <div class="p-5">
                    <p class="section-label">Titipan</p>
                    <p class="mt-2 text-2xl font-semibold tabular-nums tracking-tight text-ink-900">{{ $consignment->quantity }}</p>
                </div>
                <div class="p-5">
                    <p class="section-label">Terjual</p>
                    <p class="mt-2 text-2xl font-semibold tabular-nums tracking-tight text-ink-900">{{ $consignment->sales->sum('quantity') }}</p>
                </div>
                <div class="p-5">
                    <p class="section-label">Retur</p>
                    <p class="mt-2 text-2xl font-semibold tabular-nums tracking-tight text-ink-900">{{ $consignment->returns->sum('quantity') }}</p>
                </div>
                <div class="p-5">
                    <p class="section-label">Sisa stok</p>
                    <p class="mt-2 text-2xl font-semibold tabular-nums tracking-tight text-ink-900">
                        {{ $consignment->quantity - $consignment->sales->sum('quantity') - $consignment->returns->sum('quantity') }}
                    </p>
                </div>
            </div>

            <div class="border-t border-hairline px-6 py-4">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-ink-500">Harga satuan</span>
                    <span class="font-medium tabular-nums text-ink-900">Rp {{ number_format($consignment->product->price, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div class="surface overflow-hidden">
                <div class="border-b border-hairline px-5 py-4">
                    <h3 class="text-sm font-semibold text-ink-900">Riwayat Penjualan</h3>
                </div>
                @if($consignment->sales->count() > 0)
                    <div class="divide-y divide-hairline">
                        @foreach($consignment->sales as $sale)
                        <div class="px-5 py-3.5">
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-sm font-medium tabular-nums text-ink-900">{{ $sale->quantity }} pcs</span>
                                <span class="text-sm font-semibold tabular-nums text-brand-600">Rp {{ number_format($sale->amount, 0, ',', '.') }}</span>
                            </div>
                            <p class="mt-0.5 text-xs text-ink-400">{{ $sale->sale_date->format('d M Y') }}</p>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="px-5 py-10 text-center text-sm text-ink-500">Belum ada penjualan</p>
                @endif
            </div>

            <div class="surface overflow-hidden">
                <div class="border-b border-hairline px-5 py-4">
                    <h3 class="text-sm font-semibold text-ink-900">Riwayat Retur</h3>
                </div>
                @if($consignment->returns->count() > 0)
                    <div class="divide-y divide-hairline">
                        @foreach($consignment->returns as $return)
                        <div class="px-5 py-3.5">
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-sm font-medium tabular-nums text-ink-900">{{ $return->quantity }} pcs</span>
                                <span class="text-sm font-semibold tabular-nums text-rose-600">-Rp {{ number_format($return->quantity * $consignment->product->price, 0, ',', '.') }}</span>
                            </div>
                            <p class="mt-0.5 text-xs text-ink-500">{{ $return->reason ?? 'Tidak ada alasan' }}</p>
                            <p class="mt-0.5 text-xs text-ink-400">{{ $return->return_date->format('d M Y') }}</p>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="px-5 py-10 text-center text-sm text-ink-500">Belum ada retur</p>
                @endif
            </div>
        </div>
    </div>

    <div class="space-y-4">
        @if($canChangeStatus)
        <div class="surface overflow-hidden">
            <div class="border-b border-hairline px-5 py-4">
                <h3 class="text-sm font-semibold text-ink-900">Status Ajuan</h3>
                <p class="mt-0.5 text-xs text-ink-500">Klik status untuk langsung menyimpan</p>
            </div>
            <form action="{{ route('consignments.status', $consignment) }}" method="POST" class="space-y-1 p-3">
                @csrf
                @method('PATCH')
                @foreach($statusOptions as $value => $label)
                    @php $isCurrent = $consignment->status === $value; @endphp
                    <button type="submit" name="status" value="{{ $value }}"
                        @if($isCurrent) aria-current="true" @endif
                        class="flex w-full items-center justify-between gap-2 rounded-xl px-3 py-2 text-left text-[13px] font-medium transition {{ $isCurrent ? 'bg-ink-900 text-white' : 'text-ink-700 hover:bg-canvas' }}">
                        <span>{{ $label }}</span>
                        @if($isCurrent)
                            <svg class="size-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        @endif
                    </button>
                @endforeach
            </form>
            <p class="px-5 pb-4 text-xs leading-relaxed text-ink-500">
                Status <span class="font-medium text-ink-700">Ditolak</span> mengembalikan stok ke UMKM; mengaktifkan kembali memotong stok lagi.
            </p>
        </div>
        @endif

        <div class="surface overflow-hidden">
            <div class="border-b border-hairline px-5 py-4">
                <h3 class="text-sm font-semibold text-ink-900">Ringkasan Nilai</h3>
            </div>
            <div class="space-y-3 p-5 text-sm">
                <div class="flex items-center justify-between gap-3">
                    <span class="text-ink-500">Total penjualan</span>
                    <span class="font-medium tabular-nums text-ink-900">Rp {{ number_format($consignment->sales->sum('amount'), 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between gap-3">
                    <span class="text-ink-500">Nilai retur</span>
                    <span class="font-medium tabular-nums text-rose-600">-Rp {{ number_format($consignment->returns->sum('quantity') * $consignment->product->price, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between gap-3 border-t border-hairline pt-3">
                    <span class="font-medium text-ink-900">Nilai kotor</span>
                    <span class="text-lg font-semibold tabular-nums tracking-tight text-brand-600">
                        Rp {{ number_format($consignment->sales->sum('amount') - ($consignment->returns->sum('quantity') * $consignment->product->price), 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-hairline bg-canvas/60 p-5">
            <p class="text-xs leading-relaxed text-ink-500">
                Nilai kotor dihitung dari total penjualan dikurangi nilai barang yang diretur, sebelum komisi toko.
            </p>
        </div>
    </div>
</div>
@endsection
