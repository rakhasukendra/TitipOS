<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnItem extends Model
{
    protected $table = 'returns';

    protected $fillable = ['consignment_id', 'quantity', 'reason', 'return_date'];

    protected function casts(): array
    {
        return [
            'return_date' => 'date',
        ];
    }

    public function consignment(): BelongsTo
    {
        return $this->belongsTo(Consignment::class);
    }

    /**
     * Batasi retur sesuai akses pengguna: admin melihat semua, toko melihat retur
     * tokonya, dan UMKM melihat retur dari produknya sendiri.
     */
    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->isAdmin()) {
            return $query;
        }

        if ($user->isStoreManager()) {
            return $query->whereHas('consignment', function (Builder $consignmentQuery) use ($user) {
                $consignmentQuery->where('store_id', $user->store_id);
            });
        }

        return $query->whereHas('consignment.product', function (Builder $productQuery) use ($user) {
            $productQuery->where('umkm_id', $user->id);
        });
    }
}
