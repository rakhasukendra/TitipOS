<?php

namespace App\Http\Controllers;

use App\Models\Consignment;
use App\Models\Settlement;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;

class SettlementController extends Controller
{
    public function index()
    {
        $settlements = Settlement::with(['store', 'umkm'])
            ->latest()
            ->paginate(10);
        return view('settlements.index', compact('settlements'));
    }

    public function create()
    {
        $stores = Store::all();
        $umkms = User::whereHas('role', function ($q) {
            $q->where('slug', 'umkm');
        })->get();
        return view('settlements.create', compact('stores', 'umkms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'store_id' => 'required|exists:stores,id',
            'umkm_id' => 'required|exists:users,id',
            'commission_rate' => 'required|numeric|min:0|max:100',
            'settlement_date' => 'required|date',
        ]);

        $consignments = Consignment::where([
            'store_id' => $validated['store_id'],
        ])->with('product')
         ->whereHas('product', function ($q) use ($validated) {
             $q->where('umkm_id', $validated['umkm_id']);
         })->get();

        $totalSales = 0;
        $totalReturns = 0;

        foreach ($consignments as $consignment) {
            $totalSales += $consignment->sales()->sum('amount');
            $returnQty = $consignment->returns()->sum('quantity');
            $totalReturns += $returnQty * $consignment->product->price;
        }

        $commission = ($totalSales - $totalReturns) * ($validated['commission_rate'] / 100);
        $netAmount = $totalSales - $totalReturns - $commission;

        Settlement::create([
            'store_id' => $validated['store_id'],
            'umkm_id' => $validated['umkm_id'],
            'total_sales' => $totalSales,
            'total_returns' => $totalReturns,
            'commission_rate' => $validated['commission_rate'],
            'net_amount' => $netAmount,
            'settlement_date' => $validated['settlement_date'],
        ]);

        return redirect()->route('settlements.index')->with('success', 'Settlement berhasil dibuat');
    }

    public function show(Settlement $settlement)
    {
        $settlement->load('store', 'umkm');
        $consignments = Consignment::where([
            'store_id' => $settlement->store_id,
        ])->with(['product', 'sales', 'returns'])
         ->whereHas('product', function ($q) use ($settlement) {
             $q->where('umkm_id', $settlement->umkm_id);
         })->get();
        
        return view('settlements.show', compact('settlement', 'consignments'));
    }

    public function update(Settlement $settlement)
    {
        if ($settlement->settled_at) {
            return back()->withErrors(['status' => 'Settlement sudah di-mark sebagai settled']);
        }

        $settlement->update(['settled_at' => now()]);
        return redirect()->route('settlements.show', $settlement)->with('success', 'Settlement berhasil di-mark sebagai settled');
    }

    public function destroy(Settlement $settlement)
    {
        if ($settlement->settled_at) {
            return back()->withErrors(['status' => 'Settlement yang sudah settled tidak bisa dihapus']);
        }

        $settlement->delete();
        return redirect()->route('settlements.index')->with('success', 'Settlement berhasil dihapus');
    }
}
