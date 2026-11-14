<?php

use App\Models\Consignment;
use App\Models\Product;
use App\Models\ReturnItem;
use App\Models\Role;
use App\Models\Sale;
use App\Models\Settlement;
use App\Models\Store;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

function userWithRole(string $slug): User
{
    $role = Role::firstOrCreate(['slug' => $slug], ['name' => ucfirst($slug)]);

    return User::factory()->create([
        'role_id' => $role->id,
        'password' => Hash::make('password'),
    ]);
}

function storeFor(User $manager, array $attributes = []): Store
{
    $store = Store::create(array_merge(['name' => 'Toko Bunga'], $attributes));
    $store->update(['manager_id' => $manager->id]);
    $manager->update(['store_id' => $store->id]);

    return $store;
}

function productFor(User $umkm, array $attributes = []): Product
{
    return Product::create(array_merge([
        'name' => 'Bunga Mawar',
        'description' => 'Mawar segar',
        'price' => 25000,
        'stock' => 10,
        'umkm_id' => $umkm->id,
    ], $attributes));
}

function consignmentFor(Product $product, Store $store, array $attributes = []): Consignment
{
    return Consignment::create(array_merge([
        'product_id' => $product->id,
        'store_id' => $store->id,
        'quantity' => 5,
        'consignment_date' => now()->toDateString(),
        'status' => 'active',
    ], $attributes));
}

test('umkm dapat membuat, melihat, mengubah, dan menghapus produk', function () {
    $umkm = userWithRole('umkm');

    $this->actingAs($umkm)->get(route('products.index'))->assertOk();
    $this->actingAs($umkm)->get(route('products.create'))->assertOk();

    $this->actingAs($umkm)
        ->post(route('products.store'), [
            'name' => 'Bunga Tulip',
            'description' => 'Tulip import',
            'price' => 35000,
            'stock' => 8,
        ])
        ->assertRedirect(route('products.index'));

    $product = Product::where('name', 'Bunga Tulip')->firstOrFail();
    expect($product->umkm_id)->toBe($umkm->id);

    $this->actingAs($umkm)->get(route('products.show', $product))->assertOk();
    $this->actingAs($umkm)->get(route('products.edit', $product))->assertOk();

    $this->actingAs($umkm)
        ->put(route('products.update', $product), [
            'name' => 'Bunga Tulip Pink',
            'description' => 'Tulip import',
            'price' => 40000,
            'stock' => 9,
        ])
        ->assertRedirect(route('products.index'));

    expect($product->fresh()->name)->toBe('Bunga Tulip Pink');

    $this->actingAs($umkm)->delete(route('products.destroy', $product))->assertRedirect(route('products.index'));
    expect(Product::find($product->id))->toBeNull();
});

test('umkm tidak bisa mengakses produk milik umkm lain', function () {
    $owner = userWithRole('umkm');
    $otherUmkm = userWithRole('umkm');
    $ownProduct = productFor($owner, ['name' => 'Produk Sendiri']);
    $otherProduct = productFor($otherUmkm, ['name' => 'Produk Orang Lain']);

    $this->actingAs($owner)->get(route('products.index'))
        ->assertOk()
        ->assertSee('Produk Sendiri')
        ->assertDontSee('Produk Orang Lain');

    $this->actingAs($owner)->get(route('products.edit', $otherProduct))->assertForbidden();
    $this->actingAs($owner)->get(route('products.show', $otherProduct))->assertForbidden();
    $this->actingAs($owner)->put(route('products.update', $otherProduct), [
        'name' => 'Dibajak',
        'price' => 1,
        'stock' => 1,
    ])->assertForbidden();
    $this->actingAs($owner)->delete(route('products.destroy', $otherProduct))->assertForbidden();

    expect($otherProduct->fresh()->name)->toBe('Produk Orang Lain');
});

