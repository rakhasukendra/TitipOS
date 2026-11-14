@extends('layouts.app')

@php
    $dashboardUser = auth()->user();
    $dashboardSubheader = match (true) {
        $dashboardUser->isStoreManager() => 'Ringkasan titipan masuk dan penjualan toko Anda',
        $dashboardUser->isAdmin() => 'Ringkasan seluruh titipan, penjualan, dan settlement',
        default => 'Ringkasan performa titipan dan penjualan Anda',
    };
@endphp

@section('header', 'Dashboard')
@section('subheader', $dashboardSubheader)

@section('content')
@php
    $formatRupiah = fn (float $value): string => 'Rp ' . number_format($value, 0, ',', '.');

    $formatCompact = function (float $value): string {
        if ($value >= 1_000_000) {
            $formatted = rtrim(rtrim(number_format($value / 1_000_000, 1, ',', '.'), '0'), ',');

            return $formatted . 'jt';
        }

        if ($value >= 1_000) {
            $formatted = rtrim(rtrim(number_format($value / 1_000, 1, ',', '.'), '0'), ',');

            return $formatted . 'rb';
        }

        return number_format($value, 0, ',', '.');
    };

    $monthlySales = (int) $stats['monthly_sales'];
    $previousMonthSales = (int) $stats['previous_month_sales'];
    $salesChange = $previousMonthSales > 0
        ? (($monthlySales - $previousMonthSales) / $previousMonthSales) * 100
        : null;

    $trendValues = $revenueTrend->values();
    $hasRevenue = $trendValues->sum('total') > 0;
    $trendTotal = (float) $trendValues->sum('total');

    $chartWidth = 720;
    $chartHeight = 280;
    $chartTop = 16;
    $chartRight = 8;
    $chartBottom = 32;
    $chartLeft = 56;
    $plotWidth = $chartWidth - $chartLeft - $chartRight;
    $plotHeight = $chartHeight - $chartTop - $chartBottom;
    $baseline = $chartTop + $plotHeight;
    $axisMax = max((float) $trendValues->max('total'), 1);
    $pointCount = $trendValues->count();

    $points = $trendValues->map(function (array $row, int $index) use ($axisMax, $chartLeft, $chartTop, $plotHeight, $plotWidth, $pointCount) {
        $x = $chartLeft + ($pointCount > 1 ? $index * ($plotWidth / ($pointCount - 1)) : $plotWidth / 2);
        $y = $chartTop + $plotHeight - ((float) $row['total'] / $axisMax) * $plotHeight;

        return [
            'x' => round($x, 1),
            'y' => round($y, 1),
            'label' => $row['label'],
            'total' => (float) $row['total'],
        ];
    });

    $linePath = $points
        ->map(fn (array $point, int $index) => ($index === 0 ? 'M' : 'L') . $point['x'] . ' ' . $point['y'])
        ->join(' ');
    $areaPath = $linePath
        . ' L ' . $points->last()['x'] . ' ' . $baseline
        . ' L ' . $points->first()['x'] . ' ' . $baseline . ' Z';

    $gridLines = collect([1, 0.75, 0.5, 0.25, 0])
        ->map(fn (float $ratio) => [
            'y' => round($chartTop + $plotHeight - $ratio * $plotHeight, 1),
            'label' => in_array($ratio, [1.0, 0.5, 0.0], true) ? $formatCompact($axisMax * $ratio) : null,
            'isBaseline' => $ratio === 0.0,
        ]);

    $chartSummary = 'Grafik pendapatan enam bulan terakhir: '
        . $points->map(fn (array $point) => $point['label'] . ' ' . $formatRupiah($point['total']))->join(', ');

    $statusLabels = [
        'pending' => 'Menunggu',
        'active' => 'Aktif',
        'rejected' => 'Ditolak',
        'settled' => 'Settled',
        'returned' => 'Retur',
    ];
    $statusColors = [
        'pending' => '#0284c7',
        'active' => '#0e7c5b',
        'rejected' => '#e11d48',
        'settled' => '#3c4147',
        'returned' => '#d97706',
    ];
    $statusTotal = (int) $statusBreakdown->sum('total');
    $circumference = 2 * M_PI * 42;
    $runningOffset = 0;

    $donutSegments = $statusBreakdown->map(function (array $row) use ($statusLabels, $statusColors, $statusTotal, $circumference, &$runningOffset) {
        $share = $statusTotal > 0 ? $row['total'] / $statusTotal : 0;
        $length = $share * $circumference;
        $segment = [
            'label' => $statusLabels[$row['status']] ?? $row['status'],
            'color' => $statusColors[$row['status']] ?? '#9ca1a6',
            'total' => (int) $row['total'],
            'percent' => $share * 100,
            'length' => $length,
            'offset' => $runningOffset,
        ];

        $runningOffset += $length;

        return $segment;
    });

    $topRevenueMax = max((float) $topProducts->max('revenue'), 1);
