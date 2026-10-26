<?php

namespace App\Http\Controllers;

use App\Models\Consignment;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Http\Request;

class ConsignmentController extends Controller
{
    public function index()
    {
        $consignments = Consignment::with(['product', 'store'])
            ->latest()
            ->paginate(10);
        return view('consignments.index', compact('consignments'));
    }

    public function create()
    {
        $products = Product::where('umkm_id', auth()->id())->get();
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
        
        if ($product->stock < $validated['quantity']) {
            return back()->withErrors(['quantity' => 'Stok tidak mencukupi']);
        }

        $product->decrement('stock', $validated['quantity']);

        Consignment::create([
            'product_id' => $validated['product_id'],
            'store_id' => $validated['store_id'],
            'quantity' => $validated['quantity'],
            'consignment_date' => $validated['consignment_date'],
            'status' => 'active',
        ]);

        return redirect()->route('consignments.index')->with('success', 'Titipan berhasil dibuat');
    }

    public function show(Consignment $consignment)
    {
        $consignment->load('product', 'store', 'sales', 'returns');
        return view('consignments.show', compact('consignment'));
    }

    public function edit(Consignment $consignment)
    {
        return view('consignments.edit', compact('consignment'));
    }

    public function update(Request $request, Consignment $consignment)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
            'consignment_date' => 'required|date',
            'status' => 'required|in:active,settled,returned',
        ]);

        $consignment->update($validated);

        return redirect()->route('consignments.index')->with('success', 'Titipan berhasil diperbarui');
    }

    public function destroy(Consignment $consignment)
    {
        if ($consignment->status === 'settled') {
            return back()->withErrors(['status' => 'Titipan yang sudah settled tidak bisa dihapus']);
        }

        $consignment->product->increment('stock', $consignment->quantity);
        $consignment->delete();

        return redirect()->route('consignments.index')->with('success', 'Titipan berhasil dihapus');
    }
}
