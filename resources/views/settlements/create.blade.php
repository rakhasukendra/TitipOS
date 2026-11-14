@extends('layouts.app')

@section('header', 'Buat Settlement')
@section('subheader', 'Tutup periode penjualan antara toko dan UMKM')

@section('content')
<div class="max-w-2xl">
    <a href="{{ route('settlements.index') }}" class="mb-4 inline-flex items-center gap-1.5 link-muted">
        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Kembali ke daftar settlement
    </a>

    <div class="surface overflow-hidden">
        <div class="border-b border-hairline px-6 py-5 sm:px-8">
            <p class="section-label">Formulir</p>
            <h2 class="mt-1.5 text-lg font-semibold tracking-tight text-ink-900">Buat Settlement</h2>
            <p class="mt-1 text-sm text-ink-500">Tutup titipan aktif antara satu toko dan satu UMKM.</p>
        </div>
        <form action="{{ route('settlements.store') }}" method="POST" class="space-y-5 p-6 sm:p-8">
            @csrf

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
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

                <div>
                    <label for="umkm_id" class="label">UMKM</label>
                    <select id="umkm_id" name="umkm_id" required class="field field-select @error('umkm_id') field-error @enderror">
                        <option value="">Pilih UMKM</option>
                        @foreach($umkms as $umkm)
                            <option value="{{ $umkm->id }}" {{ old('umkm_id') == $umkm->id ? 'selected' : '' }}>
                                {{ $umkm->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('umkm_id')
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <label for="commission_rate" class="label">Komisi (%)</label>
                    <input id="commission_rate" name="commission_rate" type="number" value="{{ old('commission_rate', 10) }}" step="0.01" required
                        class="field tabular-nums @error('commission_rate') field-error @enderror">
                    @error('commission_rate')
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="settlement_date" class="label">Tanggal settlement</label>
                    <input id="settlement_date" name="settlement_date" type="date" value="{{ old('settlement_date', date('Y-m-d')) }}" required
                        class="field @error('settlement_date') field-error @enderror">
                    @error('settlement_date')
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center gap-2 border-t border-hairline pt-5">
                <button type="submit" class="btn btn-primary">Buat Settlement</button>
                <a href="{{ route('settlements.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
