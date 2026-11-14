@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Mitra Toko</h1>
            <p class="text-gray-500 mt-1">Kelola data toko mitra TitipOS.</p>
        </div>

        <a href="{{ route('stores.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
            + Tambah Toko
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div class="bg-white shadow rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">

                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Toko</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Alamat</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Telepon</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Manager</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200">

                    @forelse($stores as $index => $store)
                    <tr class="hover:bg-gray-50">

                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $stores->firstItem() + $index }}
                        </td>

                        <td class="px-6 py-4">
                            <div class="font-semibold text-gray-900">
                                {{ $store->name }}
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ $store->email ?? '-' }}
                            </div>
                        </td>

                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $store->address ?? '-' }}
                        </td>

                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $store->phone ?? '-' }}
                        </td>

                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $store->manager->name ?? 'Belum ditentukan' }}
                        </td>

                        <td class="px-6 py-4 text-sm whitespace-nowrap space-x-2">

                            <a href="{{ route('stores.show', $store) }}"
                               class="text-blue-600 hover:text-blue-800">
                                Detail
                            </a>

                            <a href="{{ route('stores.edit', $store) }}"
                               class="text-yellow-600 hover:text-yellow-800">
                                Edit
                            </a>

                            <form action="{{ route('stores.destroy', $store) }}"
                                  method="POST"
                                  class="inline"
                                  onsubmit="return confirm('Yakin ingin menghapus toko ini?')">

                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                        class="text-red-600 hover:text-red-800">
                                    Hapus
                                </button>
                            </form>

                        </td>
                    </tr>

                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            Belum ada data mitra toko.
                        </td>
                    </tr>
                    @endforelse

                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $stores->links() }}
    </div>

</div>
@endsection
