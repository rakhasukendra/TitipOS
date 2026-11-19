<?php

use App\Models\Consignment;
use App\Models\Product;
use App\Models\Role;
use App\Models\Sale;
use App\Models\Store;
use App\Models\User;
use ZipArchive;

function reportUser(string $slug): User
{
    $role = Role::firstOrCreate(['slug' => $slug], ['name' => ucfirst($slug)]);

    return User::factory()->create(['role_id' => $role->id]);
}

/**
 * Mengambil isi worksheet dari file XLSX hasil export.
 */
function reportSheet(string $contents): string
{
    $path = tempnam(sys_get_temp_dir(), 'xlsx-test');
    file_put_contents($path, $contents);

    $zip = new ZipArchive;
    $zip->open($path);
    $sheet = $zip->getFromName('xl/worksheets/sheet1.xml');
    $zip->close();
    @unlink($path);

    return $sheet;
}

/**
 * Membuat toko beserta satu titipan yang sudah terjual.
 */
function storeWithSale(User $manager, User $umkm, string $storeName, string $productName, int $amount): Store
{
    $store = Store::create(['name' => $storeName, 'manager_id' => $manager->id]);
    $manager->update(['store_id' => $store->id]);

    $product = Product::create([
        'name' => $productName,
        'description' => $productName,
        'price' => $amount,
        'stock' => 10,
        'umkm_id' => $umkm->id,
    ]);

    $consignment = Consignment::create([
        'product_id' => $product->id,
        'store_id' => $store->id,
        'quantity' => 5,
        'consignment_date' => now()->toDateString(),
        'status' => 'active',
    ]);

    Sale::create([
        'consignment_id' => $consignment->id,
        'quantity' => 1,
        'amount' => $amount,
        'sale_date' => now()->toDateString(),
    ]);

    return $store;
}

test('admin melihat dan mengekspor laporan seluruh toko', function () {
    $admin = reportUser('admin');
    $umkm = reportUser('umkm');
    $firstManager = reportUser('store');
    $secondManager = reportUser('store');

    storeWithSale($firstManager, $umkm, 'Toko Mawar', 'Mawar', 25000);
    storeWithSale($secondManager, $umkm, 'Toko Tulip', 'Tulip', 40000);

    $this->actingAs($admin)->get(route('reports.index'))
        ->assertOk()
        ->assertSee('Toko Mawar')
        ->assertSee('Toko Tulip')
        ->assertSee('Export Excel');

    $response = $this->actingAs($admin)->get(route('reports.export'));

    $response->assertOk();
    expect($response->headers->get('content-disposition'))->toContain('laporan-admin-')
        ->toContain('.xlsx');

    $content = $response->streamedContent();
    expect($content)->toStartWith('PK');

    $sheet = reportSheet($content);
    expect($sheet)->toContain('Toko Mawar')
        ->toContain('Toko Tulip')
        ->toContain('Admin')
        // Lebar kolom diatur agar header dan tanggal tidak terpotong.
        ->toContain('width="30"')
        ->toContain(now()->format('d/m/Y'));
});

test('staff toko hanya melihat dan mengekspor laporan tokonya sendiri', function () {
    $umkm = reportUser('umkm');
    $staff = reportUser('store');
    $otherManager = reportUser('store');

    storeWithSale($staff, $umkm, 'Toko Sendiri', 'Mawar', 25000);
    storeWithSale($otherManager, $umkm, 'Toko Lain', 'Tulip', 40000);

    $this->actingAs($staff)->get(route('reports.index'))
        ->assertOk()
        ->assertSee('Toko Sendiri')
        ->assertDontSee('Toko Lain')
        ->assertSee('Export Excel');

    $response = $this->actingAs($staff)->get(route('reports.export'));

    $response->assertOk();
    expect($response->headers->get('content-disposition'))->toContain('laporan-store-')
        ->toContain('.xlsx');

    expect(reportSheet($response->streamedContent()))->toContain('Toko Sendiri')
        ->not->toContain('Toko Lain');
});

test('umkm hanya melihat dan mengekspor laporan produknya sendiri', function () {
    $umkm = reportUser('umkm');
    $otherUmkm = reportUser('umkm');
    $firstManager = reportUser('store');
    $secondManager = reportUser('store');

    storeWithSale($firstManager, $umkm, 'Toko Produk Saya', 'Mawar', 25000);
    storeWithSale($secondManager, $otherUmkm, 'Toko Produk Lain', 'Tulip', 40000);

    $this->actingAs($umkm)->get(route('reports.index'))
        ->assertOk()
        ->assertSee('Toko Produk Saya')
        ->assertDontSee('Toko Produk Lain')
        ->assertSee('Export Excel');

    $response = $this->actingAs($umkm)->get(route('reports.export'));

    $response->assertOk();
    expect($response->headers->get('content-disposition'))->toContain('laporan-umkm-')
        ->toContain('.xlsx');

    expect(reportSheet($response->streamedContent()))->toContain('Toko Produk Saya')
        ->not->toContain('Toko Produk Lain');
});

test('tamu diarahkan ke login saat membuka laporan', function () {
    $this->get(route('reports.index'))->assertRedirect(route('login'));
    $this->get(route('reports.export'))->assertRedirect(route('login'));
});