test('admin dapat membuat produk untuk umkm yang dipilih', function () {
    $admin = userWithRole('admin');
    $umkm = userWithRole('umkm');

    $this->actingAs($admin)->get(route('products.create'))
        ->assertOk()
        ->assertSee('Pemilik (UMKM)');

    $this->actingAs($admin)
        ->post(route('products.store'), [
            'name' => 'Produk Admin',
            'price' => 10000,
            'stock' => 3,
            'umkm_id' => $umkm->id,
        ])
        ->assertRedirect(route('products.index'));

    expect(Product::where('name', 'Produk Admin')->firstOrFail()->umkm_id)->toBe($umkm->id);

    $this->actingAs($admin)->get(route('products.index'))
        ->assertOk()
        ->assertSee('Pemilik');

    $this->actingAs($admin)
        ->post(route('products.store'), [
            'name' => 'Produk Tanpa Pemilik',
            'price' => 10000,
            'stock' => 3,
            'umkm_id' => $admin->id,
        ])
        ->assertSessionHasErrors('umkm_id');
});

test('umkm tidak mendapat error server saat menghapus produk yang sudah dititipkan', function () {
    $umkm = userWithRole('umkm');
    $store = Store::create(['name' => 'Toko Bunga']);
    $product = productFor($umkm);
    consignmentFor($product, $store);

    $response = $this->actingAs($umkm)->delete(route('products.destroy', $product));

    $response->assertRedirect();
    $response->assertSessionHasErrors();
    expect(Product::find($product->id))->not->toBeNull();
});

test('umkm dapat membuat, mengubah, dan menghapus titipan', function () {
    $umkm = userWithRole('umkm');
    $store = Store::create(['name' => 'Toko Bunga']);
    $product = productFor($umkm, ['stock' => 10]);

    $this->actingAs($umkm)->get(route('consignments.create'))->assertOk();

    $this->actingAs($umkm)
        ->post(route('consignments.store'), [
            'product_id' => $product->id,
            'store_id' => $store->id,
            'quantity' => 4,
            'consignment_date' => now()->toDateString(),
        ])
        ->assertRedirect(route('consignments.index'));

    $consignment = Consignment::firstOrFail();
    expect($product->fresh()->stock)->toBe(6);

    $this->actingAs($umkm)->get(route('consignments.index'))->assertOk();
    $this->actingAs($umkm)->get(route('consignments.show', $consignment))->assertOk();
    $this->actingAs($umkm)->get(route('consignments.edit', $consignment))->assertOk();

    $this->actingAs($umkm)
        ->put(route('consignments.update', $consignment), [
            'quantity' => 6,
            'consignment_date' => now()->toDateString(),
            'status' => 'active',
        ])
        ->assertRedirect(route('consignments.index'));

    expect($consignment->fresh()->quantity)->toBe(6);
    expect($product->fresh()->stock)->toBe(4);

    $this->actingAs($umkm)->delete(route('consignments.destroy', $consignment))->assertRedirect(route('consignments.index'));
    expect(Consignment::find($consignment->id))->toBeNull();
    expect($product->fresh()->stock)->toBe(10);
});

test('umkm hanya melihat titipan dari produknya sendiri', function () {
    $owner = userWithRole('umkm');
    $otherUmkm = userWithRole('umkm');
    $store = Store::create(['name' => 'Toko Bunga']);
    $ownConsignment = consignmentFor(productFor($owner, ['name' => 'Mawar Sendiri']), $store);
    consignmentFor(productFor($otherUmkm, ['name' => 'Tulip Orang Lain']), $store);

    $this->actingAs($owner)->get(route('consignments.index'))
        ->assertOk()
        ->assertSee('Mawar Sendiri')
        ->assertDontSee('Tulip Orang Lain');

    $this->actingAs($otherUmkm)->get(route('consignments.show', $ownConsignment))->assertForbidden();
});

test('umkm tidak mendapat error server saat menghapus titipan yang sudah ada penjualannya', function () {
    $umkm = userWithRole('umkm');
    $store = Store::create(['name' => 'Toko Bunga']);
    $product = productFor($umkm);
    $consignment = consignmentFor($product, $store);

    Sale::create([
        'consignment_id' => $consignment->id,
        'quantity' => 1,
        'amount' => 25000,
        'sale_date' => now()->toDateString(),
    ]);

    $response = $this->actingAs($umkm)->delete(route('consignments.destroy', $consignment));

    $response->assertRedirect();
    $response->assertSessionHasErrors();
    expect(Consignment::find($consignment->id))->not->toBeNull();
});

