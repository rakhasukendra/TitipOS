<?php

namespace App\Http\Controllers;

use App\Models\Consignment;
use App\Models\Product;
use App\Models\Sale;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();
        $previousMonthStart = now()->subMonthNoOverflow()->startOfMonth();
        $previousMonthEnd = now()->subMonthNoOverflow()->endOfMonth();

        $stats = [
            'total_products' => Product::query()
                ->when(! $user->isAdmin(), function ($query) use ($user) {
                    $query->where('umkm_id', $user->id);
                })
                ->count(),
            'active_consignments' => Consignment::visibleTo($user)->where('status', 'active')->count(),
            'monthly_sales' => Sale::visibleTo($user)->whereBetween('sale_date', [$monthStart, $monthEnd])->count(),
            'previous_month_sales' => Sale::visibleTo($user)->whereBetween('sale_date', [$previousMonthStart, $previousMonthEnd])->count(),
            'total_revenue' => Sale::visibleTo($user)->sum('amount') ?? 0,
        ];

        $trendMonths = collect(range(5, 0))
            ->map(fn (int $monthsAgo) => now()->startOfMonth()->subMonthsNoOverflow($monthsAgo));

        $revenueByMonth = Sale::visibleTo($user)
            ->where('sale_date', '>=', $trendMonths->first())
            ->get(['amount', 'sale_date'])
            ->groupBy(fn (Sale $sale) => $sale->sale_date->format('Y-m'))
            ->map(fn ($sales) => (float) $sales->sum('amount'));

        $revenueTrend = $trendMonths->map(fn ($month) => [
            'label' => $month->format('M'),
            'total' => $revenueByMonth[$month->format('Y-m')] ?? 0.0,
        ]);

        $consignmentCounts = Consignment::visibleTo($user)
            ->toBase()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $statusBreakdown = collect(['pending', 'active', 'rejected', 'settled', 'returned'])
            ->map(fn (string $status) => [
                'status' => $status,
                'total' => (int) ($consignmentCounts[$status] ?? 0),
            ]);

        $topProducts = Sale::visibleTo($user)
            ->join('consignments', 'consignments.id', '=', 'sales.consignment_id')
            ->join('products', 'products.id', '=', 'consignments.product_id')
            ->selectRaw('products.name as name, sum(sales.amount) as revenue, sum(sales.quantity) as sold')
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('revenue')
            ->limit(5)
            ->get();

        $recentConsignments = Consignment::with(['product', 'store'])
            ->visibleTo($user)
            ->latest()
            ->take(5)
            ->get();

        $recentSales = Sale::with(['consignment.product', 'consignment.store'])
            ->visibleTo($user)
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', [
            'stats' => $stats,
            'revenueTrend' => $revenueTrend,
            'statusBreakdown' => $statusBreakdown,
            'topProducts' => $topProducts,
            'recentConsignments' => $recentConsignments,
            'recentSales' => $recentSales,
        ]);
    }
}
