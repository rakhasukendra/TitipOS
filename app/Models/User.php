<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'name',
    'email',
    'password',
    'role_id',
    'store_id',
    'umkm_id'
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    // Toko yang dikelola oleh Store Manager.
    public function managedStore(): HasOne
    {
        return $this->hasOne(Store::class, 'manager_id');
    }

    // Produk milik UMKM Owner.
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'umkm_id');
    }

    // Transaksi konsinyasi produk milik UMKM.
    public function consignments(): HasMany
    {
        return $this->hasMany(
            Consignment::class,
            'umkm_id'
        );
    }

    public function settlements(): HasMany
    {
        return $this->hasMany(Settlement::class, 'umkm_id');
    }

    // Relasi untuk akun yang berada di bawah UMKM.
    public function umkmOwner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'umkm_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(User::class, 'umkm_id');
    }

    public function isAdmin(): bool
    {
        return $this->role?->slug === 'admin';
    }

    public function isUmkm(): bool
    {
        return $this->role?->slug === 'umkm';
    }

    public function isStore(): bool
    {
        return $this->role?->slug === 'store';
    }
}
