<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StoreController extends Controller
{
    public function index()
    {
        $stores = Store::with('manager')
            ->latest()
            ->paginate(10);

        return view('stores.index', compact('stores'));
    }

    public function create()
    {
        $managers = User::whereHas('role', function ($query) {
            $query->where('slug', 'store');
        })->whereNull('store_id')->get();

        return view('stores.create', compact('managers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'manager_id' => 'nullable|exists:users,id',
        ]);

        DB::transaction(function () use ($validated) {
            if (!empty($validated['manager_id'])) {
                $manager = User::findOrFail($validated['manager_id']);

                if (!$manager->isStore() || $manager->store_id !== null) {
                    abort(422, 'Manager tidak valid atau sudah memiliki toko.');
                }
            }

            $store = Store::create($validated);

            if (!empty($validated['manager_id'])) {
                User::whereKey($validated['manager_id'])
                    ->update(['store_id' => $store->id]);
            }
        });

        return redirect()
            ->route('stores.index')
            ->with('success', 'Mitra toko berhasil ditambahkan.');
    }

    public function show(Store $store)
    {
        $store->load('manager', 'consignments.product');

        return view('stores.show', compact('store'));
    }

    public function edit(Store $store)
    {
        $managers = User::whereHas('role', function ($query) {
            $query->where('slug', 'store');
        })->where(function ($query) use ($store) {
            $query->whereNull('store_id')
                ->orWhere('store_id', $store->id);
        })->get();

        return view('stores.edit', compact('store', 'managers'));
    }

    public function update(Request $request, Store $store)
    {
       $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'manager_id' => 'nullable|exists:users,id',
        ]);

        DB::transaction(function () use ($validated, $store) {
            $oldManagerId = $store->manager_id;
            $newManagerId = $validated['manager_id'] ?? null;

            if ($newManagerId) {
                $manager = User::findOrFail($newManagerId);

                if (
                    !$manager->isStore() ||
                    ($manager->store_id !== null &&
                     $manager->store_id != $store->id)
                ) {
                    abort(422, 'Manager tidak valid atau sudah memiliki toko lain.');
                }
            }

            $store->update($validated);

            if ($oldManagerId && $oldManagerId != $newManagerId) {
                User::whereKey($oldManagerId)
                    ->where('store_id', $store->id)
                    ->update(['store_id' => null]);
            }

            if ($newManagerId) {
                User::whereKey($newManagerId)
                    ->update(['store_id' => $store->id]);
            }
        });

        return redirect()
            ->route('stores.index')
            ->with('success', 'Data mitra toko berhasil diperbarui.');
    }

    public function destroy(Store $store)
    {
        if ($store->consignments()->exists()) {
            return back()->withErrors([
                'store' => 'Toko tidak dapat dihapus karena masih memiliki transaksi konsinyasi.'
            ]);
        }

        DB::transaction(function () use ($store) {
            User::where('store_id', $store->id)
                ->update(['store_id' => null]);

            $store->delete();
        });

        return redirect()
            ->route('stores.index')
            ->with('success', 'Mitra toko berhasil dihapus.');
    }
}
