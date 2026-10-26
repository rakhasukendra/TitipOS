<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $umkmRole = Role::where('slug', 'umkm')->first();
        $storeRole = Role::where('slug', 'store')->first();

        $umkm = User::create([
            'name' => 'UMKM Test',
            'email' => 'umkm@example.com',
            'password' => Hash::make('password'),
            'role_id' => $umkmRole?->id,
        ]);

        $store = User::create([
            'name' => 'Store Manager',
            'email' => 'store@example.com',
            'password' => Hash::make('password'),
            'role_id' => $storeRole?->id,
        ]);

        $store->store()->create(['name' => 'Toko Test', 'address' => 'Jl. Test No. 1']);
    }
}
