<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Settlement extends Model
{
    protected $fillable = [
        'store_id',
        'umkm_id',
        'total_sales',
        'total_returns',
        'commission_rate',
        'net_amount',
        'settlement_date',
        'settled_at',
    ];

    protected function casts(): array
    {
        return [
            'total_sales' => 'decimal:2',
            'total_returns' => 'decimal:2',
            'commission_rate' => 'decimal:2',
            'net_amount' => 'decimal:2',
            'settlement_date' => 'date',
            'settled_at' => 'datetime',
        ];
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function umkm(): BelongsTo
    {
        return $this->belongsTo(User::class, 'umkm_id');
    }
}
