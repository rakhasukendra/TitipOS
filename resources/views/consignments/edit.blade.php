@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-6">Edit Titipan</h1>

    <div class="bg-white shadow-sm rounded-2xl border border-gray-100 p-6">
        <form action="{{ route('consignments.update', $consignment) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Produk</label>
                <input type="text" value="{{ $consignment->product->name }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-100" readonly>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Toko</label>
                <input type="text" value="{{ $consignment->store->name }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-100" readonly>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah</label>
                <input type="number" name="quantity" value="{{ old('quantity', $consignment->quantity) }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent" required min="1">
                @error('quantity')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                    <option value="active" {{ $consignment->status === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="settled" {{ $consignment->status === 'settled' ? 'selected' : '' }}>Settled</option>
                    <option value="returned" {{ $consignment->status === 'returned' ? 'selected' : '' }}>Dikembalikan</option>
                </select>
                @error('status')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Titipan</label>
                <input type="date" name="consignment_date" value="{{ old('consignment_date', $consignment->consignment_date->format('Y-m-d')) }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                @error('consignment_date')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                    Simpan
                </button>
                <a href="{{ route('consignments.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg transition">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
