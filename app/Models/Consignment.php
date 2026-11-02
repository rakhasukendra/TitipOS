<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Consignment extends Model
{
    protected $fillable = [
        'product_id',
        'store_id',
        'quantity',
        'consignment_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'consignment_date' => 'date',
        ];
    }

    // Produk yang dititipkan.
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // Toko penerima titipan.
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    // Riwayat penjualan dari transaksi titipan.
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    // Riwayat retur dari transaksi titipan.
    public function returns(): HasMany
    {
        return $this->hasMany(ReturnItem::class);
    }

    // Menghitung total barang yang sudah terjual.
    public function getTotalSoldAttribute(): int
    {
        return (int) $this->sales()->sum('quantity');
    }

    // Menghitung total barang yang sudah diretur.
    public function getTotalReturnedAttribute(): int
    {
        return (int) $this->returns()->sum('quantity');
    }

    // Menghitung sisa stok titipan di toko.
    public function getRemainingStockAttribute(): int
    {
        return max(
            0,
            $this->quantity
            - $this->total_sold
            - $this->total_returned
        );
    }

    // Menghitung nilai penjualan.
    public function getTotalSalesAttribute(): float
    {
        return (float) $this->sales()->sum('amount');
    }
}