test('store dapat mencatat dan menghapus penjualan', function () {
    $umkm = userWithRole('umkm');
    $storeUser = userWithRole('store');
    $store = storeFor($storeUser);
    $product = productFor($umkm);
    $consignment = consignmentFor($product, $store);

    $this->actingAs($storeUser)->get(route('sales.index'))->assertOk();
    $this->actingAs($storeUser)->get(route('sales.create'))->assertOk();

    $this->actingAs($storeUser)
        ->post(route('sales.store'), [
            'consignment_id' => $consignment->id,
            'quantity' => 2,
            'sale_date' => now()->toDateString(),
        ])
        ->assertRedirect(route('sales.index'));

    $sale = Sale::firstOrFail();
    expect((float) $sale->amount)->toBe(50000.0);

    $this->actingAs($storeUser)->get(route('sales.show', $sale))->assertOk();
    $this->actingAs($storeUser)->delete(route('sales.destroy', $sale))->assertRedirect(route('sales.index'));
    expect(Sale::find($sale->id))->toBeNull();
});

test('store tidak bisa mencatat penjualan untuk titipan toko lain', function () {
    $umkm = userWithRole('umkm');
    $storeUser = userWithRole('store');
    $otherStoreUser = userWithRole('store');
    $store = storeFor($storeUser);
    $otherStore = storeFor($otherStoreUser, ['name' => 'Toko Lain']);
    $product = productFor($umkm, ['name' => 'Mawar Toko Lain']);
    $otherConsignment = consignmentFor($product, $otherStore);

    $this->actingAs($storeUser)->get(route('sales.create'))
        ->assertOk()
        ->assertDontSee('Mawar Toko Lain');

    $this->actingAs($storeUser)
        ->post(route('sales.store'), [
            'consignment_id' => $otherConsignment->id,
            'quantity' => 1,
            'sale_date' => now()->toDateString(),
        ])
        ->assertForbidden();

    expect(Sale::count())->toBe(0);
    expect($store->id)->not->toBe($otherStore->id);
});

test('store dapat mencatat dan menghapus retur', function () {
    $umkm = userWithRole('umkm');
    $storeUser = userWithRole('store');
    $store = storeFor($storeUser);
    $product = productFor($umkm);
    $consignment = consignmentFor($product, $store);

    $this->actingAs($storeUser)->get(route('returns.index'))->assertOk();
    $this->actingAs($storeUser)->get(route('returns.create'))->assertOk();

    $this->actingAs($storeUser)
        ->post(route('returns.store'), [
            'consignment_id' => $consignment->id,
            'quantity' => 1,
            'reason' => 'Rusak',
            'return_date' => now()->toDateString(),
        ])
        ->assertRedirect(route('returns.index'));

    $return = ReturnItem::firstOrFail();
    expect($product->fresh()->stock)->toBe(11);

    $this->actingAs($storeUser)->get(route('returns.show', $return))->assertOk();
    $this->actingAs($storeUser)->delete(route('returns.destroy', $return))->assertRedirect(route('returns.index'));
    expect(ReturnItem::find($return->id))->toBeNull();
    expect($product->fresh()->stock)->toBe(10);
});

test('admin dapat membuat settlement yang mengunci titipannya', function () {
    $admin = userWithRole('admin');
    $umkm = userWithRole('umkm');
    $store = Store::create(['name' => 'Toko Bunga']);
    $product = productFor($umkm);
    $consignment = consignmentFor($product, $store);

    Sale::create([
        'consignment_id' => $consignment->id,
        'quantity' => 2,
        'amount' => 50000,
        'sale_date' => now()->toDateString(),
    ]);

    $this->actingAs($admin)->get(route('reports.index'))->assertOk();
    $this->actingAs($admin)->get(route('settlements.index'))->assertOk();
    $this->actingAs($admin)->get(route('settlements.create'))->assertOk();

    $this->actingAs($admin)
        ->post(route('settlements.store'), [
            'store_id' => $store->id,
            'umkm_id' => $umkm->id,
            'commission_rate' => 10,
            'settlement_date' => now()->toDateString(),
        ])
        ->assertRedirect(route('settlements.index'));

    $settlement = Settlement::firstOrFail();
    expect((float) $settlement->total_sales)->toBe(50000.0);
    expect((float) $settlement->net_amount)->toBe(45000.0);

    $consignment->refresh();
    expect($consignment->status)->toBe('settled');
    expect($consignment->settlement_id)->toBe($settlement->id);

    $this->actingAs($admin)->get(route('settlements.show', $settlement))->assertOk();

    $this->actingAs($admin)
        ->post(route('settlements.store'), [
            'store_id' => $store->id,
            'umkm_id' => $umkm->id,
            'commission_rate' => 10,
            'settlement_date' => now()->toDateString(),
        ])
        ->assertSessionHasErrors('store_id');

    expect(Settlement::count())->toBe(1);
});

