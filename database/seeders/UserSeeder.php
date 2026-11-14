<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $umkmRole = Role::where('slug', 'umkm')->firstOrFail();
        $storeRole = Role::where('slug', 'store')->firstOrFail();
        $adminRole = Role::where('slug', 'admin')->first();

        // Membuat akun UMKM Owner.
        User::firstOrCreate(
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

        // Membuat akun Admin jika role admin tersedia.
        if ($adminRole !== null) {
            User::firstOrCreate(
                ['email' => 'admin@example.com'],
                [
                    'name' => 'Admin Test',
                    'password' => 'password',
                    'role_id' => $adminRole->id,
                ]
            );
        }
    }
}
