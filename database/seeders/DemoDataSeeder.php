<?php

namespace Database\Seeders;

use App\Models\Consignment;
use App\Models\Product;
use App\Models\ReturnItem;
use App\Models\Sale;
use App\Models\Settlement;
use App\Models\Store;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DemoDataSeeder extends Seeder
{
    /**
     * Jumlah bulan data dummy, termasuk bulan berjalan.
     */
    private const MONTHS = 6;

    /**
     * Stok awal tiap produk agar cukup untuk seluruh titipan demo.
     */
    private const BASE_STOCK = 500;

    private const COMMISSION_RATE = 10;

    public function run(): void
    {
        $products = Product::query()->get();
        $stores = Store::query()->get();

        if ($products->isEmpty() || $stores->isEmpty()) {
            $this->command?->warn('Data dummy dilewati: belum ada produk atau toko.');

            return;
        }

        DB::transaction(function () use ($products, $stores): void {
            $this->clearTransactionData();

            $products->each(fn (Product $product) => $product->update([
                'stock' => max(self::BASE_STOCK, $product->stock),
            ]));

            $firstMonth = now()->startOfMonth()->subMonthsNoOverflow(self::MONTHS - 1);

            for ($offset = 0; $offset < self::MONTHS; $offset++) {
                $month = $firstMonth->copy()->addMonthsNoOverflow($offset);
                $consignments = $this->seedMonth($products, $stores, $month);

                if ($month->lt(now()->startOfMonth())) {
                    $this->createMonthlySettlements($consignments, $month);
                }
            }
        });

        $this->command?->info('Data titipan lama dihapus dan data dummy '.self::MONTHS.' bulan berhasil dibuat.');
    }

    /**
     * Hapus transaksi dari yang paling hilir agar foreign key tetap aman.
     */
    private function clearTransactionData(): void
    {
        ReturnItem::query()->delete();
        Sale::query()->delete();
        Consignment::query()->delete();
        Settlement::query()->delete();
    }

    /**
     * @param  Collection<int, Product>  $products
     * @param  Collection<int, Store>  $stores
     * @return Collection<int, Consignment>
     */
    private function seedMonth(Collection $products, Collection $stores, Carbon $month): Collection
    {
        $latestDate = $month->copy()->endOfMonth()->gt(now()) ? now() : $month->copy()->endOfMonth();
        $consignmentLatestDate = $month->copy()->addDays(20)->gt($latestDate)
            ? $latestDate->copy()
            : $month->copy()->addDays(20);

        $consignments = collect();

        foreach ($products as $product) {
            $consignmentCount = random_int(1, 2);

            for ($index = 0; $index < $consignmentCount; $index++) {
                $quantity = min(random_int(10, 24), $product->stock);

                if ($quantity < 5) {
                    continue;
                }

                $consignmentDate = $this->randomDateBetween($month->copy(), $consignmentLatestDate);
                $consignment = Consignment::create([
                    'product_id' => $product->id,
                    'store_id' => $stores->random()->id,
                    'quantity' => $quantity,
                    'consignment_date' => $consignmentDate,
                    'status' => 'active',
                ]);
                $consignment->forceFill([
                    'created_at' => $consignmentDate,
                    'updated_at' => $consignmentDate,
                ])->save();

                $product->decrement('stock', $quantity);

                if (random_int(1, 100) <= 15) {
                    $this->recordFullReturn($consignment, $product, $latestDate);
                } else {
                    $this->recordSalesAndReturns($consignment, $product, $latestDate);
                }

                $consignments->push($consignment);
            }
        }

        return $consignments;
    }

    /**
     * Titipan yang seluruh barangnya dikembalikan ke UMKM.
     */
    private function recordFullReturn(Consignment $consignment, Product $product, Carbon $latestDate): void
    {
        $returnDate = $this->randomDateBetween(
            $consignment->consignment_date->copy()->addDay(),
            $latestDate,
        );

        $return = ReturnItem::create([
            'consignment_id' => $consignment->id,
            'quantity' => $consignment->quantity,
            'reason' => 'Tidak laku',
            'return_date' => $returnDate,
        ]);
        $return->forceFill([
            'created_at' => $returnDate,
            'updated_at' => $returnDate,
        ])->save();

        $consignment->update(['status' => 'returned']);
        $product->increment('stock', $consignment->quantity);
    }

    /**
     * Sebagian barang terjual dalam beberapa transaksi, sebagian kecil diretur.
     */
    private function recordSalesAndReturns(Consignment $consignment, Product $product, Carbon $latestDate): void
    {
        $soldQuantity = max(1, (int) round($consignment->quantity * random_int(40, 75) / 100));
        $saleCount = random_int(1, 4);
        $remaining = $soldQuantity;

        for ($index = 1; $index <= $saleCount && $remaining > 0; $index++) {
            $maxChunk = max(1, intdiv($remaining, $saleCount - $index + 1));
            $quantity = $index === $saleCount ? $remaining : random_int(1, $maxChunk);
            $remaining -= $quantity;

            $saleDate = $this->randomDateBetween(
                $consignment->consignment_date->copy()->addDay(),
                $latestDate,
            );

            $sale = Sale::create([
                'consignment_id' => $consignment->id,
                'quantity' => $quantity,
                'amount' => (float) $product->price * $quantity,
                'sale_date' => $saleDate,
            ]);
            $sale->forceFill([
                'created_at' => $saleDate,
                'updated_at' => $saleDate,
            ])->save();
        }

        $returnableQuantity = $consignment->quantity - $soldQuantity;

        if ($returnableQuantity > 0 && random_int(1, 100) <= 20) {
            $returnQuantity = min(random_int(1, 3), $returnableQuantity);
            $returnDate = $this->randomDateBetween(
                $consignment->consignment_date->copy()->addDay(),
                $latestDate,
            );

            $return = ReturnItem::create([
                'consignment_id' => $consignment->id,
                'quantity' => $returnQuantity,
                'reason' => collect(['Barang rusak', 'Tidak laku', 'Kemasan rusak'])->random(),
                'return_date' => $returnDate,
            ]);
            $return->forceFill([
                'created_at' => $returnDate,
                'updated_at' => $returnDate,
            ])->save();

            $product->increment('stock', $returnQuantity);
        }
    }

    /**
     * Buat settlement per toko dan UMKM untuk titipan aktif pada bulan yang sudah lewat.
     *
     * @param  Collection<int, Consignment>  $consignments
     */
    private function createMonthlySettlements(Collection $consignments, Carbon $month): void
    {
        $settlementDate = $month->copy()->endOfMonth();

        Consignment::query()
            ->with(['product', 'sales', 'returns'])
            ->whereKey($consignments->pluck('id'))
            ->where('status', 'active')
            ->get()
            ->groupBy(fn (Consignment $consignment) => $consignment->store_id.'-'.$consignment->product->umkm_id)
            ->each(function (Collection $group) use ($settlementDate): void {
                $totalSales = $group->sum(fn (Consignment $consignment) => $consignment->sales->sum('amount'));
                $totalReturns = $group->sum(
                    fn (Consignment $consignment) => $consignment->returns->sum('quantity') * (float) $consignment->product->price,
                );
                $commission = ($totalSales - $totalReturns) * (self::COMMISSION_RATE / 100);
                $isPaid = $settlementDate->lt(now()->startOfMonth()->subMonthNoOverflow());

                $settlement = Settlement::create([
                    'store_id' => $group->first()->store_id,
                    'umkm_id' => $group->first()->product->umkm_id,
                    'total_sales' => $totalSales,
                    'total_returns' => $totalReturns,
                    'commission_rate' => self::COMMISSION_RATE,
                    'net_amount' => $totalSales - $totalReturns - $commission,
                    'settlement_date' => $settlementDate,
                    'settled_at' => $isPaid ? $settlementDate->copy()->addDays(3) : null,
                ]);
                $settlement->forceFill([
                    'created_at' => $settlementDate,
                    'updated_at' => $settlementDate,
                ])->save();

                Consignment::whereKey($group->pluck('id'))->update([
                    'settlement_id' => $settlement->id,
                    'status' => 'settled',
                ]);
            });
    }

    private function randomDateBetween(Carbon $start, Carbon $end): Carbon
    {
        if ($start->gt($end)) {
            return $end->copy();
        }

        return $start->copy()->addDays(random_int(0, (int) $start->diffInDays($end)));
    }
}
