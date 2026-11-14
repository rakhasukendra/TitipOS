@extends('layouts.app')

@section('header', 'Catat Retur')
@section('subheader', 'Kembalikan barang titipan yang tidak terjual')

@section('content')
<div class="max-w-2xl">
    <a href="{{ route('returns.index') }}" class="mb-4 inline-flex items-center gap-1.5 link-muted">
        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Kembali ke daftar retur
    </a>

    <div class="surface overflow-hidden">
        <div class="border-b border-hairline px-6 py-5 sm:px-8">
            <p class="section-label">Formulir</p>
            <h2 class="mt-1.5 text-lg font-semibold tracking-tight text-ink-900">Catat Retur</h2>
            <p class="mt-1 text-sm text-ink-500">Catat barang titipan yang dikembalikan dari toko.</p>
        </div>
        <form action="{{ route('returns.store') }}" method="POST" class="space-y-5 p-6 sm:p-8">
            @csrf

            <div>
                <label for="consignment_id" class="label">Titipan</label>
                <select id="consignment_id" name="consignment_id" required class="field field-select @error('consignment_id') field-error @enderror">
                    <option value="">Pilih titipan</option>
                    @php $returnableCount = 0; @endphp
                    @foreach($consignments as $consignment)
                        @php
                            $sold = $consignment->sales->sum('quantity');
                            $returned = $consignment->returns->sum('quantity');
                            $remaining = $consignment->quantity - $sold - $returned;
                            $returnableCount += $remaining > 0 ? 1 : 0;
                        @endphp
                        <option value="{{ $consignment->id }}"
                            {{ old('consignment_id') == $consignment->id ? 'selected' : '' }}
                            data-remaining="{{ $remaining }}"
                            {{ $remaining > 0 ? '' : 'disabled' }}>
                            {{ $consignment->product->name }} - {{ $consignment->store->name }} (Sisa: {{ $remaining }}{{ $remaining > 0 ? '' : ' - habis terjual' }})
                        </option>
                    @endforeach
                </select>
                @if($consignments->isEmpty())
                    <p class="hint">Belum ada titipan aktif yang bisa diretur. Terima ajuan titipan dari UMKM terlebih dahulu.</p>
                @elseif($returnableCount === 0)
                    <p class="mt-1.5 text-xs font-medium text-amber-600">Semua titipan sudah habis terjual, jadi belum ada barang yang bisa diretur.</p>
                @endif
                @error('consignment_id')
                    <p class="error-text">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <label for="quantity" class="label">Jumlah retur</label>
                    <input id="quantity" name="quantity" type="number" value="{{ old('quantity') }}" required min="1"
                        placeholder="0"
                        class="field tabular-nums @error('quantity') field-error @enderror">
                    <p class="hint" id="remaining-info">Pilih titipan terlebih dahulu</p>
                    @error('quantity')
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="return_date" class="label">Tanggal retur</label>
                    <input id="return_date" name="return_date" type="date" value="{{ old('return_date', date('Y-m-d')) }}" required
                        class="field @error('return_date') field-error @enderror">
                    @error('return_date')
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="reason" class="label">Alasan retur</label>
                <textarea id="reason" name="reason" rows="3" placeholder="Opsional — misalnya barang rusak atau tidak laku"
                    class="field @error('reason') field-error @enderror">{{ old('reason') }}</textarea>
                @error('reason')
                    <p class="error-text">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-2 border-t border-hairline pt-5">
                <button type="submit" class="btn btn-primary">Simpan Retur</button>
                <a href="{{ route('returns.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>

<script>
document.querySelector('select[name="consignment_id"]').addEventListener('change', function() {
    const option = this.options[this.selectedIndex];
    const remaining = option.dataset.remaining;
    const remainingInfo = document.getElementById('remaining-info');

    if (remaining > 0) {
        remainingInfo.textContent = 'Sisa barang yang bisa diretur: ' + remaining;
        document.getElementById('quantity').max = remaining;
    } else {
        remainingInfo.textContent = 'Titipan ini tidak punya sisa barang untuk diretur';
        document.getElementById('quantity').removeAttribute('max');
    }
});
</script>
@endsection
