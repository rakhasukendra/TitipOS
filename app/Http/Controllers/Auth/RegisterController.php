<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Store;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class RegisterController extends Controller
{
    public function create()
    {
        $roles = Role::whereIn('slug', ['umkm', 'store'])->get();
        $storeRoleId = Role::where('slug', 'store')->value('id');

        return view('auth.register', compact('roles', 'storeRoleId'));
    }

    public function store(Request $request)
    {
        $isStoreManager = $this->isRegisteringAsStoreManager($request);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|confirmed|min:8',
            'role_id' => [
                'required',
                Rule::exists('roles', 'id')->whereIn('slug', ['umkm', 'store']),
            ],
            'store_name' => [Rule::requiredIf($isStoreManager), 'nullable', 'string', 'max:255'],
            'store_address' => 'nullable|string|max:255',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $storeName = $validated['store_name'] ?? null;
        $storeAddress = $validated['store_address'] ?? null;

        unset($validated['store_name'], $validated['store_address']);

        $user = DB::transaction(function () use ($validated, $isStoreManager, $storeName, $storeAddress) {
            $user = User::create($validated);

            if ($isStoreManager) {
                $store = Store::create([
                    'name' => $storeName,
                    'address' => $storeAddress,
                    'manager_id' => $user->id,
                ]);

                $user->update(['store_id' => $store->id]);
            }

            return $user;
        });

        event(new Registered($user));

        Auth::login($user);

        return redirect('/dashboard');
    }

    private function isRegisteringAsStoreManager(Request $request): bool
    {
        $storeRoleId = Role::where('slug', 'store')->value('id');

        return $storeRoleId !== null && (int) $request->input('role_id') === (int) $storeRoleId;
    }
}
