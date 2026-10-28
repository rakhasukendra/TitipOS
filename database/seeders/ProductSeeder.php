<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $umkm = User::whereHas('role', function ($q) {
            $q->where('slug', 'umkm');
        })->first();

        if (!$umkm) {
            return;
        }

        Product::create([
            'name' => 'Bunga Mawar Merah',
            'description' => 'Bunga mawar merah segar berkualitas tinggi',
            'price' => 25000,
            'stock' => 100,
            'umkm_id' => $umkm->id,
        ]);

        Product::create([
            'name' => 'Bunga Tulip Pink',
            'description' => 'Bunga tulip pink import',
            'price' => 35000,
            'stock' => 50,
            'umkm_id' => $umkm->id,
        ]);

        Product::create([
            'name' => 'Bunga Matahari',
            'description' => 'Bunga matahari cerah',
            'price' => 15000,
            'stock' => 150,
            'umkm_id' => $umkm->id,
        ]);
    }
}
