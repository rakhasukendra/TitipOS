@extends('layouts.app')

@section('header', 'Retur')
@section('subheader', 'Barang titipan yang dikembalikan dari toko')

@section('content')
@php
    $metrics = [
        ['label' => 'Retur', 'value' => $stats['total'] . ' catatan'],
        ['label' => 'Unit diretur', 'value' => $stats['units'] . ' pcs'],
        ['label' => 'Nilai retur', 'value' => 'Rp ' . number_format($stats['value'], 0, ',', '.')],
    ];
@endphp

<div class="surface overflow-hidden">
    <x-list-header eyebrow="Retur" title="Riwayat Retur" :metrics="$metrics">
        <x-slot:actions>
        @if(in_array(auth()->user()->role->slug ?? '', ['store', 'admin']))
        <a href="{{ route('returns.create') }}" class="btn btn-primary btn-sm">
            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Catat Retur
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
                    <th class="th">Alasan</th>
                    <th class="th">Tanggal</th>
                    <th class="th text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-hairline">
                @forelse($returns as $return)
                <tr class="transition hover:bg-canvas/60">
                    <td class="td font-medium text-ink-900">{{ $return->consignment->product->name }}</td>
                    <td class="td">{{ $return->consignment->store->name }}</td>
                    <td class="td tabular-nums">{{ $return->quantity }} <span class="text-xs text-ink-500">pcs</span></td>
                    <td class="td">{{ $return->reason ?? '-' }}</td>
                    <td class="td text-ink-500">{{ $return->return_date->format('d M Y') }}</td>
                    <td class="td">
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('returns.show', $return) }}" class="link-muted">Detail</a>
                            @if(in_array(auth()->user()->role->slug ?? '', ['store', 'admin']))
                            <form action="{{ route('returns.destroy', $return) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="link-danger" onclick="return confirm('Yakin hapus retur ini?')">Hapus</button>
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                            </svg>
                        </div>
                        <p class="mt-3 text-sm font-medium text-ink-900">Belum ada retur</p>
                        <p class="mt-1 text-xs text-ink-500">Retur yang dicatat akan tampil di sini beserta alasannya.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6">
    {{ $returns->links() }}
</div>
@endsection
