@extends('layouts.app')

@section('header', 'Catat Penjualan')
@section('subheader', 'Rekam barang titipan yang terjual di toko')

@section('content')
<div class="max-w-2xl">
    <a href="{{ route('sales.index') }}" class="mb-4 inline-flex items-center gap-1.5 link-muted">
        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Kembali ke daftar penjualan
    </a>

    <div class="surface overflow-hidden">
        <div class="border-b border-hairline px-6 py-5 sm:px-8">
            <p class="section-label">Formulir</p>
            <h2 class="mt-1.5 text-lg font-semibold tracking-tight text-ink-900">Catat Penjualan</h2>
            <p class="mt-1 text-sm text-ink-500">Rekam barang titipan yang terjual di toko Anda.</p>
        </div>
        <form action="{{ route('sales.store') }}" method="POST" class="space-y-5 p-6 sm:p-8">
            @csrf

            <div>
                <label for="consignment_id" class="label">Titipan</label>
                <select id="consignment_id" name="consignment_id" required class="field field-select @error('consignment_id') field-error @enderror">
                    <option value="">Pilih titipan</option>
                    @php $sellableCount = 0; @endphp
                    @foreach($consignments as $consignment)
                        @php
                            $sold = $consignment->sales->sum('quantity');
                            $remaining = $consignment->quantity - $sold;
                            $sellableCount += $remaining > 0 ? 1 : 0;
                        @endphp
                        <option value="{{ $consignment->id }}"
                            {{ old('consignment_id') == $consignment->id ? 'selected' : '' }}
                            data-remaining="{{ $remaining }}"
                            data-price="{{ $consignment->product->price }}"
                            {{ $remaining > 0 ? '' : 'disabled' }}>
                            {{ $consignment->product->name }} - {{ $consignment->store->name }} (Sisa: {{ $remaining }}{{ $remaining > 0 ? '' : ' - habis terjual' }})
                        </option>
                    @endforeach
                </select>
                @if($consignments->isEmpty())
                    <p class="hint">Belum ada titipan aktif yang bisa dijual. Terima ajuan titipan dari UMKM terlebih dahulu.</p>
                @elseif($sellableCount === 0)
                    <p class="mt-1.5 text-xs font-medium text-amber-600">Semua titipan sudah habis terjual, jadi belum ada barang yang bisa dijual.</p>
                @endif
                @error('consignment_id')
                    <p class="error-text">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <label for="quantity" class="label">Jumlah terjual</label>
                    <input id="quantity" name="quantity" type="number" value="{{ old('quantity') }}" required min="1"
                        placeholder="0"
                        class="field tabular-nums @error('quantity') field-error @enderror">
                    <p class="hint" id="remaining-info">Pilih titipan terlebih dahulu</p>
                    @error('quantity')
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="sale_date" class="label">Tanggal penjualan</label>
                    <input id="sale_date" name="sale_date" type="date" value="{{ old('sale_date', date('Y-m-d')) }}" required
                        class="field @error('sale_date') field-error @enderror">
                    @error('sale_date')
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="rounded-xl border border-hairline bg-canvas/60 px-4 py-3.5">
                <div class="flex items-center justify-between gap-3">
                    <span class="text-sm text-ink-500">Total harga</span>
                    <input type="text" id="total-display" readonly value="Rp 0"
                        class="w-auto min-w-0 max-w-[60%] border-0 bg-transparent p-0 text-right text-lg font-semibold tabular-nums tracking-tight text-ink-900 focus:outline-none">
                </div>
            </div>

            <div class="flex items-center gap-2 border-t border-hairline pt-5">
                <button type="submit" class="btn btn-primary">Simpan Penjualan</button>
                <a href="{{ route('sales.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>

<script>
let selectedPrice = 0;

document.querySelector('select[name="consignment_id"]').addEventListener('change', function() {
    const option = this.options[this.selectedIndex];
    const remaining = option.dataset.remaining;
    selectedPrice = parseFloat(option.dataset.price) || 0;
    const remainingInfo = document.getElementById('remaining-info');

    if (remaining > 0) {
        remainingInfo.textContent = 'Sisa barang: ' + remaining;
        document.getElementById('quantity').max = remaining;
    } else {
        remainingInfo.textContent = 'Titipan ini tidak punya sisa barang untuk dijual';
        document.getElementById('quantity').removeAttribute('max');
    }

    updateTotal();
});

document.getElementById('quantity').addEventListener('input', updateTotal);

function updateTotal() {
    const quantity = parseInt(document.getElementById('quantity').value) || 0;
    const total = quantity * selectedPrice;
    document.getElementById('total-display').value = 'Rp ' + total.toLocaleString('id-ID');
}
</script>
@endsection