test('penjualan tidak bisa ditambahkan ke titipan yang sudah di-settle', function () {
    $admin = userWithRole('admin');
    $storeUser = userWithRole('store');
    $umkm = userWithRole('umkm');
    $store = storeFor($storeUser);
    $product = productFor($umkm);
    $consignment = consignmentFor($product, $store);
    $settlement = Settlement::create([
        'store_id' => $store->id,
        'umkm_id' => $umkm->id,
        'total_sales' => 0,
        'total_returns' => 0,
        'commission_rate' => 10,
        'net_amount' => 0,
        'settlement_date' => now()->toDateString(),
    ]);
    $consignment->update(['settlement_id' => $settlement->id, 'status' => 'settled']);

    $this->actingAs($storeUser)
        ->post(route('sales.store'), [
            'consignment_id' => $consignment->id,
            'quantity' => 1,
            'sale_date' => now()->toDateString(),
        ])
        ->assertSessionHasErrors('consignment_id');

    expect(Sale::count())->toBe(0);
    expect($admin->isAdmin())->toBeTrue();
});

test('menghapus settlement membuka kunci titipannya', function () {
    $admin = userWithRole('admin');
    $umkm = userWithRole('umkm');
    $store = Store::create(['name' => 'Toko Bunga']);
    $consignment = consignmentFor(productFor($umkm), $store);
    $settlement = Settlement::create([
        'store_id' => $store->id,
        'umkm_id' => $umkm->id,
        'total_sales' => 0,
        'total_returns' => 0,
        'commission_rate' => 10,
        'net_amount' => 0,
        'settlement_date' => now()->toDateString(),
    ]);
    $consignment->update(['settlement_id' => $settlement->id, 'status' => 'settled']);

    $this->actingAs($admin)->delete(route('settlements.destroy', $settlement))->assertRedirect(route('settlements.index'));

    $consignment->refresh();
    expect($consignment->settlement_id)->toBeNull();
    expect($consignment->status)->toBe('active');
    expect(Settlement::find($settlement->id))->toBeNull();
});

test('store manager yang mendaftar otomatis memiliki toko', function () {
    $storeRole = Role::create(['name' => 'Store Manager', 'slug' => 'store']);
    Role::create(['name' => 'UMKM Owner', 'slug' => 'umkm']);
    Role::create(['name' => 'Admin', 'slug' => 'admin']);

    $this->get(route('register'))->assertOk()->assertDontSee('Admin');

    $this->post(route('register'), [
        'name' => 'Manajer Baru',
        'email' => 'manajer@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role_id' => $storeRole->id,
        'store_name' => 'Toko Baru',
        'store_address' => 'Jl. Baru No. 2',
    ])->assertRedirect('/dashboard');

    $user = User::where('email', 'manajer@example.com')->firstOrFail();
    expect($user->store_id)->not->toBeNull();

    $store = Store::findOrFail($user->store_id);
    expect($store->name)->toBe('Toko Baru');
    expect($store->manager_id)->toBe($user->id);

    $this->actingAs($user)->get(route('dashboard'))->assertOk();
    $this->actingAs($user)->get(route('sales.create'))->assertOk();
});

test('umkm yang mendaftar tidak membuat toko', function () {
    $umkmRole = Role::create(['name' => 'UMKM Owner', 'slug' => 'umkm']);

    $this->post(route('register'), [
        'name' => 'UMKM Baru',
        'email' => 'umkm.baru@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role_id' => $umkmRole->id,
    ])->assertRedirect('/dashboard');

    expect(User::where('email', 'umkm.baru@example.com')->firstOrFail()->store_id)->toBeNull();
    expect(Store::count())->toBe(0);
});

