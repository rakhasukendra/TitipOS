<?php

namespace App\Http\Controllers;

use App\Models\Consignment;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_products' => Product::count(),
            'active_consignments' => Consignment::where('status', 'active')->count(),
            'monthly_sales' => Sale::whereMonth('created_at', now()->month)->count(),
            'total_revenue' => Sale::sum('amount') ?? 0,
        ];

        $recentConsignments = Consignment::with(['product', 'store'])
            ->latest()
            ->take(5)
            ->get();

        $recentSales = Sale::with(['consignment.product', 'consignment.store'])
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', [
            'stats' => $stats,
            'recentConsignments' => $recentConsignments,
            'recentSales' => $recentSales,
        ]);
    }
}
