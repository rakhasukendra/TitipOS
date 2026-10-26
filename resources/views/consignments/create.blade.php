@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-6">Tambah Titipan Baru</h1>

    <div class="bg-white shadow rounded-lg p-6">
        <form action="{{ route('consignments.store') }}" method="POST">
            @csrf
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Produk</label>
                <select name="product_id" class="w-full border border-gray-300 rounded px-3 py-2">
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
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Toko</label>
                <select name="store_id" class="w-full border border-gray-300 rounded px-3 py-2" required>
                    <option value="">Pilih toko</option>
                    @foreach($stores as $store)
                        <option value="{{ $store->id }}" {{ old('store_id') == $store->id ? 'selected' : '' }}>
                            {{ $store->name }}
                        </option>
                    @endforeach
                </select>
                @error('store_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah</label>
                <input type="number" name="quantity" value="{{ old('quantity') }}" class="w-full border border-gray-300 rounded px-3 py-2" required min="1" id="quantity">
                <p class="text-xs text-gray-500 mt-1" id="stock-info">Pilih produk terlebih dahulu</p>
                @error('quantity')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Titipan</label>
                <input type="date" name="consignment_date" value="{{ old('consignment_date', date('Y-m-d')) }}" class="w-full border border-gray-300 rounded px-3 py-2" required>
                @error('consignment_date')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                    Simpan
                </button>
                <a href="{{ route('consignments.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded">
                    Batal
                </a>
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
