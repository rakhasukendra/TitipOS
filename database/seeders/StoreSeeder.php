<?php

namespace Database\Seeders;

use App\Models\Store;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StoreSeeder extends Seeder
{
    public function run(): void
    {
        Store::create([
            'name' => 'Toko Bunga Mawar',
            'address' => 'Jl. Merdeka No. 45',
            'phone' => '081234567890',
            'email' => 'toko.bunga@example.com',
        ]);
    }
}
