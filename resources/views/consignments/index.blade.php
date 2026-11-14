@extends('layouts.app')

@php
    $user = auth()->user();
    $isStore = $user->isStoreManager();
    $isAdmin = $user->isAdmin();
    $canCreate = $user->isUmkm() || $isAdmin;
@endphp

@section('header', $isStore ? 'Titipan Masuk' : ($isAdmin ? 'Semua Titipan' : 'Titipan Saya'))
@section('subheader', $isStore
    ? 'Ajuan barang titipan dari UMKM untuk toko Anda'
    : ($isAdmin ? 'Seluruh titipan UMKM di semua toko' : 'Barang Anda yang sedang dititipkan di toko'))

@section('content')
@php
    $metrics = [
        ['label' => 'Total', 'value' => $stats['total'] . ' pcs'],
        ['label' => 'Menunggu', 'value' => $stats['pending'] . ' pcs'],
        ['label' => 'Aktif', 'value' => $stats['active'] . ' pcs'],
        ['label' => 'Terjual', 'value' => $stats['sold'] . ' pcs'],
    ];
@endphp

<div class="surface overflow-hidden">
    <x-list-header
        eyebrow="Titipan"
        :title="$isStore ? 'Ajuan & Titipan Masuk' : ($isAdmin ? 'Daftar Semua Titipan' : 'Daftar Titipan Saya')"
        :metrics="$metrics"
    >
        <x-slot:actions>
        @if($canCreate)
        <a href="{{ route('consignments.create') }}" class="btn btn-primary btn-sm">
            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Titipan
        </a>
        @endif
        </x-slot:actions>
    </x-list-header>
    <div class="overflow-x-auto">
        <table class="w-full min-w-[720px] text-left">
            <thead class="border-b border-hairline bg-canvas/60">
                <tr>
                    <th class="th">Produk</th>
                    <th class="th">{{ $isStore ? 'UMKM' : 'Toko' }}</th>
                    <th class="th">Jumlah</th>
                    <th class="th">Status</th>
                    <th class="th text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-hairline">
                @forelse($consignments as $consignment)
                <tr class="transition hover:bg-canvas/60">
                    <td class="td">
                        <p class="font-medium text-ink-900">{{ $consignment->product->name }}</p>
                        <p class="mt-0.5 text-xs text-ink-500">{{ $consignment->consignment_date->format('d M Y') }}</p>
                    </td>
                    <td class="td">{{ $isStore ? $consignment->product->umkm->name ?? '-' : $consignment->store->name }}</td>
                    <td class="td tabular-nums">{{ $consignment->quantity }} <span class="text-xs text-ink-500">pcs</span></td>
                    <td class="td">
                        @if(($isStore || $isAdmin) && $consignment->settlement_id === null && array_key_exists($consignment->status, $statusOptions))
                        <form action="{{ route('consignments.status', $consignment) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <select name="status" aria-label="Ubah status titipan" title="Ubah status titipan"
                                onchange="if (this.value === 'rejected' && ! confirm('Tolak ajuan titipan ini? Stok akan dikembalikan ke UMKM.')) { this.value = '{{ $consignment->status }}'; return; } this.form.submit();"
                                class="badge status-select {{ $consignment->statusBadgeClass() }}">
                                @foreach($statusOptions as $value => $label)
                                    <option value="{{ $value }}" {{ $consignment->status === $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </form>
                        @else
                        <span class="badge {{ $consignment->statusBadgeClass() }}">
                            {{ $consignment->statusLabel() }}
                        </span>
                        @endif
                    </td>
                    <td class="td">
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('consignments.show', $consignment) }}" class="link-muted">Detail</a>
                            @if($consignment->isPending() && ($isStore || $isAdmin))
                            <form action="{{ route('consignments.approve', $consignment) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm">Terima</button>
                            </form>
                            @endif
                            @if($user->isUmkm() || $isAdmin)
                            <a href="{{ route('consignments.edit', $consignment) }}" class="link-muted">Edit</a>
                            <form action="{{ route('consignments.destroy', $consignment) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="link-danger" onclick="return confirm('Yakin hapus titipan ini?')">Hapus</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-5 py-16 text-center">
                        <div class="mx-auto flex size-12 items-center justify-center rounded-full border border-dashed border-hairline text-ink-400">
                            <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        @if($isStore)
                            <p class="mt-3 text-sm font-medium text-ink-900">Belum ada titipan masuk</p>
                            <p class="mt-1 text-xs text-ink-500">Ajuan barang dari UMKM akan muncul di sini untuk Anda terima atau tolak.</p>
                        @else
                            <p class="mt-3 text-sm font-medium text-ink-900">Belum ada titipan</p>
                            <p class="mt-1 text-xs text-ink-500">
                                {{ $isAdmin ? 'Titipan yang diajukan UMKM akan muncul di sini.' : 'Catat titipan pertama Anda agar stok di toko tercatat rapi.' }}
                            </p>
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6">
    {{ $consignments->links() }}
</div>
@endsection
