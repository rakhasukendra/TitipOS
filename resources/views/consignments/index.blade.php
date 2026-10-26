@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Daftar Titipan</h1>
        <a href="{{ route('consignments.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
            Tambah Titipan
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produk</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Toko</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($consignments as $consignment)
                <tr>
                    <td class="px-6 py-4">
                        <div class="font-medium text-gray-900">{{ $consignment->product->name }}</div>
                        <div class="text-sm text-gray-500">{{ $consignment->consignment_date->format('d M Y') }}</div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900">
                        {{ $consignment->store->name }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900">
                        {{ $consignment->quantity }} pcs
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded text-xs font-medium
                            {{ $consignment->status === 'active' ? 'bg-green-100 text-green-800' : 
                               ($consignment->status === 'settled' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800') }}">
                            {{ $consignment->status === 'active' ? 'Aktif' : ($consignment->status === 'settled' ? 'Ter settled' : 'Dikembalikan') }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm space-x-2">
                        <a href="{{ route('consignments.show', $consignment) }}" class="text-blue-600 hover:text-blue-900">Detail</a>
                        <a href="{{ route('consignments.edit', $consignment) }}" class="text-yellow-600 hover:text-yellow-900">Edit</a>
                        <form action="{{ route('consignments.destroy', $consignment) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Yakin hapus titipan ini?')">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">Belum ada titipan</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $consignments->links() }}
    </div>
</div>
@endsection
