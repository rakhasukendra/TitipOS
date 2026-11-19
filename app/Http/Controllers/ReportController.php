<?php

namespace App\Http\Controllers;

use App\Models\Consignment;
use App\Models\ReturnItem;
use App\Models\Sale;
use App\Models\Store;
use App\Models\User;
use App\Support\XlsxWriter;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index', $this->buildReport(auth()->user()));
    }

    public function export(): StreamedResponse
    {
        $user = auth()->user();
        $report = $this->buildReport($user);

        $writer = XlsxWriter::make([30, 16, 16, 16, 16, 22])
            ->addRow(['Laporan TitipOS'], 'title')
            ->addRow(['Akun', $user->name])
            ->addRow(['Peran', $user->role?->name ?? '-'])
            ->addRow(['Dibuat', now()->format('d/m/Y H:i')])
            ->addRow([])
            ->addRow(['Ringkasan per Toko'], 'title')
            ->addRow(['Toko', 'Titipan (unit)', 'Terjual (unit)', 'Retur (unit)', 'Sisa (unit)', 'Nilai Penjualan (Rp)'], 'header');

        foreach ($report['storeRows'] as $row) {
            $writer->addRow([
                $row['name'],
                $row['consigned'],
                $row['sold'],
                $row['returned'],
                $row['remaining'],
                ['value' => $row['revenue'], 'style' => 'money'],
            ]);
        }

        $writer->addRow([
            ['value' => 'Total', 'style' => 'total'],
            ['value' => $report['totals']['consigned'], 'style' => 'total'],
            ['value' => $report['totals']['sold'], 'style' => 'total'],
            ['value' => $report['totals']['returned'], 'style' => 'total'],
            ['value' => $report['totals']['remaining'], 'style' => 'total'],
            ['value' => $report['totals']['revenue'], 'style' => 'totalMoney'],
        ]);

        $writer->addRow([])
            ->addRow(['Penjualan Harian'], 'title')
            ->addRow(['Tanggal', 'Terjual (unit)', 'Nilai Penjualan (Rp)'], 'header');

        foreach ($report['salesByDate'] as $sale) {
            $writer->addRow([
                Carbon::parse($sale->date)->format('d/m/Y'),
                (int) $sale->qty,
                ['value' => (float) $sale->total, 'style' => 'money'],
            ]);
        }

        $writer->addRow([])
            ->addRow(['Retur Harian'], 'title')
            ->addRow(['Tanggal', 'Retur (unit)'], 'header');

        foreach ($report['returnsByDate'] as $return) {
            $writer->addRow([
                Carbon::parse($return->date)->format('d/m/Y'),
                (int) $return->qty,
            ]);
        }

        $filename = sprintf(
            'laporan-%s-%s.xlsx',
            $user->role?->slug ?? 'pengguna',
            now()->format('Ymd-His')
        );

        $contents = $writer->contents();

        return response()->streamDownload(function () use ($contents): void {
            echo $contents;
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * @return array{
     *     storeRows: Collection<int, array{name: string, consigned: int, sold: int, returned: int, remaining: int, revenue: float}>,
     *     totals: array{consigned: int, sold: int, returned: int, remaining: int, revenue: float},
     *     metrics: array<int, array{label: string, value: string}>,
     *     salesByDate: Collection<int, object>,
     *     returnsByDate: Collection<int, object>
     * }
     */
    private function buildReport(User $user): array
    {
        $stores = Store::query()
            ->when($user->isStoreManager(), function (Builder $query) use ($user) {
                $query->whereKey($user->store_id);
            })
            ->when($user->isUmkm(), function (Builder $query) use ($user) {
                $query->whereHas('consignments', function (Builder $query) use ($user) {
                    $query->visibleTo($user);
                });
            })
            ->with([
                'consignments' => function ($query) use ($user) {
                    $query->visibleTo($user)->with(['sales', 'returns']);
                },
            ])
            ->orderBy('name')
            ->get();

        $storeRows = $stores->map(function (Store $store) {
            $consignments = $store->consignments;
            $consigned = (int) $consignments->sum('quantity');
            $sold = (int) $consignments->sum(fn (Consignment $consignment) => $consignment->sales->sum('quantity'));
            $returned = (int) $consignments->sum(fn (Consignment $consignment) => $consignment->returns->sum('quantity'));

            return [
                'name' => $store->name,
                'consigned' => $consigned,
                'sold' => $sold,
                'returned' => $returned,
                'remaining' => $consigned - $sold - $returned,
                'revenue' => (float) $consignments->sum(fn (Consignment $consignment) => $consignment->sales->sum('amount')),
            ];
        });

        $totals = [
            'consigned' => (int) $storeRows->sum('consigned'),
            'sold' => (int) $storeRows->sum('sold'),
            'returned' => (int) $storeRows->sum('returned'),
            'remaining' => (int) $storeRows->sum('remaining'),
            'revenue' => (float) $storeRows->sum('revenue'),
        ];

        $metrics = [
            ['label' => 'Titipan', 'value' => $totals['consigned'].' unit'],
            ['label' => 'Terjual', 'value' => $totals['sold'].' unit'],
            ['label' => 'Retur', 'value' => $totals['returned'].' unit'],
            ['label' => 'Sisa', 'value' => $totals['remaining'].' unit'],
        ];

        $salesByDate = Sale::visibleTo($user)
            ->selectRaw('DATE(sale_date) as date, SUM(quantity) as qty, SUM(amount) as total')
            ->groupBy('date')
            ->orderByDesc('date')
            ->limit(10)
            ->get();

        $returnsByDate = ReturnItem::visibleTo($user)
            ->selectRaw('DATE(return_date) as date, SUM(quantity) as qty')
            ->groupBy('date')
            ->orderByDesc('date')
            ->limit(10)
            ->get();

        return [
            'storeRows' => $storeRows,
            'totals' => $totals,
            'metrics' => $metrics,
            'salesByDate' => $salesByDate,
            'returnsByDate' => $returnsByDate,
        ];
    }
}
