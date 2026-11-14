<?php

namespace App\Http\Controllers;

use App\Models\Consignment;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ConsignmentController extends Controller
{
    public function index()
    {
        $query = Consignment::query()->visibleTo(auth()->user());

        $consignments = (clone $query)->with(['product.umkm', 'store'])
            ->latest()
            ->paginate(10);

        $stats = [
            'total' => (int) (clone $query)->sum('quantity'),
            'pending' => (int) (clone $query)->where('status', Consignment::STATUS_PENDING)->sum('quantity'),
            'active' => (int) (clone $query)->where('status', Consignment::STATUS_ACTIVE)->sum('quantity'),
            'sold' => (int) Sale::whereIn('consignment_id', (clone $query)->select('id'))->sum('quantity'),
        ];

        $pendingCount = (clone $query)->where('status', Consignment::STATUS_PENDING)->count();

        $statusOptions = Consignment::approvalStatusOptions();

        return view('consignments.index', compact('consignments', 'pendingCount', 'statusOptions', 'stats'));
    }

    public function create()
    {
        $products = Product::query()
            ->when(! auth()->user()->isAdmin(), function ($query) {
                $query->where('umkm_id', auth()->id());
            })
            ->get();
        $stores = Store::all();

        return view('consignments.create', compact('products', 'stores'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'store_id' => 'required|exists:stores,id',
            'quantity' => 'required|integer|min:1',
            'consignment_date' => 'required|date',
        ]);

        $product = Product::find($validated['product_id']);
        $this->authorizeProductOwner($product);

        if ($product->stock < $validated['quantity']) {
            return back()->withErrors(['quantity' => 'Stok tidak mencukupi'])->withInput();
        }

        DB::transaction(function () use ($validated, $product) {
            $product->decrement('stock', $validated['quantity']);

            Consignment::create([
                'product_id' => $validated['product_id'],
                'store_id' => $validated['store_id'],
                'quantity' => $validated['quantity'],
                'consignment_date' => $validated['consignment_date'],
                'status' => Consignment::STATUS_PENDING,
            ]);
        });

        return redirect()->route('consignments.index')
            ->with('success', 'Ajuan titipan terkirim. Menunggu persetujuan toko.');
    }

    public function approve(Consignment $consignment)
    {
        $this->authorizeStoreAccess($consignment);

        if (! $consignment->isPending()) {
            return back()->withErrors(['status' => 'Hanya ajuan yang menunggu persetujuan yang bisa diterima']);
        }

        $consignment->update(['status' => Consignment::STATUS_ACTIVE]);

        return back()->with('success', 'Titipan diterima dan siap dijual di toko');
    }

    public function reject(Consignment $consignment)
    {
        $this->authorizeStoreAccess($consignment);

        if (! $consignment->isPending()) {
            return back()->withErrors(['status' => 'Hanya ajuan yang menunggu persetujuan yang bisa ditolak']);
        }

        DB::transaction(function () use ($consignment) {
            $consignment->update(['status' => Consignment::STATUS_REJECTED]);
            $consignment->product->increment('stock', $consignment->quantity);
        });

        return back()->with('success', 'Ajuan titipan ditolak dan stok dikembalikan ke UMKM');
    }

    public function show(Consignment $consignment)
    {
        $this->authorizeView($consignment);

        $consignment->load('product', 'store', 'sales', 'returns');

        $statusOptions = Consignment::approvalStatusOptions();

        return view('consignments.show', compact('consignment', 'statusOptions'));
    }

    public function edit(Consignment $consignment)
    {
        $this->authorizeOwner($consignment);

        return view('consignments.edit', compact('consignment'));
    }

    public function update(Request $request, Consignment $consignment)
    {
        $this->authorizeOwner($consignment);

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
            'consignment_date' => 'required|date',
            'status' => ['required', Rule::in(array_keys(Consignment::statusOptions()))],
        ]);

        if ($consignment->settlement_id !== null) {
            return back()->withErrors(['status' => 'Titipan yang sudah masuk settlement tidak bisa diubah']);
        }

        $previousStatus = $consignment->status;
        $newStatus = $validated['status'];
        $statusChanged = $newStatus !== $previousStatus;

        $soldQuantity = $consignment->sales()->sum('quantity');
        $returnedQuantity = $consignment->returns()->sum('quantity');
        $lockedQuantity = $soldQuantity + $returnedQuantity;

        if ($validated['quantity'] < $lockedQuantity) {
            return back()->withErrors([
                'quantity' => "Jumlah titipan tidak boleh kurang dari {$lockedQuantity} pcs yang sudah terjual atau diretur",
            ])->withInput();
        }

        $product = $consignment->product;
        $quantityDifference = $validated['quantity'] - $consignment->quantity;

        if ($statusChanged
            && ($previousStatus === Consignment::STATUS_REJECTED || $newStatus === Consignment::STATUS_REJECTED)
            && $quantityDifference !== 0) {
            return back()->withErrors([
                'quantity' => 'Ubah jumlah terpisah dari perubahan status. Simpan salah satu terlebih dahulu.',
            ])->withInput();
        }

        $statusStockChange = $this->statusStockChange($consignment, $newStatus);

        if ($statusStockChange['error'] !== null) {
            return back()->withErrors(['status' => $statusStockChange['error']])->withInput();
        }

        if ($quantityDifference > 0 && $product->stock < $quantityDifference) {
            return back()->withErrors([
                'quantity' => 'Stok produk tidak mencukupi untuk menambah jumlah titipan',
            ])->withInput();
        }

        DB::transaction(function () use ($consignment, $validated, $product, $quantityDifference, $statusStockChange) {
            $stockChange = $statusStockChange['stock_change'] - $quantityDifference;

            if ($stockChange !== 0) {
                $product->update(['stock' => $product->stock + $stockChange]);
            }

            $consignment->update($validated);
        });

        return redirect()->route('consignments.index')->with('success', 'Titipan berhasil diperbarui');
    }

    /**
     * Ubah status titipan langsung dari akun toko atau admin.
     */
    public function updateStatus(Request $request, Consignment $consignment)
    {
        $this->authorizeStoreAccess($consignment);

        $validated = $request->validate([
            'status' => ['required', Rule::in(array_keys(Consignment::approvalStatusOptions()))],
        ]);

        if ($consignment->settlement_id !== null) {
            return back()->withErrors(['status' => 'Titipan yang sudah masuk settlement tidak bisa diubah']);
        }

        if ($validated['status'] === $consignment->status) {
            return back()->with('success', 'Status titipan tidak berubah');
        }

        $statusStockChange = $this->statusStockChange($consignment, $validated['status']);

        if ($statusStockChange['error'] !== null) {
            return back()->withErrors(['status' => $statusStockChange['error']]);
        }

        DB::transaction(function () use ($consignment, $validated, $statusStockChange) {
            if ($statusStockChange['stock_change'] !== 0) {
                $consignment->product->update([
                    'stock' => $consignment->product->stock + $statusStockChange['stock_change'],
                ]);
            }

            $consignment->update(['status' => $validated['status']]);
        });

        return back()->with('success', 'Status titipan diubah menjadi '.$consignment->statusLabel());
    }

    public function destroy(Consignment $consignment)
    {
        $this->authorizeOwner($consignment);

        if ($consignment->status === Consignment::STATUS_SETTLED || $consignment->settlement_id !== null) {
            return back()->withErrors(['status' => 'Titipan yang sudah masuk settlement tidak bisa dihapus']);
        }

        if ($consignment->sales()->exists() || $consignment->returns()->exists()) {
            return back()->withErrors(['status' => 'Titipan yang sudah memiliki penjualan atau retur tidak bisa dihapus']);
        }

        if ($consignment->status !== Consignment::STATUS_REJECTED) {
            $consignment->product->increment('stock', $consignment->quantity);
        }

        $consignment->delete();

        return redirect()->route('consignments.index')->with('success', 'Titipan berhasil dihapus');
    }

    /**
     * Toko boleh melihat titipan tokonya, UMKM hanya titipan produknya, admin semuanya.
     */
    private function authorizeView(Consignment $consignment): void
    {
        $isVisible = Consignment::visibleTo(auth()->user())
            ->whereKey($consignment->getKey())
            ->exists();

        if (! $isVisible) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk titipan ini.');
        }
    }

    /**
     * Perubahan data titipan hanya untuk pemilik produk atau admin.
     */
    private function authorizeOwner(Consignment $consignment): void
    {
        $this->authorizeProductOwner($consignment->product);
    }

    /**
     * Penerimaan, penolakan, atau perubahan status ajuan hanya untuk toko tujuan atau admin.
     */
    private function authorizeStoreAccess(Consignment $consignment): void
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return;
        }

        if (! $user->isStoreManager() || $consignment->store_id !== $user->store_id) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk meninjau titipan ini.');
        }
    }

    /**
     * Hitung penyesuaian stok saat status titipan berubah.
     *
     * @return array{error: ?string, stock_change: int}
     */
    private function statusStockChange(Consignment $consignment, string $newStatus): array
    {
        if ($newStatus === $consignment->status) {
            return ['error' => null, 'stock_change' => 0];
        }

        if ($newStatus === Consignment::STATUS_REJECTED
            && ($consignment->sales()->exists() || $consignment->returns()->exists())) {
            return [
                'error' => 'Titipan yang sudah memiliki penjualan atau retur tidak bisa ditolak',
                'stock_change' => 0,
            ];
        }

        $holdsStock = fn (string $status): bool => in_array($status, [
            Consignment::STATUS_PENDING,
            Consignment::STATUS_ACTIVE,
            Consignment::STATUS_SETTLED,
        ], true);

        $reactivatedQuantity = ! $holdsStock($consignment->status) && $holdsStock($newStatus)
            ? $consignment->quantity
            : 0;
        $releasedQuantity = $holdsStock($consignment->status) && ! $holdsStock($newStatus)
            ? $consignment->quantity
            : 0;

        if ($reactivatedQuantity > 0 && $consignment->product->stock < $reactivatedQuantity) {
            return [
                'error' => "Stok produk tidak mencukupi untuk mengaktifkan {$reactivatedQuantity} pcs titipan ini kembali",
                'stock_change' => 0,
            ];
        }

        return ['error' => null, 'stock_change' => $releasedQuantity - $reactivatedQuantity];
    }

    private function authorizeProductOwner(Product $product): void
    {
        if (! auth()->user()->isAdmin() && $product->umkm_id !== auth()->id()) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk titipan ini.');
        }
    }
}
