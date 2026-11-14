@extends('layouts.app')

@section('header', 'Produk')
@section('subheader', 'Katalog produk yang Anda titipkan ke toko')

@section('content')
@php
    $metrics = [
        ['label' => 'Produk', 'value' => $stats['total'] . ' item'],
        ['label' => 'Stok', 'value' => $stats['stock'] . ' pcs'],
        ['label' => 'Nilai stok', 'value' => 'Rp ' . number_format($stats['value'], 0, ',', '.')],
    ];
@endphp

<div class="surface overflow-hidden">
    <x-list-header eyebrow="Katalog" title="Daftar Produk" :metrics="$metrics">
        <x-slot:actions>
        <a href="{{ route('products.create') }}" class="btn btn-primary btn-sm">
            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Produk
        </a>
        </x-slot:actions>
    </x-list-header>
    <div class="overflow-x-auto">
        <table class="w-full min-w-[720px] text-left">
            <thead class="border-b border-hairline bg-canvas/60">
                <tr>
                    <th class="th">Nama</th>
                    @if(auth()->user()->isAdmin())
                    <th class="th">Pemilik</th>
                    @endif
                    <th class="th">Harga</th>
                    <th class="th">Stok</th>
                    <th class="th text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-hairline">
                @forelse($products as $product)
                <tr class="transition hover:bg-canvas/60">
                    <td class="td">
                        <p class="font-medium text-ink-900">{{ $product->name }}</p>
                        <p class="mt-0.5 text-xs text-ink-500">{{ Str::limit($product->description, 50) }}</p>
                    </td>
                    @if(auth()->user()->isAdmin())
                    <td class="td">{{ $product->umkm->name ?? '-' }}</td>
                    @endif
                    <td class="td font-medium tabular-nums text-ink-900">
                        Rp {{ number_format($product->price, 0, ',', '.') }}
                    </td>
                    <td class="td tabular-nums">
                        {{ $product->stock }} <span class="text-xs text-ink-500">pcs</span>
                    </td>
                    <td class="td">
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('products.show', $product) }}" class="link-muted">Lihat</a>
                            <a href="{{ route('products.edit', $product) }}" class="link-muted">Edit</a>
                            <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="link-danger" onclick="return confirm('Yakin hapus produk ini?')">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="{{ auth()->user()->isAdmin() ? 5 : 4 }}" class="px-5 py-16 text-center">
                        <div class="mx-auto flex size-12 items-center justify-center rounded-full border border-dashed border-hairline text-ink-400">
                            <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                        <p class="mt-3 text-sm font-medium text-ink-900">Belum ada produk</p>
                        <p class="mt-1 text-xs text-ink-500">Tambahkan produk pertama Anda untuk mulai menitipkan barang.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6">
    {{ $products->links() }}
</div>
@endsection
