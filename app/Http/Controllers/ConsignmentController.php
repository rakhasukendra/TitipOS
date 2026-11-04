<?php

namespace App\Http\Controllers;

use App\Models\Consignment;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ConsignmentController extends Controller
{
    /**
     * Menampilkan daftar transaksi konsinyasi.
     */
    public function index()
    {
        $user = auth()->user();

        $query = Consignment::with([
            'product',
            'store',
            'sales',
            'returns'
        ]);

        // UMKM hanya melihat transaksi dari produknya.
        if ($user->isUmkm()) {
            $query->whereHas('product', function ($q) use ($user) {
                $q->where('umkm_id', $user->id);
            });
        }

        // Store Manager hanya melihat transaksi tokonya.
        if ($user->isStore()) {
            $query->where('store_id', $user->store_id);
        }

        $consignments = $query
            ->latest()
            ->paginate(10);

        return view('consignments.index', compact('consignments'));
    }

    /**
     * Menampilkan formulir pembuatan konsinyasi.
     */
    public function create()
    {
        $user = auth()->user();

        $products = Product::query()
            ->when($user->isUmkm(), function ($query) use ($user) {
                $query->where('umkm_id', $user->id);
            })
            ->when(!$user->isAdmin(), function ($query) use ($user) {
                if (!$user->isUmkm()) {
                    $query->where('umkm_id', $user->umkm_id);
                }
            })
            ->get();

        $stores = Store::query()
            ->when($user->isStore(), function ($query) use ($user) {
                $query->where('id', $user->store_id);
            })
            ->get();

        return view('consignments.create', compact('products', 'stores'));
    }

    /**
     * Menyimpan transaksi konsinyasi.
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'store_id' => 'required|integer|exists:stores,id',
            'quantity' => 'required|integer|min:1',
            'consignment_date' => 'required|date',
        ]);

        if (!$user->isUmkm() && !$user->isAdmin()) {
            abort(403, 'Anda tidak memiliki izin membuat titipan.');
        }

        $consignment = DB::transaction(function () use ($validated, $user) {

            $product = Product::whereKey($validated['product_id'])
                ->lockForUpdate()
                ->firstOrFail();

            // Pastikan produk merupakan milik UMKM yang login.
            if ($user->isUmkm() && $product->umkm_id != $user->id) {
                abort(403, 'Produk bukan milik UMKM Anda.');
            }

            if ($product->stock < $validated['quantity']) {
                throw ValidationException::withMessages([
                    'quantity' => 'Stok produk tidak mencukupi.'
                ]);
            }

            $store = Store::findOrFail($validated['store_id']);

            // Mengurangi stok produk.
            $product->decrement('stock', $validated['quantity']);

            // Membuat transaksi konsinyasi.
            return Consignment::create([
                'product_id' => $product->id,
                'store_id' => $store->id,
                'quantity' => $validated['quantity'],
                'consignment_date' => $validated['consignment_date'],
                'status' => 'active',
            ]);
        });

        return redirect()
            ->route('consignments.show', $consignment)
            ->with('success', 'Transaksi konsinyasi berhasil dibuat.');
    }

    /**
     * Menampilkan detail transaksi.
     */
    public function show(Consignment $consignment)
    {
        $this->authorizeConsignment($consignment);

        $consignment->load([
            'product',
            'store',
            'sales',
            'returns'
        ]);

        return view('consignments.show', compact('consignment'));
    }

    /**
     * Menampilkan formulir edit.
     */
    public function edit(Consignment $consignment)
    {
        $this->authorizeConsignment($consignment);

        if (!auth()->user()->isUmkm() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $consignment->load('product', 'store');

        return view('consignments.edit', compact('consignment'));
    }

    /**
     * Memperbarui transaksi konsinyasi.
     */
    public function update(Request $request, Consignment $consignment)
    {
        $user = auth()->user();

        $this->authorizeConsignment($consignment);

        if (!$user->isUmkm() && !$user->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
            'consignment_date' => 'required|date',
            'status' => 'required|in:active,settled,returned',
        ]);

        // Untuk menjaga konsistensi stok, jumlah titipan
        // dan status tidak boleh diubah sembarangan.
        if ($validated['quantity'] != $consignment->quantity) {
            return back()->withErrors([
                'quantity' => 'Jumlah titipan tidak dapat diubah melalui edit. Buat transaksi baru jika diperlukan.'
            ]);
        }

        $consignment->update([
            'consignment_date' => $validated['consignment_date'],
        ]);

        return redirect()
            ->route('consignments.show', $consignment)
            ->with('success', 'Data konsinyasi berhasil diperbarui.');
    }

    /**
     * Menghapus transaksi konsinyasi.
     */
    public function destroy(Consignment $consignment)
    {
        $this->authorizeConsignment($consignment);

        $user = auth()->user();

        if (!$user->isUmkm() && !$user->isAdmin()) {
            abort(403);
        }

        if ($consignment->sales()->exists() ||
            $consignment->returns()->exists()) {
            return back()->withErrors([
                'status' => 'Transaksi tidak dapat dihapus karena sudah memiliki catatan penjualan atau retur.'
            ]);
        }

        if ($consignment->status === 'settled') {
            return back()->withErrors([
                'status' => 'Transaksi yang sudah diselesaikan tidak dapat dihapus.'
            ]);
        }

        DB::transaction(function () use ($consignment) {

            $product = Product::whereKey($consignment->product_id)
                ->lockForUpdate()
                ->firstOrFail();

            // Mengembalikan stok karena barang belum terjual
            // dan belum diretur.
            $product->increment('stock', $consignment->quantity);

            $consignment->delete();
        });

        return redirect()
            ->route('consignments.index')
            ->with('success', 'Transaksi konsinyasi berhasil dihapus.');
    }

    /**
     * Memeriksa hak akses terhadap transaksi.
     */
    private function authorizeConsignment(Consignment $consignment): void
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return;
        }

        if ($user->isUmkm()) {
            $isOwner = $consignment->product
                ->umkm_id == $user->id;

            if (!$isOwner) {
                abort(403, 'Anda tidak memiliki akses ke transaksi ini.');
            }

            return;
        }

        if ($user->isStore()) {
            if ($consignment->store_id != $user->store_id) {
                abort(403, 'Transaksi ini bukan milik toko Anda.');
            }

            return;
        }

        abort(403);
    }
}
