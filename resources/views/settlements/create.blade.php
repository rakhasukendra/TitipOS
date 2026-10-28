@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-6">Buat Settlement</h1>

    <div class="bg-white shadow rounded-lg p-6">
        <form action="{{ route('settlements.store') }}" method="POST">
            @csrf
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Toko</label>
                <select name="store_id" class="w-full border border-gray-300 rounded px-3 py-2" required id="store_id">
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
                <label class="block text-sm font-medium text-gray-700 mb-2">UMKM</label>
                <select name="umkm_id" class="w-full border border-gray-300 rounded px-3 py-2" required>
                    <option value="">Pilih UMKM</option>
                    @foreach($umkms as $umkm)
                        <option value="{{ $umkm->id }}" {{ old('umkm_id') == $umkm->id ? 'selected' : '' }}>
                            {{ $umkm->name }}
                        </option>
                    @endforeach
                </select>
                @error('umkm_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Komisi (%)</label>
                <input type="number" name="commission_rate" value="{{ old('commission_rate', 10) }}" step="0.01" class="w-full border border-gray-300 rounded px-3 py-2" required>
                @error('commission_rate')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Settlement</label>
                <input type="date" name="settlement_date" value="{{ old('settlement_date', date('Y-m-d')) }}" class="w-full border border-gray-300 rounded px-3 py-2" required>
                @error('settlement_date')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                    Buat Settlement
                </button>
                <a href="{{ route('settlements.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
