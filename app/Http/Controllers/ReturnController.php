<?php

namespace App\Http\Controllers;

use App\Models\Consignment;
use App\Models\ReturnItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReturnController extends Controller
{
    public function index()
    {
        $query = ReturnItem::query()->visibleTo(auth()->user());

        $returns = (clone $query)->with(['consignment.product', 'consignment.store'])
            ->latest()
            ->paginate(10);

        $stats = [
            'total' => (clone $query)->count(),
            'units' => (int) (clone $query)->sum('quantity'),
            'value' => (float) (clone $query)
                ->join('consignments', 'returns.consignment_id', '=', 'consignments.id')
                ->join('products', 'consignments.product_id', '=', 'products.id')
                ->sum(DB::raw('returns.quantity * products.price')),
        ];

        return view('returns.index', compact('returns', 'stats'));
    }

    public function create()
    {
        $consignments = Consignment::with(['product', 'store', 'sales', 'returns'])
            ->where('status', 'active')
            ->whereNull('settlement_id')
            ->visibleTo(auth()->user())
            ->get();

        return view('returns.create', compact('consignments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'consignment_id' => 'required|exists:consignments,id',
            'quantity' => 'required|integer|min:1',
            'reason' => 'nullable|string',
            'return_date' => 'required|date',
        ]);

        $consignment = Consignment::find($validated['consignment_id']);

        $this->authorizeConsignment($consignment);

        if ($consignment->status !== 'active' || $consignment->settlement_id !== null) {
            return back()->withErrors(['consignment_id' => 'Titipan ini sudah di-settle dan tidak bisa diretur'])->withInput();
        }

        $soldQuantity = $consignment->sales()->sum('quantity');
        $returnedQuantity = $consignment->returns()->sum('quantity');
        $remainingQuantity = $consignment->quantity - $soldQuantity - $returnedQuantity;

        if ($validated['quantity'] > $remainingQuantity) {
            return back()->withErrors(['quantity' => 'Jumlah melebihi sisa barang'])->withInput();
        }

        DB::transaction(function () use ($validated, $consignment) {
            ReturnItem::create($validated);
            $consignment->product->increment('stock', $validated['quantity']);
        });

        return redirect()->route('returns.index')->with('success', 'Retur berhasil dicatat');
    }

    public function show(ReturnItem $return)
    {
        $isVisible = ReturnItem::visibleTo(auth()->user())
            ->whereKey($return->getKey())
            ->exists();

        if (! $isVisible) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk retur ini.');
        }

        $return->load('consignment.product', 'consignment.store');

        return view('returns.show', compact('return'));
    }

    public function destroy(ReturnItem $return)
    {
        $this->authorizeConsignment($return->consignment);

        if ($return->consignment->settlement_id !== null) {
            return back()->withErrors(['status' => 'Retur yang sudah masuk settlement tidak bisa dihapus']);
        }

        DB::transaction(function () use ($return) {
            $return->consignment->product->decrement('stock', $return->quantity);
            $return->delete();
        });

        return redirect()->route('returns.index')->with('success', 'Retur berhasil dihapus');
    }

    /**
     * Toko hanya boleh mencatat retur untuk titipan di tokonya, admin untuk semua.
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
