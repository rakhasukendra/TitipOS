<?php

namespace App\Http\Controllers;

use App\Models\Consignment;
use App\Models\Sale;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    public function index()
    {
        $query = Sale::query()->visibleTo(auth()->user());

        $sales = (clone $query)->with(['consignment.product', 'consignment.store'])
            ->latest()
            ->paginate(10);

        $stats = [
            'total' => (clone $query)->count(),
            'units' => (int) (clone $query)->sum('quantity'),
            'revenue' => (float) (clone $query)->sum('amount'),
        ];
        $stats['average'] = $stats['total'] > 0 ? $stats['revenue'] / $stats['total'] : 0.0;

        return view('sales.index', compact('sales', 'stats'));
    }

    public function create()
    {
        $consignments = Consignment::with(['product', 'store', 'sales', 'returns'])
            ->where('status', 'active')
            ->whereNull('settlement_id')
            ->visibleTo(auth()->user())
            ->get();

        return view('sales.create', compact('consignments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'consignment_id' => 'required|exists:consignments,id',
            'quantity' => 'required|integer|min:1',
            'sale_date' => 'required|date',
        ]);

        $consignment = Consignment::find($validated['consignment_id']);

        $this->authorizeConsignment($consignment);

        if ($consignment->status !== 'active' || $consignment->settlement_id !== null) {
            return back()->withErrors(['consignment_id' => 'Titipan ini sudah di-settle dan tidak bisa dijual lagi'])->withInput();
        }

        $soldQuantity = $consignment->sales()->sum('quantity');
        $returnedQuantity = $consignment->returns()->sum('quantity');
        $remainingQuantity = $consignment->quantity - $soldQuantity - $returnedQuantity;

        if ($validated['quantity'] > $remainingQuantity) {
            return back()->withErrors(['quantity' => 'Jumlah melebihi sisa titipan'])->withInput();
        }

        $amount = $consignment->product->price * $validated['quantity'];

        Sale::create([
            'consignment_id' => $validated['consignment_id'],
            'quantity' => $validated['quantity'],
            'amount' => $amount,
            'sale_date' => $validated['sale_date'],
        ]);

        return redirect()->route('sales.index')->with('success', 'Penjualan berhasil dicatat');
    }

    public function show(Sale $sale)
    {
        $isVisible = Sale::visibleTo(auth()->user())
            ->whereKey($sale->getKey())
            ->exists();

        if (! $isVisible) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk penjualan ini.');
        }

        $sale->load('consignment.product', 'consignment.store');

        return view('sales.show', compact('sale'));
    }

    public function destroy(Sale $sale)
    {
        $this->authorizeConsignment($sale->consignment);

        if ($sale->consignment->settlement_id !== null) {
            return back()->withErrors(['status' => 'Penjualan yang sudah masuk settlement tidak bisa dihapus']);
        }

        $sale->delete();

        return redirect()->route('sales.index')->with('success', 'Penjualan berhasil dihapus');
    }

    /**
     * Toko hanya boleh mencatat penjualan untuk titipan di tokonya, admin untuk semua.
     */
    private function authorizeConsignment(Consignment $consignment): void
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return;
        }

        if (! $user->isStoreManager() || $consignment->store_id !== $user->store_id) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk titipan ini.');
        }
    }
}
