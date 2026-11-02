@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Daftar Titipan</h1>
            <p class="text-gray-500 mt-1">
                Kelola dan pantau stok produk yang dititipkan ke toko mitra.
            </p>
        </div>

        @if(in_array(auth()->user()->role->slug ?? '', ['umkm', 'admin']))
            <a href="{{ route('consignments.create') }}"
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                + Tambah Titipan
            </a>
        @endif
    </div>

    <!-- Pesan sukses -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4">
            {{ session('success') }}
        </div>
    @endif

    <!-- Pesan error -->
    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <!-- Tabel Titipan -->
    <div class="bg-white shadow rounded-lg overflow-x-auto">

        <table class="min-w-full divide-y divide-gray-200">

            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                        Produk
                    </th>

                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                        Toko
                    </th>

                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                        Jumlah Titipan
                    </th>

                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                        Terjual
                    </th>

                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                        Retur
                    </th>

                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                        Sisa Stok
                    </th>

                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                        Status
                    </th>

                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                        Aksi
                    </th>
                </tr>
            </thead>

            <tbody class="bg-white divide-y divide-gray-200">

                @forelse($consignments as $consignment)

                    <tr class="hover:bg-gray-50">

                        <!-- Produk -->
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">
                                {{ $consignment->product->name }}
                            </div>

                            <div class="text-sm text-gray-500">
                                {{ $consignment->consignment_date->format('d M Y') }}
                            </div>
                        </td>

                        <!-- Toko -->
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ $consignment->store->name }}
                        </td>

                        <!-- Jumlah Titipan -->
                        <td class="px-6 py-4 text-sm text-center text-gray-900">
                            {{ $consignment->quantity }} pcs
                        </td>

                        <!-- Terjual -->
                        <td class="px-6 py-4 text-sm text-center">
                            <span class="font-semibold text-green-600">
                                {{ $consignment->total_sold }} pcs
                            </span>
                        </td>

                        <!-- Retur -->
                        <td class="px-6 py-4 text-sm text-center">
                            <span class="font-semibold text-orange-600">
                                {{ $consignment->total_returned }} pcs
                            </span>
                        </td>

                        <!-- Sisa Stok -->
                        <td class="px-6 py-4 text-sm text-center">
                            <span class="inline-flex px-3 py-1 rounded-full font-semibold
                                {{ $consignment->remaining_stock > 0
                                    ? 'bg-blue-100 text-blue-700'
                                    : 'bg-red-100 text-red-700' }}">
                                {{ $consignment->remaining_stock }} pcs
                            </span>
                        </td>

                        <!-- Status -->
                        <td class="px-6 py-4 text-center">
                            <span class="px-2 py-1 rounded text-xs font-medium
                                {{ $consignment->status === 'active'
                                    ? 'bg-green-100 text-green-800'
                                    : ($consignment->status === 'settled'
                                        ? 'bg-blue-100 text-blue-800'
                                        : 'bg-red-100 text-red-800') }}">

                                {{ $consignment->status === 'active'
                                    ? 'Aktif'
                                    : ($consignment->status === 'settled'
                                        ? 'Selesai'
                                        : 'Dikembalikan') }}

                            </span>
                        </td>

                        <!-- Aksi -->
                        <td class="px-6 py-4 text-sm text-center whitespace-nowrap">

                            <a href="{{ route('consignments.show', $consignment) }}"
                               class="text-blue-600 hover:text-blue-900 mr-2">
                                Detail
                            </a>

                            @if(in_array(auth()->user()->role->slug ?? '', ['umkm', 'admin']))
                                <a href="{{ route('consignments.edit', $consignment) }}"
                                   class="text-yellow-600 hover:text-yellow-900 mr-2">
                                    Edit
                                </a>

                                <form action="{{ route('consignments.destroy', $consignment) }}"
                                      method="POST"
                                      class="inline">

                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="text-red-600 hover:text-red-900"
                                            onclick="return confirm('Yakin ingin menghapus titipan ini?')">
                                        Hapus
                                    </button>
                                </form>
                            @endif

                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                            Belum ada data titipan.
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $consignments->links() }}
    </div>

</div>
@endsection