@endphp

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
    <div class="surface p-5">
        <div class="flex items-start justify-between">
            <p class="section-label">Total Produk</p>
            <span class="flex size-8 items-center justify-center rounded-lg bg-canvas text-ink-500">
                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </span>
        </div>
        <p class="mt-4 text-2xl font-semibold tabular-nums tracking-tight text-ink-900">{{ $stats['total_products'] }}</p>
        <p class="mt-1 text-xs text-ink-500">Produk terdaftar</p>
    </div>

    <div class="surface p-5">
        <div class="flex items-start justify-between">
            <p class="section-label">Titipan Aktif</p>
            <span class="flex size-8 items-center justify-center rounded-lg bg-canvas text-ink-500">
                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </span>
        </div>
        <p class="mt-4 text-2xl font-semibold tabular-nums tracking-tight text-ink-900">{{ $stats['active_consignments'] }}</p>
        <p class="mt-1 text-xs text-ink-500">Sedang berjalan</p>
    </div>

    <div class="surface p-5">
        <div class="flex items-start justify-between">
            <p class="section-label">Penjualan Bulan Ini</p>
            <span class="flex size-8 items-center justify-center rounded-lg bg-canvas text-ink-500">
                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
            </span>
        </div>
        <p class="mt-4 text-2xl font-semibold tabular-nums tracking-tight text-ink-900">{{ $monthlySales }}</p>
        @if($salesChange === null)
            <p class="mt-1 text-xs text-ink-500">{{ $monthlySales > 0 ? 'Belum ada pembanding bulan lalu' : 'Belum ada transaksi' }}</p>
        @else
            <p class="mt-1 flex items-center gap-1 text-xs {{ $salesChange >= 0 ? 'text-brand-600' : 'text-rose-600' }}">
                <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    @if($salesChange >= 0)
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 17L17 7M17 7H9m8 0v8"/>
                    @else
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7l10 10M17 17H9m8 0V9"/>
                    @endif
                </svg>
                <span class="font-medium tabular-nums">{{ number_format(abs($salesChange), 0, ',', '.') }}%</span>
                vs bulan lalu
            </p>
        @endif
    </div>

    <div class="relative overflow-hidden rounded-2xl bg-ink-900 p-5 text-white">
        <div class="pointer-events-none absolute -top-16 -right-12 size-44 rounded-full bg-brand-600/30 blur-2xl"></div>
        <div class="relative">
            <div class="flex items-start justify-between">
                <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-white/50">Total Pendapatan</p>
                <span class="flex size-8 items-center justify-center rounded-lg bg-white/10 text-white/80">
                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </span>
            </div>
            <p class="mt-4 text-2xl font-semibold tabular-nums tracking-tight">{{ $formatRupiah((float) $stats['total_revenue']) }}</p>
            <p class="mt-1 text-xs text-white/50">Pendapatan kotor</p>
        </div>
    </div>
</div>

