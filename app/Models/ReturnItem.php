<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnItem extends Model
{
    protected $table = 'returns';

    protected $fillable = [
        'consignment_id',
        'quantity',
        'reason',
        'return_date',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'return_date' => 'date',
        ];
    }

    // Transaksi konsinyasi asal retur.
    public function consignment(): BelongsTo
    {
        return $this->belongsTo(Consignment::class);
    }
}
