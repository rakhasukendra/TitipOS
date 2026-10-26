<?php

namespace App\Http\Controllers;

use App\Models\Consignment;
use App\Models\Sale;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    public function index()
    {
        $sales = Sale::with(['consignment.product', 'consignment.store'])
            ->latest()
            ->paginate(10);
        return view('sales.index', compact('sales'));
    }

    public function create()
    {
        $consignments = Consignment::with(['product', 'store'])
            ->where('status', 'active')
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
        
        $soldQuantity = $consignment->sales()->sum('quantity');
        $remainingQuantity = $consignment->quantity - $soldQuantity;

        if ($validated['quantity'] > $remainingQuantity) {
            return back()->withErrors(['quantity' => 'Jumlah melebihi sisa titipan']);
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
        $sale->load('consignment.product', 'consignment.store');
        return view('sales.show', compact('sale'));
    }

    public function destroy(Sale $sale)
    {
        $sale->delete();
        return redirect()->route('sales.index')->with('success', 'Penjualan berhasil dihapus');
    }
}
