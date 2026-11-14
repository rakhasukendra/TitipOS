<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index()
    {
        $query = Product::query()
            ->when(! auth()->user()->isAdmin(), function ($query) {
                $query->where('umkm_id', auth()->id());
            });

        $products = (clone $query)->with('umkm')->latest()->paginate(10);

        $stats = [
            'total' => (clone $query)->count(),
            'stock' => (int) (clone $query)->sum('stock'),
            'value' => (float) (clone $query)->sum(DB::raw('price * stock')),
        ];

        return view('products.index', compact('products', 'stats'));
    }

    public function create()
    {
        $umkms = $this->umkmOwners();

        return view('products.create', compact('umkms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'umkm_id' => 'nullable|integer|exists:users,id',
        ]);

        if (auth()->user()->isAdmin()) {
            $owner = User::find($validated['umkm_id'] ?? null);

            if (! $owner?->isUmkm()) {
                return back()->withErrors(['umkm_id' => 'Pemilik produk harus akun UMKM'])->withInput();
            }

            $validated['umkm_id'] = $owner->id;
        } else {
            $validated['umkm_id'] = auth()->id();
        }

        Product::create($validated);

        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan');
    }

    public function show(Product $product)
    {
        $this->authorizeProduct($product);

        $product->load('umkm', 'consignments.store');

        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $this->authorizeProduct($product);

        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $this->authorizeProduct($product);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        $product->update($validated);

        return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui');
    }

    public function destroy(Product $product)
    {
        $this->authorizeProduct($product);

        if ($product->consignments()->exists()) {
            return back()->withErrors(['product' => 'Produk yang sudah memiliki riwayat titipan tidak bisa dihapus']);
        }

        $product->delete();

        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus');
    }

    /**
     * Produk hanya boleh dikelola pemiliknya sendiri atau admin.
     */
    private function authorizeProduct(Product $product): void
    {
        if (! auth()->user()->isAdmin() && $product->umkm_id !== auth()->id()) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk produk ini.');
        }
    }

    /**
     * Daftar UMKM yang bisa dipilih admin sebagai pemilik produk.
     */
    private function umkmOwners(): Collection
    {
        return User::whereHas('role', function ($query) {
            $query->where('slug', 'umkm');
        })->orderBy('name')->get();
    }
}
