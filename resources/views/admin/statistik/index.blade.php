@extends('layouts.app')

@section('title', 'Statistik Peserta')

@section('content')
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">Statistik Peserta</h2>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            @if($tahunAktif)
                Data peserta tahun penerimaan <span class="font-medium text-gray-700 dark:text-gray-300">{{ $tahunAktif->label ?? $tahunAktif->tahun }}</span>
            @else
                Belum ada tahun penerimaan aktif
            @endif
        </p>
    </div>

    {{-- Kartu ringkasan total --}}
    <div class="mb-6">
        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 p-5 shadow-theme-sm inline-flex items-center gap-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-50 dark:bg-brand-500/10 text-brand-500">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M17 21V19C17 17.9391 16.5786 16.9217 15.8284 16.1716C15.0783 15.4214 14.0609 15 13 15H5C3.93913 15 2.92172 15.4214 2.17157 16.1716C1.42143 16.9217 1 17.9391 1 19V21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M9 11C11.2091 11 13 9.20914 13 7C13 4.79086 11.2091 3 9 3C6.79086 3 5 4.79086 5 7C5 9.20914 6.79086 11 9 11Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M23 21V19C22.9993 18.1137 22.7044 17.2528 22.1614 16.5523C21.6184 15.8519 20.8581 15.3516 20 15.13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M16 3.13C16.8604 3.35031 17.623 3.85071 18.1676 4.55232C18.7122 5.25392 19.0078 6.11683 19.0078 7.005C19.0078 7.89317 18.7122 8.75608 18.1676 9.45768C17.623 10.1593 16.8604 10.6597 16 10.88" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Peserta Terdaftar</p>
                <p class="mt-0.5 text-2xl font-bold text-gray-800 dark:text-white/90">{{ number_format($totalPeserta) }}</p>
            </div>
        </div>
    </div>

    {{-- Baris pertama: Per Jalur + Per Jurusan --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2 mb-6">

        {{-- Chart: Per Jalur Pendaftaran --}}
        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 p-5 shadow-theme-sm">
            <h3 class="mb-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Distribusi per Jalur Pendaftaran</h3>
            @if($perJalur->isEmpty())
                <p class="text-sm text-gray-400 text-center py-10">Belum ada data</p>
            @else
                <div class="relative h-64">
                    <canvas id="chartJalur"></canvas>
                </div>
                <div class="mt-4 space-y-1">
                    @foreach($perJalur as $item)
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">{{ $item->label }}</span>
                        <span class="font-semibold text-gray-800 dark:text-white/90">{{ $item->total }}</span>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Chart: Per Jurusan --}}
        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 p-5 shadow-theme-sm">
            <h3 class="mb-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Distribusi per Jurusan</h3>
            @if($perJurusan->isEmpty())
                <p class="text-sm text-gray-400 text-center py-10">Belum ada data</p>
            @else
                <div class="relative h-64">
                    <canvas id="chartJurusan"></canvas>
                </div>
                <div class="mt-4 space-y-1">
                    @foreach($perJurusan as $item)
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">{{ $item->label }}</span>
                        <span class="font-semibold text-gray-800 dark:text-white/90">{{ $item->total }}</span>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

    {{-- Baris kedua: Status Verifikasi + Status Hasil --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

        {{-- Chart: Status Verifikasi --}}
        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 p-5 shadow-theme-sm">
            <h3 class="mb-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Status Verifikasi</h3>
            @if($perStatusVerifikasi->isEmpty())
                <p class="text-sm text-gray-400 text-center py-10">Belum ada data</p>
            @else
                <div class="relative h-64">
                    <canvas id="chartVerifikasi"></canvas>
                </div>
                <div class="mt-4 space-y-1">
                    @foreach($perStatusVerifikasi as $item)
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">{{ $item['label'] }}</span>
                        <span class="font-semibold text-gray-800 dark:text-white/90">{{ $item['total'] }}</span>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Chart: Status Hasil Seleksi --}}
        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 p-5 shadow-theme-sm">
            <h3 class="mb-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Status Hasil Seleksi</h3>
            @if($perStatusHasil->isEmpty())
                <p class="text-sm text-gray-400 text-center py-10">Belum ada data</p>
            @else
                <div class="relative h-64">
                    <canvas id="chartHasil"></canvas>
                </div>
                <div class="mt-4 space-y-1">
                    @foreach($perStatusHasil as $item)
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">{{ $item['label'] }}</span>
                        <span class="font-semibold text-gray-800 dark:text-white/90">{{ $item['total'] }}</span>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function () {
    const isDark = document.documentElement.classList.contains('dark');

    const COLORS = [
        '#465fff', '#12b76a', '#f79009', '#f04438',
        '#0ba5ec', '#fb6514', '#7c3aed', '#0e9384',
    ];

    const gridColor = isDark ? 'rgba(255,255,255,0.08)' : 'rgba(0,0,0,0.06)';
    const tickColor = isDark ? '#9ca3af' : '#6b7280';

    function barChart(id, labels, data) {
        const ctx = document.getElementById(id);
        if (!ctx) return;
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: COLORS.slice(0, labels.length),
                    borderRadius: 6,
                    borderSkipped: false,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => ` ${ctx.parsed.y} peserta`,
                        },
                    },
                },
                scales: {
                    x: {
                        grid: { color: gridColor },
                        ticks: { color: tickColor, font: { size: 12 } },
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: gridColor },
                        ticks: {
                            color: tickColor,
                            font: { size: 12 },
                            stepSize: 1,
                            precision: 0,
                        },
                    },
                },
            },
        });
    }

    function doughnutChart(id, labels, data) {
        const ctx = document.getElementById(id);
        if (!ctx) return;
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: COLORS.slice(0, labels.length),
                    borderWidth: 2,
                    borderColor: isDark ? '#1d2939' : '#ffffff',
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: tickColor,
                            font: { size: 12 },
                            padding: 12,
                            usePointStyle: true,
                            pointStyleWidth: 8,
                        },
                    },
                    tooltip: {
                        callbacks: {
                            label: ctx => ` ${ctx.label}: ${ctx.parsed} peserta`,
                        },
                    },
                },
                cutout: '65%',
            },
        });
    }

    @if($perJalur->isNotEmpty())
    barChart(
        'chartJalur',
        @json($perJalur->pluck('label')),
        @json($perJalur->pluck('total')->map(fn($v) => (int)$v))
    );
    @endif

    @if($perJurusan->isNotEmpty())
    barChart(
        'chartJurusan',
        @json($perJurusan->pluck('label')),
        @json($perJurusan->pluck('total')->map(fn($v) => (int)$v))
    );
    @endif

    @if($perStatusVerifikasi->isNotEmpty())
    doughnutChart(
        'chartVerifikasi',
        @json($perStatusVerifikasi->pluck('label')),
        @json($perStatusVerifikasi->pluck('total'))
    );
    @endif

    @if($perStatusHasil->isNotEmpty())
    doughnutChart(
        'chartHasil',
        @json($perStatusHasil->pluck('label')),
        @json($perStatusHasil->pluck('total'))
    );
    @endif
})();
</script>
@endpush
