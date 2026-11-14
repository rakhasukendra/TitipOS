<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $umkmRole = Role::where('slug', 'umkm')->first();
        $storeRole = Role::where('slug', 'store')->first();
        $adminRole = Role::where('slug', 'admin')->first();

        $umkm = User::create([
            'name' => 'UMKM Test',
            'email' => 'umkm@example.com',
            'password' => Hash::make('password'),
            'role_id' => $umkmRole?->id,
        ]);

        $storeManager = User::create([
            'name' => 'Store Manager',
            'email' => 'store@example.com',
            'password' => Hash::make('password'),
            'role_id' => $storeRole?->id,
        ]);

        $store = $storeManager->store()->create(['name' => 'Toko Test', 'address' => 'Jl. Test No. 1']);
        $store->update(['manager_id' => $storeManager->id]);

        User::create([
            'name' => 'Admin Test',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role_id' => $adminRole?->id,
        ]);
    }
}
