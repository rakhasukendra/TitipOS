<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Consignment extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_REJECTED = 'rejected';

    public const STATUS_SETTLED = 'settled';

    public const STATUS_RETURNED = 'returned';

    protected $fillable = ['product_id', 'store_id', 'settlement_id', 'quantity', 'consignment_date', 'status'];

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

    public function settlement(): BelongsTo
    {
        return $this->belongsTo(Settlement::class);
    }

    /**
     * Batasi titipan sesuai akses pengguna: admin melihat semua, toko melihat miliknya,
     * dan UMKM melihat titipan dari produknya sendiri.
     */
    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->isAdmin()) {
            return $query;
        }

        if ($user->isStoreManager()) {
            return $query->where('store_id', $user->store_id);
        }

        return $query->whereHas('product', function (Builder $productQuery) use ($user) {
            $productQuery->where('umkm_id', $user->id);
        });
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * @return array<string, string>
     */
    public static function statusOptions(): array
    {
        return [
            self::STATUS_PENDING => 'Menunggu persetujuan',
            self::STATUS_ACTIVE => 'Aktif',
            self::STATUS_REJECTED => 'Ditolak',
            self::STATUS_SETTLED => 'Ter-settle',
            self::STATUS_RETURNED => 'Dikembalikan',
        ];
    }

    /**
     * Status ajuan yang bisa dipilih toko maupun admin dari halaman titipan.
     * Status settlement dan retur tetap ditetapkan lewat menu masing-masing.
     *
     * @return array<string, string>
     */
    public static function approvalStatusOptions(): array
    {
        return array_intersect_key(self::statusOptions(), array_flip([
            self::STATUS_PENDING,
            self::STATUS_ACTIVE,
            self::STATUS_REJECTED,
        ]));
    }

    public function statusLabel(): string
    {
        return self::statusOptions()[$this->status] ?? ucfirst((string) $this->status);
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            self::STATUS_ACTIVE => 'badge-success',
            self::STATUS_PENDING => 'badge-warning',
            self::STATUS_SETTLED => 'badge-info',
            self::STATUS_REJECTED, self::STATUS_RETURNED => 'badge-danger',
            default => 'badge-neutral',
        };
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
