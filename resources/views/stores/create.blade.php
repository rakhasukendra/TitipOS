@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Tambah Mitra Toko</h1>
        <p class="text-gray-500 mt-2">
            Masukkan informasi toko yang akan menjadi mitra konsinyasi.
        </p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">

        <form action="{{ route('stores.store') }}" method="POST">
            @csrf

            <!-- Nama Toko -->
            <div class="mb-5">
                <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                    Nama Toko <span class="text-red-500">*</span>
                </label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name') }}"
                    required
                    maxlength="255"
                    placeholder="Masukkan nama toko"
                    class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >

                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Alamat Toko -->
            <div class="mb-5">
                <label for="address" class="block text-sm font-semibold text-gray-700 mb-2">
                    Alamat Toko <span class="text-red-500">*</span>
                </label>

                <textarea
                    id="address"
                    name="address"
                    rows="4"
                    required
                    placeholder="Masukkan alamat lengkap toko"
                    class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >{{ old('address') }}</textarea>

                @error('address')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Nomor Telepon -->
            <div class="mb-5">
                <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">
                    Nomor Telepon <span class="text-red-500">*</span>
                </label>

                <input
                    type="tel"
                    id="phone"
                    name="phone"
                    value="{{ old('phone') }}"
                    required
                    maxlength="20"
                    placeholder="Contoh: 081234567890"
                    class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >

                @error('phone')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email Toko -->
            <div class="mb-5">
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                    Email Toko <span class="text-gray-400 font-normal">(Opsional)</span>
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="Contoh: toko@email.com"
                    class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >

                @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Store Manager -->
            <div class="mb-6">
                <label for="manager_id" class="block text-sm font-semibold text-gray-700 mb-2">
                    Store Manager <span class="text-gray-400 font-normal">(Opsional)</span>
                </label>

                <select
                    id="manager_id"
                    name="manager_id"
                    class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
                    <option value="">Pilih Store Manager</option>

                    @foreach($managers as $manager)
                        <option
                            value="{{ $manager->id }}"
                            {{ old('manager_id') == $manager->id ? 'selected' : '' }}
                        >
                            {{ $manager->name }} - {{ $manager->email }}
                        </option>
                    @endforeach
                </select>

                @error('manager_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tombol -->
            <div class="flex items-center gap-3">
                <button
                    type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-5 py-3 rounded-lg transition"
                >
                    Simpan Toko
                </button>

                <a
                    href="{{ route('stores.index') }}"
                    class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium px-5 py-3 rounded-lg transition"
                >
                    Batal
                </a>
            </div>

        </form>
    </div>
</div>
@endsection