test('umkm tidak melihat tombol aksi yang bukan haknya', function () {
    $umkm = userWithRole('umkm');

    $this->actingAs($umkm)->get(route('sales.index'))
        ->assertOk()
        ->assertDontSee('Catat Penjualan');

    $this->actingAs($umkm)->get(route('returns.index'))
        ->assertOk()
        ->assertDontSee('Catat Retur');
});

test('store tidak melihat tombol tambah titipan', function () {
    $storeUser = userWithRole('store');

    $this->actingAs($storeUser)->get(route('consignments.index'))
        ->assertOk()
        ->assertDontSee('Tambah Titipan');

    $this->actingAs($storeUser)->get(route('sales.index'))
        ->assertOk()
        ->assertSee('Catat Penjualan');
});

test('ajuan titipan dari umkm menunggu persetujuan toko dan belum bisa dijual', function () {
    $umkm = userWithRole('umkm');
    $storeUser = userWithRole('store');
    $store = storeFor($storeUser);
    $product = productFor($umkm, ['stock' => 10]);

    $this->actingAs($umkm)
        ->post(route('consignments.store'), [
            'product_id' => $product->id,
            'store_id' => $store->id,
            'quantity' => 4,
            'consignment_date' => now()->toDateString(),
        ])
        ->assertRedirect(route('consignments.index'));

    $consignment = Consignment::firstOrFail();
    expect($consignment->status)->toBe('pending');
    expect($product->fresh()->stock)->toBe(6);

    $this->actingAs($storeUser)->get(route('consignments.index'))
        ->assertOk()
        ->assertSee('Menunggu persetujuan')
        ->assertSee('Terima');

    $this->actingAs($storeUser)->get(route('sales.create'))
        ->assertOk()
        ->assertDontSee($product->name);

    $this->actingAs($storeUser)
        ->post(route('consignments.approve', $consignment))
        ->assertRedirect();

    expect($consignment->fresh()->status)->toBe('active');
});

test('toko dapat menolak ajuan titipan dan stok dikembalikan ke umkm', function () {
    $umkm = userWithRole('umkm');
    $storeUser = userWithRole('store');
    $otherStoreUser = userWithRole('store');
    $store = storeFor($storeUser);
    storeFor($otherStoreUser, ['name' => 'Toko Lain']);
    $product = productFor($umkm, ['stock' => 10]);
    $consignment = consignmentFor($product, $store, ['quantity' => 4, 'status' => 'pending']);
    $product->decrement('stock', 4);

    $this->actingAs($otherStoreUser)
        ->post(route('consignments.reject', $consignment))
        ->assertForbidden();

    $this->actingAs($storeUser)
        ->post(route('consignments.reject', $consignment))
        ->assertRedirect();

    expect($consignment->fresh()->status)->toBe('rejected');
    expect($product->fresh()->stock)->toBe(10);

    $this->actingAs($umkm)->delete(route('consignments.destroy', $consignment))
        ->assertRedirect(route('consignments.index'));

    expect($product->fresh()->stock)->toBe(10);
});

test('store dan admin dapat mengubah status ajuan titipan', function () {
    $umkm = userWithRole('umkm');
    $storeUser = userWithRole('store');
    $otherStoreUser = userWithRole('store');
    $admin = userWithRole('admin');
    $store = storeFor($storeUser);
    storeFor($otherStoreUser, ['name' => 'Toko Lain']);
    $product = productFor($umkm, ['stock' => 10]);
    $consignment = consignmentFor($product, $store, ['quantity' => 4, 'status' => 'pending']);
    $product->decrement('stock', 4);

    $this->actingAs($storeUser)->get(route('consignments.index'))
        ->assertOk()
        ->assertSee('name="status"', false);

    $this->actingAs($otherStoreUser)
        ->patch(route('consignments.status', $consignment), ['status' => 'active'])
        ->assertForbidden();

    $this->actingAs($storeUser)
        ->patch(route('consignments.status', $consignment), ['status' => 'active'])
        ->assertRedirect();

    expect($consignment->fresh()->status)->toBe('active');

    $this->actingAs($admin)
        ->patch(route('consignments.status', $consignment), ['status' => 'rejected'])
        ->assertRedirect();

    expect($consignment->fresh()->status)->toBe('rejected');
    expect($product->fresh()->stock)->toBe(10);
});

