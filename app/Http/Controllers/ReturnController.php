<?php

namespace App\Http\Controllers;

use App\Models\Consignment;
use App\Models\ReturnItem;
use Illuminate\Http\Request;

class ReturnController extends Controller
{
    public function index()
    {
        $returns = ReturnItem::with(['consignment.product', 'consignment.store'])
            ->latest()
            ->paginate(10);
        return view('returns.index', compact('returns'));
    }

    public function create()
    {
        $consignments = Consignment::with(['product', 'store'])
            ->where('status', 'active')
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
        
        $soldQuantity = $consignment->sales()->sum('quantity');
        $returnedQuantity = $consignment->returns()->sum('quantity');
        $remainingQuantity = $consignment->quantity - $soldQuantity - $returnedQuantity;

        if ($validated['quantity'] > $remainingQuantity) {
            return back()->withErrors(['quantity' => 'Jumlah melebihi sisa barang']);
        }

        ReturnItem::create($validated);

        $consignment->product->increment('stock', $validated['quantity']);

        return redirect()->route('returns.index')->with('success', 'Retur berhasil dicatat');
    }

    public function show(ReturnItem $return)
    {
        $return->load('consignment.product', 'consignment.store');
        return view('returns.show', compact('return'));
    }

    public function destroy(ReturnItem $return)
    {
        $return->consignment->product->decrement('stock', $return->quantity);
        $return->delete();
        return redirect()->route('returns.index')->with('success', 'Retur berhasil dihapus');
    }
}