<div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-3">
    <div class="surface flex flex-col p-5 lg:col-span-2">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <h2 class="text-sm font-semibold text-ink-900">Tren Pendapatan</h2>
                <p class="mt-0.5 text-xs text-ink-500">Enam bulan terakhir</p>
            </div>
            @if($hasRevenue)
                <div class="text-right">
                    <p class="text-sm font-semibold tabular-nums text-ink-900">{{ $formatRupiah($trendTotal) }}</p>
                    <p class="text-[11px] text-ink-400">Total 6 bulan</p>
                </div>
            @endif
        </div>

        @if($hasRevenue)
            <div class="mt-5 flex-1">
                <svg viewBox="0 0 {{ $chartWidth }} {{ $chartHeight }}" class="h-auto w-full" role="img" aria-label="{{ $chartSummary }}">
                    <defs>
                        <linearGradient id="revenueArea" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#0e7c5b" stop-opacity="0.18"/>
                            <stop offset="100%" stop-color="#0e7c5b" stop-opacity="0"/>
                        </linearGradient>
                    </defs>

                    @foreach($gridLines as $line)
                        <line
                            x1="{{ $chartLeft }}"
                            y1="{{ $line['y'] }}"
                            x2="{{ $chartWidth - $chartRight }}"
                            y2="{{ $line['y'] }}"
                            stroke="{{ $line['isBaseline'] ? '#e7e4de' : '#efede8' }}"
                            stroke-width="1"
                            @if(! $line['isBaseline']) stroke-dasharray="4 6" @endif
                        />
                        @if($line['label'] !== null)
                            <text x="{{ $chartLeft - 12 }}" y="{{ $line['y'] + 4 }}" text-anchor="end" class="chart-axis-text">{{ $line['label'] }}</text>
                        @endif
                    @endforeach

                    <path d="{{ $areaPath }}" fill="url(#revenueArea)"/>
                    <path d="{{ $linePath }}" fill="none" stroke="#0e7c5b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>

                    @foreach($points as $point)
                        @if($point['total'] > 0)
                            <circle cx="{{ $point['x'] }}" cy="{{ $point['y'] }}" r="4" fill="#ffffff" stroke="#0e7c5b" stroke-width="2">
                                <title>{{ $point['label'] }}: {{ $formatRupiah($point['total']) }}</title>
                            </circle>
                        @endif
                    @endforeach

                    @foreach($points as $point)
                        <text x="{{ $point['x'] }}" y="{{ $chartHeight - 10 }}" text-anchor="middle" class="chart-axis-text">{{ $point['label'] }}</text>
                    @endforeach
                </svg>
            </div>
        @else
            <div class="flex flex-1 flex-col items-center justify-center px-4 py-12 text-center">
                <span class="flex size-12 items-center justify-center rounded-full border border-dashed border-hairline text-ink-400">
                    <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 17l5-6 4 4 6-8"/>
                    </svg>
                </span>
                <p class="mt-3 text-sm font-medium text-ink-900">Belum ada pendapatan tercatat</p>
                <p class="mt-1 max-w-xs text-xs text-ink-500">Grafik muncul setelah penjualan pertama dicatat dalam enam bulan terakhir.</p>
                @if(in_array(auth()->user()->role->slug ?? '', ['store', 'admin']))
                    <a href="{{ route('sales.create') }}" class="btn btn-secondary btn-sm mt-4">Catat penjualan</a>
                @endif
            </div>
        @endif
    </div>

    <div class="grid gap-4">
        <div class="surface p-5">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h2 class="text-sm font-semibold text-ink-900">Status Titipan</h2>
                    <p class="mt-0.5 text-xs text-ink-500">
                        {{ $dashboardUser->isStoreManager()
                            ? 'Titipan yang masuk ke toko Anda'
                            : ($dashboardUser->isAdmin() ? 'Seluruh titipan di semua toko' : 'Titipan dari produk Anda') }}
                    </p>
                </div>
            </div>

            <div class="mt-4 flex items-center gap-5">
                <div class="relative shrink-0">
                    <svg viewBox="0 0 120 120" class="size-[120px]" role="img" aria-label="Distribusi status titipan: {{ $donutSegments->map(fn ($segment) => $segment['label'] . ' ' . $segment['total'])->join(', ') }}">
                        <circle cx="60" cy="60" r="42" fill="none" stroke="#f1efea" stroke-width="14"/>
                        @foreach($donutSegments as $segment)
                            @if($segment['length'] > 0)
                                <circle
                                    cx="60"
                                    cy="60"
                                    r="42"
                                    fill="none"
                                    stroke="{{ $segment['color'] }}"
                                    stroke-width="14"
                                    stroke-dasharray="{{ round($segment['length'], 2) }} {{ round($circumference - $segment['length'], 2) }}"
                                    stroke-dashoffset="{{ -round($segment['offset'], 2) }}"
                                    transform="rotate(-90 60 60)"
                                >
                                    <title>{{ $segment['label'] }}: {{ $segment['total'] }} ({{ number_format($segment['percent'], 0, ',', '.') }}%)</title>
                                </circle>
                            @endif
                        @endforeach
                    </svg>
                    <div class="pointer-events-none absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-xl font-semibold tabular-nums tracking-tight text-ink-900">{{ $statusTotal }}</span>
                        <span class="text-[10px] uppercase tracking-[0.12em] text-ink-400">titipan</span>
                    </div>
                </div>

                <ul class="min-w-0 flex-1 space-y-2.5">
                    @foreach($donutSegments as $segment)
                        <li class="flex items-center justify-between gap-3">
                            <span class="flex min-w-0 items-center gap-2 text-xs text-ink-500">
                                <span class="size-2 shrink-0 rounded-full" style="background-color: {{ $segment['color'] }}"></span>
                                <span class="truncate">{{ $segment['label'] }}</span>
                            </span>
                            <span class="shrink-0 text-xs font-semibold tabular-nums text-ink-900">{{ $segment['total'] }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="surface p-5">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h2 class="text-sm font-semibold text-ink-900">Produk Teratas</h2>
                    <p class="mt-0.5 text-xs text-ink-500">Berdasarkan pendapatan</p>
                </div>
                <a href="{{ route('products.index') }}" class="link-muted shrink-0">Semua →</a>
            </div>

            @if($topProducts->isNotEmpty())
                <ul class="mt-4 space-y-3.5">
                    @foreach($topProducts as $product)
                        <li>
                            <div class="flex items-baseline justify-between gap-3">
                                <p class="truncate text-sm font-medium text-ink-900">{{ $product->name }}</p>
                                <p class="shrink-0 text-xs font-semibold tabular-nums text-ink-900">{{ $formatRupiah((float) $product->revenue) }}</p>
                            </div>
                            <div class="mt-1.5 h-1.5 overflow-hidden rounded-full bg-canvas">
                                <div class="h-full rounded-full bg-brand-600" style="width: {{ max(4, round(((float) $product->revenue / $topRevenueMax) * 100)) }}%"></div>
                            </div>
                            <p class="mt-1 text-[11px] text-ink-400">{{ number_format((float) $product->sold, 0, ',', '.') }} pcs terjual</p>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="mt-4 text-xs text-ink-500">Belum ada penjualan. Produk teratas muncul setelah transaksi pertama tercatat.</p>
            @endif
        </div>
    </div>
</div>

<div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-2">
    <div class="surface overflow-hidden">
        <div class="flex items-center justify-between border-b border-hairline px-5 py-4">
            <h2 class="text-sm font-semibold text-ink-900">
                {{ $dashboardUser->isStoreManager() ? 'Ajuan & Titipan Terbaru' : 'Titipan Terbaru' }}
            </h2>
            <a href="{{ route('consignments.index') }}" class="link-muted">{{ $dashboardUser->isStoreManager() ? 'Tinjau semua →' : 'Lihat semua →' }}</a>
        </div>

        @if($recentConsignments->count() > 0)
            <div class="divide-y divide-hairline">
                @foreach($recentConsignments as $consignment)
                <div class="flex items-center justify-between gap-4 px-5 py-3.5 transition hover:bg-canvas/70">
                    <div class="flex min-w-0 items-center gap-3">
                        <span class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-canvas text-ink-400">
                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </span>
                        <div class="min-w-0">
                            <p class="truncate text-sm font-medium text-ink-900">{{ $consignment->product->name }}</p>
                            <p class="truncate text-xs text-ink-500">{{ $consignment->store->name }}</p>
                        </div>
                    </div>
                    <div class="shrink-0 text-right">
                        <p class="text-sm font-semibold tabular-nums text-ink-900">{{ $consignment->quantity }} <span class="text-xs font-normal text-ink-500">pcs</span></p>
                        <p class="text-xs text-ink-400">{{ $consignment->consignment_date->format('d M Y') }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="px-5 py-14 text-center">
                <div class="mx-auto flex size-12 items-center justify-center rounded-full border border-dashed border-hairline text-ink-400">
                    <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                </div>
                @if($dashboardUser->isStoreManager())
                    <p class="mt-3 text-sm font-medium text-ink-900">Belum ada titipan masuk</p>
                    <p class="mt-1 text-xs text-ink-500">Ajuan barang dari UMKM akan muncul di sini untuk Anda terima atau tolak.</p>
                @else
                    <p class="mt-3 text-sm font-medium text-ink-900">Belum ada titipan</p>
                    <a href="{{ route('consignments.create') }}" class="mt-2 inline-block link-muted">Buat titipan baru →</a>
                @endif
            </div>
        @endif
    </div>

    <div class="surface overflow-hidden">
        <div class="flex items-center justify-between border-b border-hairline px-5 py-4">
            <h2 class="text-sm font-semibold text-ink-900">Penjualan Terbaru</h2>
            <a href="{{ route('sales.index') }}" class="link-muted">Lihat semua →</a>
        </div>

        @if($recentSales->count() > 0)
            <div class="divide-y divide-hairline">
                @foreach($recentSales as $sale)
                <div class="flex items-center justify-between gap-4 px-5 py-3.5 transition hover:bg-canvas/70">
                    <div class="flex min-w-0 items-center gap-3">
                        <span class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-canvas text-ink-400">
                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                        </span>
                        <div class="min-w-0">
                            <p class="truncate text-sm font-medium text-ink-900">{{ $sale->consignment->product->name }}</p>
                            <p class="truncate text-xs text-ink-500">{{ $sale->consignment->store->name }}</p>
                        </div>
                    </div>
                    <div class="shrink-0 text-right">
                        <p class="text-sm font-semibold tabular-nums text-brand-600">Rp {{ number_format($sale->amount, 0, ',', '.') }}</p>
                        <p class="text-xs text-ink-400">{{ $sale->sale_date->format('d M Y') }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="px-5 py-14 text-center">
                <div class="mx-auto flex size-12 items-center justify-center rounded-full border border-dashed border-hairline text-ink-400">
                    <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="mt-3 text-sm font-medium text-ink-900">Belum ada penjualan</p>
                @if(in_array(auth()->user()->role->slug ?? '', ['store', 'admin']))
                    <a href="{{ route('sales.create') }}" class="mt-2 inline-block link-muted">Catat penjualan →</a>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
