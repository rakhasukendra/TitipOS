<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    /**
     * Menampilkan halaman register.
     */
    public function create()
    {
        // Pengguna hanya boleh mendaftar sebagai UMKM atau Store.
        $roles = Role::whereIn('slug', ['umkm', 'store'])->get();

        return view('auth.register', compact('roles'));
    }

    /**
     * Memproses pendaftaran pengguna.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|confirmed|min:8',
            'role_id' => 'required|exists:roles,id',
        ]);

        // Pastikan role yang dipilih bukan Admin.
        $role = Role::whereIn('slug', ['umkm', 'store'])
            ->find($validated['role_id']);

        if (!$role) {
            return back()
                ->withErrors([
                    'role_id' => 'Role yang dipilih tidak diperbolehkan.'
                ])
                ->withInput($request->except('password', 'password_confirmation'));
        }

        // Membuat akun pengguna.
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role_id' => $role->id,
        ]);

        // Menjalankan event registrasi Laravel.
        event(new Registered($user));

        // Login otomatis setelah berhasil mendaftar.
        Auth::login($user);

        $request->session()->regenerate();

        return redirect()
            ->route('dashboard')
            ->with('success', 'Pendaftaran berhasil. Selamat datang di TitipOS!');
    }
}
