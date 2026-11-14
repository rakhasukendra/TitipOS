@extends('layouts.app')

@section('header', 'Settlement')
@section('subheader', 'Penyelesaian pembayaran antara toko dan UMKM')

@section('content')
@php
    $metrics = [
        ['label' => 'Settlement', 'value' => $stats['total'] . ' dokumen'],
        ['label' => 'Sudah dibayar', 'value' => 'Rp ' . number_format($stats['paid'], 0, ',', '.')],
        ['label' => 'Menunggu', 'value' => 'Rp ' . number_format($stats['pending'], 0, ',', '.')],
    ];
@endphp

<div class="surface overflow-hidden">
    <x-list-header eyebrow="Settlement" title="Daftar Settlement" :metrics="$metrics">
        <x-slot:actions>
        <a href="{{ route('settlements.create') }}" class="btn btn-primary btn-sm">
            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Buat Settlement
        </a>
        </x-slot:actions>
    </x-list-header>
    <div class="overflow-x-auto">
        <table class="w-full min-w-[980px] text-left">
            <thead class="border-b border-hairline bg-canvas/60">
                <tr>
                    <th class="th">Toko</th>
                    <th class="th">UMKM</th>
                    <th class="th">Total Penjualan</th>
                    <th class="th">Total Retur</th>
                    <th class="th">Netto</th>
                    <th class="th">Status</th>
                    <th class="th text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-hairline">
                @forelse($settlements as $settlement)
                <tr class="transition hover:bg-canvas/60">
                    <td class="td font-medium text-ink-900">{{ $settlement->store->name }}</td>
                    <td class="td">{{ $settlement->umkm->name }}</td>
                    <td class="td tabular-nums">Rp {{ number_format($settlement->total_sales, 0, ',', '.') }}</td>
                    <td class="td tabular-nums text-rose-600">-Rp {{ number_format($settlement->total_returns, 0, ',', '.') }}</td>
                    <td class="td font-semibold tabular-nums text-brand-600">Rp {{ number_format($settlement->net_amount, 0, ',', '.') }}</td>
                    <td class="td">
                        <span class="badge {{ $settlement->settled_at ? 'badge-success' : 'badge-warning' }}">
                            {{ $settlement->settled_at ? 'Settled' : 'Pending' }}
                        </span>
                    </td>
                    <td class="td">
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('settlements.show', $settlement) }}" class="link-muted">Detail</a>
                            @if(!$settlement->settled_at)
                                <form action="{{ route('settlements.destroy', $settlement) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="link-danger" onclick="return confirm('Yakin hapus settlement ini?')">Hapus</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-16 text-center">
                        <div class="mx-auto flex size-12 items-center justify-center rounded-full border border-dashed border-hairline text-ink-400">
                            <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <p class="mt-3 text-sm font-medium text-ink-900">Belum ada settlement</p>
                        <p class="mt-1 text-xs text-ink-500">Buat settlement untuk menutup periode penjualan toko.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6">
    {{ $settlements->links() }}
</div>
@endsection
