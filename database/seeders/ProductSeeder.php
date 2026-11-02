<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Cari akun UMKM Owner.
        $umkm = User::whereHas('role', function ($query) {
            $query->where('slug', 'umkm');
        })->first();

        if (!$umkm) {
            return;
        }

        $products = [
            [
                'name' => 'Bunga Mawar Merah',
                'description' => 'Bunga mawar merah segar berkualitas tinggi',
                'price' => 25000,
                'stock' => 100,
            ],
            [
                'name' => 'Bunga Tulip Pink',
                'description' => 'Bunga tulip pink import',
                'price' => 35000,
                'stock' => 50,
            ],
            [
                'name' => 'Bunga Matahari',
                'description' => 'Bunga matahari cerah',
                'price' => 15000,
                'stock' => 150,
            ],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(
                [
                    'umkm_id' => $umkm->id,
                    'name' => $product['name'],
                ],
                $product
            );
        }
    }
}
