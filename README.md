# TitipOS

Sistem manajemen titipan untuk UMKM dan toko mitra: produk, titipan, penjualan, retur, settlement, laporan, dan export Excel.

Dibangun dengan Laravel 13, Tailwind CSS 4, dan Vite.

## Kebutuhan

- PHP 8.3 atau lebih baru dengan ekstensi `zip` dan `pdo_sqlite`
- Composer
- Node.js 20+ (opsional, hanya jika ingin mengubah aset frontend)

## Menjalankan setelah clone

```bash
git clone https://github.com/rakhasukendra/TitipOS.git
cd TitipOS

composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
php artisan serve
```

Buka http://localhost:8000.

Atau jalankan seluruh langkah di atas (termasuk install dan build aset) dengan satu perintah:

```bash
composer setup
```

> Aset frontend hasil build sudah disertakan di `public/build`, jadi aplikasi tetap tampil normal tanpa menjalankan `npm run build`. Jalankan `npm install && npm run build` hanya setelah mengubah file CSS/JS.

Konfigurasi default memakai SQLite dan `database/database.sqlite` dibuat oleh langkah di atas. File `.env` tidak disertakan di repositori.

## Akun demo

Dibuat oleh `php artisan migrate --seed`:

| Peran | Email | Password |
| --- | --- | --- |
| UMKM Owner | `umkm@example.com` | `password` |
| Store Manager | `store@example.com` | `password` |
| Admin | `admin@example.com` | `password` |

## Perintah berguna

```bash
php artisan test        # menjalankan test
npm run dev             # Vite dev server
npm run build           # build aset produksi
```

## Lisensi

MIT.
