@extends('layouts.app')

@section('header', 'Tambah Titipan')
@section('subheader', 'Ajukan barang Anda untuk dititipkan ke toko')

@section('content')
<div class="max-w-2xl">
    <a href="{{ route('consignments.index') }}" class="mb-4 inline-flex items-center gap-1.5 link-muted">
        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Kembali ke daftar titipan
    </a>

    <div class="surface overflow-hidden">
        <div class="border-b border-hairline px-6 py-5 sm:px-8">
            <p class="section-label">Formulir</p>
            <h2 class="mt-1.5 text-lg font-semibold tracking-tight text-ink-900">Ajukan Titipan</h2>
            <p class="mt-1 text-sm text-ink-500">Ajuan akan ditinjau dulu oleh toko sebelum barang bisa dijual.</p>
        </div>
        <form action="{{ route('consignments.store') }}" method="POST" class="space-y-5 p-6 sm:p-8">
            @csrf

            <div>
                <label for="product_id" class="label">Produk</label>
                <select id="product_id" name="product_id" class="field field-select @error('product_id') field-error @enderror">
                    <option value="">Pilih produk</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}"
                            {{ old('product_id') == $product->id ? 'selected' : '' }}
                            data-stock="{{ $product->stock }}">
                            {{ $product->name }} (Stok: {{ $product->stock }})
                        </option>
                    @endforeach
                </select>
                @error('product_id')
                    <p class="error-text">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="store_id" class="label">Toko</label>
                <select id="store_id" name="store_id" required class="field field-select @error('store_id') field-error @enderror">
                    <option value="">Pilih toko</option>
                    @foreach($stores as $store)
                        <option value="{{ $store->id }}" {{ old('store_id') == $store->id ? 'selected' : '' }}>
                            {{ $store->name }}
                        </option>
                    @endforeach
                </select>
                @error('store_id')
                    <p class="error-text">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <label for="quantity" class="label">Jumlah</label>
                    <input id="quantity" name="quantity" type="number" value="{{ old('quantity') }}" required min="1"
                        placeholder="0"
                        class="field tabular-nums @error('quantity') field-error @enderror">
                    <p class="hint" id="stock-info">Pilih produk terlebih dahulu</p>
                    @error('quantity')
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="consignment_date" class="label">Tanggal titipan</label>
                    <input id="consignment_date" name="consignment_date" type="date" value="{{ old('consignment_date', date('Y-m-d')) }}" required
                        class="field @error('consignment_date') field-error @enderror">
                    @error('consignment_date')
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="rounded-xl border border-hairline bg-canvas/60 px-4 py-3">
                <p class="text-xs leading-relaxed text-ink-500">
                    Ajuan akan dikirim ke toko yang dipilih. Barang baru bisa dicatat sebagai penjualan setelah toko menerima ajuan ini.
                </p>
            </div>

            <div class="flex items-center gap-2 border-t border-hairline pt-5">
                <button type="submit" class="btn btn-primary">Kirim Ajuan</button>
                <a href="{{ route('consignments.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>

<script>
document.querySelector('select[name="product_id"]').addEventListener('change', function() {
    const option = this.options[this.selectedIndex];
    const stock = option.dataset.stock;
    const stockInfo = document.getElementById('stock-info');

    if (stock) {
        stockInfo.textContent = 'Stok tersedia: ' + stock;
    }
});
</script>
@endsection
