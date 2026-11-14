@extends('layouts.app')

@section('header', 'Edit Produk')
@section('subheader', 'Perbarui informasi produk titipan')

@section('content')
<div class="max-w-2xl">
    <a href="{{ route('products.index') }}" class="mb-4 inline-flex items-center gap-1.5 link-muted">
        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Kembali ke daftar produk
    </a>

    <div class="surface overflow-hidden">
        <div class="border-b border-hairline px-6 py-5 sm:px-8">
            <p class="section-label">Formulir</p>
            <h2 class="mt-1.5 text-lg font-semibold tracking-tight text-ink-900">Edit Produk</h2>
            <p class="mt-1 text-sm text-ink-500">Perbarui nama, harga, atau stok produk titipan Anda.</p>
        </div>
        <form action="{{ route('products.update', $product) }}" method="POST" class="space-y-5 p-6 sm:p-8">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="label">Nama produk</label>
                <input id="name" name="name" type="text" value="{{ old('name', $product->name) }}" required
                    class="field @error('name') field-error @enderror">
                @error('name')
                    <p class="error-text">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="label">Deskripsi</label>
                <textarea id="description" name="description" rows="3"
                    class="field @error('description') field-error @enderror">{{ old('description', $product->description) }}</textarea>
                @error('description')
                    <p class="error-text">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <label for="price" class="label">Harga (Rp)</label>
                    <input id="price" name="price" type="number" value="{{ old('price', $product->price) }}" required min="0"
                        class="field tabular-nums @error('price') field-error @enderror">
                    @error('price')
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="stock" class="label">Stok</label>
                    <input id="stock" name="stock" type="number" value="{{ old('stock', $product->stock) }}" required min="0"
                        class="field tabular-nums @error('stock') field-error @enderror">
                    @error('stock')
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center gap-2 border-t border-hairline pt-5">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="{{ route('products.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
