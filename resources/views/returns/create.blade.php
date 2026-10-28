@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-6">Catat Retur Barang</h1>

    <div class="bg-white shadow rounded-lg p-6">
        <form action="{{ route('returns.store') }}" method="POST">
            @csrf
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Titipan</label>
                <select name="consignment_id" class="w-full border border-gray-300 rounded px-3 py-2" required>
                    <option value="">Pilih titipan</option>
                    @foreach($consignments as $consignment)
                        @php
                            $sold = $consignment->sales->sum('quantity');
                            $returned = $consignment->returns->sum('quantity');
                            $remaining = $consignment->quantity - $sold - $returned;
                        @endphp
                        @if($remaining > 0)
                            <option value="{{ $consignment->id }}" 
                                {{ old('consignment_id') == $consignment->id ? 'selected' : '' }}
                                data-remaining="{{ $remaining }}">
                                {{ $consignment->product->name }} - {{ $consignment->store->name }} (Sisa: {{ $remaining }})
                            </option>
                        @endif
                    @endforeach
                </select>
                @error('consignment_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah Retur</label>
                <input type="number" name="quantity" value="{{ old('quantity') }}" class="w-full border border-gray-300 rounded px-3 py-2" required min="1" id="quantity">
                <p class="text-xs text-gray-500 mt-1" id="remaining-info">Pilih titipan terlebih dahulu</p>
                @error('quantity')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Retur</label>
                <textarea name="reason" rows="3" class="w-full border border-gray-300 rounded px-3 py-2" placeholder="Opsional">{{ old('reason') }}</textarea>
                @error('reason')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Retur</label>
                <input type="date" name="return_date" value="{{ old('return_date', date('Y-m-d')) }}" class="w-full border border-gray-300 rounded px-3 py-2" required>
                @error('return_date')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                    Simpan
                </button>
                <a href="{{ route('returns.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.querySelector('select[name="consignment_id"]').addEventListener('change', function() {
    const option = this.options[this.selectedIndex];
    const remaining = option.dataset.remaining;
    const remainingInfo = document.getElementById('remaining-info');
    
    if (remaining) {
        remainingInfo.textContent = 'Sisa barang yang bisa diretur: ' + remaining;
        document.getElementById('quantity').max = remaining;
    }
});
</script>
@endsection