test('status ajuan titipan bisa diubah manual lewat form edit', function () {
    $umkm = userWithRole('umkm');
    $store = Store::create(['name' => 'Toko Bunga']);
    $product = productFor($umkm, ['stock' => 10]);
    $consignment = consignmentFor($product, $store, ['quantity' => 4, 'status' => 'pending']);
    $product->decrement('stock', 4);

    $this->actingAs($umkm)->get(route('consignments.edit', $consignment))
        ->assertOk()
        ->assertSee('Menunggu persetujuan');

    $this->actingAs($umkm)->put(route('consignments.update', $consignment), [
        'quantity' => 4,
        'consignment_date' => now()->toDateString(),
        'status' => 'rejected',
    ])->assertRedirect(route('consignments.index'));

    expect($consignment->fresh()->status)->toBe('rejected');
    expect($product->fresh()->stock)->toBe(10);

    $this->actingAs($umkm)->put(route('consignments.update', $consignment), [
        'quantity' => 4,
        'consignment_date' => now()->toDateString(),
        'status' => 'active',
    ])->assertRedirect(route('consignments.index'));

    expect($consignment->fresh()->status)->toBe('active');
    expect($product->fresh()->stock)->toBe(6);
});

test('titipan yang sudah terjual tidak bisa diubah statusnya menjadi ditolak', function () {
    $umkm = userWithRole('umkm');
    $store = Store::create(['name' => 'Toko Bunga']);
    $product = productFor($umkm);
    $consignment = consignmentFor($product, $store);

    Sale::create([
        'consignment_id' => $consignment->id,
        'quantity' => 1,
        'amount' => 25000,
        'sale_date' => now()->toDateString(),
    ]);

    $this->actingAs($umkm)->put(route('consignments.update', $consignment), [
        'quantity' => 5,
        'consignment_date' => now()->toDateString(),
        'status' => 'rejected',
    ])->assertSessionHasErrors('status');

    expect($consignment->fresh()->status)->toBe('active');
    expect($product->fresh()->stock)->toBe(10);
});

test('jumlah titipan tidak bisa diubah kurang dari yang sudah terjual', function () {
    $umkm = userWithRole('umkm');
    $store = Store::create(['name' => 'Toko Bunga']);
    $product = productFor($umkm);
    $consignment = consignmentFor($product, $store, ['quantity' => 5]);

    Sale::create([
        'consignment_id' => $consignment->id,
        'quantity' => 3,
        'amount' => 75000,
        'sale_date' => now()->toDateString(),
    ]);

    $response = $this->actingAs($umkm)->put(route('consignments.update', $consignment), [
        'quantity' => 2,
        'consignment_date' => now()->toDateString(),
        'status' => 'active',
    ]);

    $response->assertSessionHasErrors('quantity');
    expect($consignment->fresh()->quantity)->toBe(5);
});

test('dashboard umkm hanya menghitung data miliknya', function () {
    $umkm = userWithRole('umkm');
    $otherUmkm = userWithRole('umkm');
    $store = Store::create(['name' => 'Toko Bunga']);

    $ownConsignment = consignmentFor(productFor($umkm, ['name' => 'Mawar Sendiri']), $store);
    $otherConsignment = consignmentFor(productFor($otherUmkm, ['name' => 'Tulip Orang Lain', 'price' => 999000]), $store);

    Sale::create([
        'consignment_id' => $ownConsignment->id,
        'quantity' => 1,
        'amount' => 25000,
        'sale_date' => now()->toDateString(),
    ]);
    Sale::create([
        'consignment_id' => $otherConsignment->id,
        'quantity' => 1,
        'amount' => 999000,
        'sale_date' => now()->toDateString(),
    ]);

    $this->actingAs($umkm)->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Rp 25.000')
        ->assertDontSee('Rp 999.000')
        ->assertSee('Mawar Sendiri')
        ->assertDontSee('Tulip Orang Lain');
});

test('dashboard tampil untuk semua role', function (string $slug) {
    $user = userWithRole($slug);
    $this->actingAs($user)->get(route('dashboard'))->assertOk();
})->with(['umkm', 'store', 'admin']);
