<?php

namespace App\Http\Controllers;

use App\Models\Consignment;
use App\Models\Settlement;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettlementController extends Controller
{
    public function index()
    {
        $settlements = Settlement::with(['store', 'umkm'])
            ->latest()
            ->paginate(10);

        $stats = [
            'total' => Settlement::count(),
            'paid' => (float) Settlement::whereNotNull('settled_at')->sum('net_amount'),
            'pending' => (float) Settlement::whereNull('settled_at')->sum('net_amount'),
        ];

        return view('settlements.index', compact('settlements', 'stats'));
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

        $consignments = $this->unsettledConsignments($validated['store_id'], $validated['umkm_id']);

        if ($consignments->isEmpty()) {
            return back()->withErrors([
                'store_id' => 'Tidak ada titipan aktif yang belum di-settle untuk toko dan UMKM ini',
            ])->withInput();
        }

        $totalSales = 0;
        $totalReturns = 0;

        foreach ($consignments as $consignment) {
            $totalSales += $consignment->sales()->sum('amount');
            $totalReturns += $consignment->returns()->sum('quantity') * $consignment->product->price;
        }

        $commission = ($totalSales - $totalReturns) * ($validated['commission_rate'] / 100);
        $netAmount = $totalSales - $totalReturns - $commission;

        DB::transaction(function () use ($validated, $consignments, $totalSales, $totalReturns, $netAmount) {
            $settlement = Settlement::create([
                'store_id' => $validated['store_id'],
                'umkm_id' => $validated['umkm_id'],
                'total_sales' => $totalSales,
                'total_returns' => $totalReturns,
                'commission_rate' => $validated['commission_rate'],
                'net_amount' => $netAmount,
                'settlement_date' => $validated['settlement_date'],
            ]);

            Consignment::whereKey($consignments->modelKeys())->update([
                'settlement_id' => $settlement->id,
                'status' => 'settled',
            ]);
        });

        return redirect()->route('settlements.index')->with('success', 'Settlement berhasil dibuat');
    }

    public function show(Settlement $settlement)
    {
        $settlement->load('store', 'umkm');

        $consignments = $settlement->consignments()
            ->with(['product', 'sales', 'returns'])
            ->get();

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

        DB::transaction(function () use ($settlement) {
            $settlement->consignments()->update([
                'settlement_id' => null,
                'status' => 'active',
            ]);

            $settlement->delete();
        });

        return redirect()->route('settlements.index')->with('success', 'Settlement berhasil dihapus');
    }

    /**
     * Titipan aktif milik toko dan UMKM yang belum pernah masuk settlement.
     */
    private function unsettledConsignments(int $storeId, int $umkmId): Collection
    {
        return Consignment::where([
            'store_id' => $storeId,
        ])->with('product')
            ->whereNull('settlement_id')
            ->where('status', 'active')
            ->whereHas('product', function ($query) use ($umkmId) {
                $query->where('umkm_id', $umkmId);
            })->get();
    }
}
