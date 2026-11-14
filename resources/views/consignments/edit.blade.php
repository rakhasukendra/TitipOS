@extends('layouts.app')

@section('header', 'Edit Titipan')
@section('subheader', 'Perbarui jumlah, status, atau tanggal titipan')

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
            <h2 class="mt-1.5 text-lg font-semibold tracking-tight text-ink-900">Edit Titipan</h2>
            <p class="mt-1 text-sm text-ink-500">Perbarui jumlah, status, atau tanggal titipan ini.</p>
        </div>
        <form action="{{ route('consignments.update', $consignment) }}" method="POST" class="space-y-5 p-6 sm:p-8">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <label for="product" class="label">Produk</label>
                    <input id="product" type="text" value="{{ $consignment->product->name }}" readonly
                        class="field cursor-not-allowed bg-canvas text-ink-500">
                </div>

                <div>
                    <label for="store" class="label">Toko</label>
                    <input id="store" type="text" value="{{ $consignment->store->name }}" readonly
                        class="field cursor-not-allowed bg-canvas text-ink-500">
                </div>
            </div>

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <label for="quantity" class="label">Jumlah</label>
                    <input id="quantity" name="quantity" type="number" value="{{ old('quantity', $consignment->quantity) }}" required min="1"
                        class="field tabular-nums @error('quantity') field-error @enderror">
                    @error('quantity')
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="status" class="label">Status</label>
                    <select id="status" name="status" required class="field field-select @error('status') field-error @enderror">
                        @foreach(\App\Models\Consignment::statusOptions() as $value => $label)
                            <option value="{{ $value }}" {{ old('status', $consignment->status) === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('status')
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="consignment_date" class="label">Tanggal titipan</label>
                <input id="consignment_date" name="consignment_date" type="date" value="{{ old('consignment_date', $consignment->consignment_date->format('Y-m-d')) }}" required
                    class="field @error('consignment_date') field-error @enderror">
                @error('consignment_date')
                    <p class="error-text">{{ $message }}</p>
                @enderror
            </div>

            <div class="rounded-xl border border-hairline bg-canvas/60 px-4 py-3">
                <p class="text-xs leading-relaxed text-ink-500">
                    Status <span class="font-medium text-ink-700">Ditolak</span> mengembalikan stok ke UMKM, dan mengaktifkannya kembali akan memotong stok lagi.
                    Titipan yang sudah memiliki penjualan atau retur tidak bisa ditolak.
                </p>
            </div>

            <div class="flex items-center gap-2 border-t border-hairline pt-5">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="{{ route('consignments.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
