<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\Store;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil role UMKM Owner.
        $umkmRole = Role::where('slug', 'umkm')->firstOrFail();

        // Ambil role Store Manager.
        $storeRole = Role::where('slug', 'store')->firstOrFail();

        // Membuat akun UMKM Owner.
        $umkm = User::firstOrCreate(
            ['email' => 'umkm@example.com'],
            [
                'name' => 'UMKM Test',
                'password' => 'password',
                'role_id' => $umkmRole->id,
            ]
        );

        // Membuat akun Store Manager.
        $manager = User::firstOrCreate(
            ['email' => 'store@example.com'],
            [
                'name' => 'Store Manager',
                'password' => 'password',
                'role_id' => $storeRole->id,
            ]
        );

        // Membuat toko yang dikelola Store Manager.
        $store = Store::firstOrCreate(
            ['manager_id' => $manager->id],
            [
                'name' => 'Toko Test',
                'address' => 'Jl. Test No. 1',
                'phone' => '081234567890',
                'email' => 'store@example.com',
            ]
        );

        // Menghubungkan akun Store Manager dengan tokonya.
        $manager->update([
            'store_id' => $store->id,
        ]);
    }
}
