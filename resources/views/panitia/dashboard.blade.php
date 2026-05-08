@extends('layouts.app')

@section('title', 'Dashboard Panitia')

@section('content')
@php
    $persenVerif    = $kpi['total'] > 0 ? round($kpi['terverifikasi'] / $kpi['total'] * 100, 1) : 0;
    $persenLolos    = $kpi['total'] > 0 ? round($kpi['lolos'] / $kpi['total'] * 100, 1) : 0;
    $persenDaftar   = $kpi['total'] > 0 ? round($kpi['daftar_ulang'] / $kpi['total'] * 100, 1) : 0;
    $maxPerJalur    = $perJalur->max('total') ?: 1;
    $maxPerJurusan  = $perJurusan->max('total') ?: 1;
@endphp

    {{-- Header --}}
    <div class="mb-6 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-title-sm2 font-bold text-gray-800 dark:text-white/90">Dashboard Panitia</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Selamat datang, {{ Auth::user()->name }}.
                @if($tahunAktif)
                    · Tahun aktif: <span class="font-medium text-gray-700 dark:text-gray-300">{{ $tahunAktif->label ?? $tahunAktif->tahun }}</span>
                @endif
            </p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('panitia.peserta.index') }}"
               class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                Manajemen Peserta
            </a>
            <a href="{{ route('panitia.statistik.index') }}"
               class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors shadow-theme-xs">
                Statistik Lengkap
            </a>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 md:gap-6 mb-6">
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gray-100 dark:bg-gray-800">
                <svg class="text-gray-800 dark:text-white/90" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M12 7.25C10.067 7.25 8.5 8.817 8.5 10.75C8.5 12.683 10.067 14.25 12 14.25C13.933 14.25 15.5 12.683 15.5 10.75C15.5 8.817 13.933 7.25 12 7.25ZM7 10.75C7 7.98858 9.23858 5.75 12 5.75C14.7614 5.75 17 7.98858 17 10.75C17 13.5114 14.7614 15.75 12 15.75C9.23858 15.75 7 13.5114 7 10.75ZM5 18.75C5 17.7835 5.7835 17 6.75 17H17.25C18.2165 17 19 17.7835 19 18.75V20.5H17.5V18.75C17.5 18.6119 17.3881 18.5 17.25 18.5H6.75C6.61193 18.5 6.5 18.6119 6.5 18.75V20.5H5V18.75Z" fill="currentColor"/>
                </svg>
            </div>
            <div class="mt-5 flex items-end justify-between">
                <div>
                    <span class="text-sm text-gray-500 dark:text-gray-400">Total Peserta</span>
                    <h4 class="mt-2 text-2xl font-bold text-gray-800 dark:text-white/90">{{ number_format($kpi['total']) }}</h4>
                </div>
                <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                    Terdaftar
                </span>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-success-50 dark:bg-success-500/10 text-success-600">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M12 3.25C7.16751 3.25 3.25 7.16751 3.25 12C3.25 16.8325 7.16751 20.75 12 20.75C16.8325 20.75 20.75 16.8325 20.75 12C20.75 7.16751 16.8325 3.25 12 3.25ZM16.0098 10.3081C16.3146 10.0273 16.3334 9.55288 16.0526 9.24809C15.7718 8.9433 15.2974 8.92447 14.9926 9.20528L10.7647 13.0996L9.00738 11.4823C8.70262 11.2017 8.22825 11.2207 7.94762 11.5255C7.66699 11.8302 7.68594 12.3046 7.99069 12.5852L10.2575 14.6708C10.5453 14.9358 10.9883 14.9352 11.2755 14.6694L16.0098 10.3081Z" fill="currentColor"/>
                </svg>
            </div>
            <div class="mt-5 flex items-end justify-between">
                <div>
                    <span class="text-sm text-gray-500 dark:text-gray-400">Terverifikasi</span>
                    <h4 class="mt-2 text-2xl font-bold text-gray-800 dark:text-white/90">{{ number_format($kpi['terverifikasi']) }}</h4>
                </div>
                <span class="inline-flex items-center gap-1 rounded-full bg-success-50 px-2 py-0.5 text-xs font-medium text-success-700 dark:bg-success-500/15 dark:text-success-500">
                    {{ $persenVerif }}%
                </span>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-50 dark:bg-brand-500/10 text-brand-600">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2.25C12.4142 2.25 12.75 2.58579 12.75 3V4.5H17.25C17.6642 4.5 18 4.83579 18 5.25V8.25C18 10.7884 16.1075 12.8847 13.6582 13.2055C13.1273 14.7237 11.6952 15.8125 10 15.8125H10V18.5H13.5C13.9142 18.5 14.25 18.8358 14.25 19.25C14.25 19.6642 13.9142 20 13.5 20H10.5H10H9.5C9.08579 20 8.75 19.6642 8.75 19.25C8.75 18.8358 9.08579 18.5 9.5 18.5H10V15.8125C8.30482 15.8125 6.87273 14.7237 6.34182 13.2055C3.89255 12.8847 2 10.7884 2 8.25V5.25C2 4.83579 2.33579 4.5 2.75 4.5H7.25V3C7.25 2.58579 7.58579 2.25 8 2.25H12Z" fill="currentColor"/>
                </svg>
            </div>
            <div class="mt-5 flex items-end justify-between">
                <div>
                    <span class="text-sm text-gray-500 dark:text-gray-400">Lolos Seleksi</span>
                    <h4 class="mt-2 text-2xl font-bold text-gray-800 dark:text-white/90">{{ number_format($kpi['lolos']) }}</h4>
                </div>
                <span class="inline-flex items-center gap-1 rounded-full bg-brand-50 px-2 py-0.5 text-xs font-medium text-brand-600 dark:bg-brand-500/15 dark:text-brand-400">
                    {{ $persenLolos }}%
                </span>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-warning-50 dark:bg-warning-500/10 text-warning-600">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M6.25 3.5C5.00736 3.5 4 4.50736 4 5.75V18.25C4 19.4926 5.00736 20.5 6.25 20.5H17.75C18.9926 20.5 20 19.4926 20 18.25V8.18198C20 7.5849 19.7629 7.01225 19.341 6.59039L16.4096 3.65901C15.9877 3.23715 15.4151 3 14.818 3H6.25Z" fill="currentColor"/>
                </svg>
            </div>
            <div class="mt-5 flex items-end justify-between">
                <div>
                    <span class="text-sm text-gray-500 dark:text-gray-400">Daftar Ulang</span>
                    <h4 class="mt-2 text-2xl font-bold text-gray-800 dark:text-white/90">{{ number_format($kpi['daftar_ulang']) }}</h4>
                </div>
                <span class="inline-flex items-center gap-1 rounded-full bg-warning-50 px-2 py-0.5 text-xs font-medium text-warning-700 dark:bg-warning-500/15 dark:text-warning-500">
                    {{ $persenDaftar }}%
                </span>
            </div>
        </div>
    </div>

    {{-- Distribusi --}}
    <div class="grid grid-cols-1 gap-4 lg:grid-cols-3 md:gap-6 mb-6">
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6 lg:col-span-2">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Distribusi per Jalur</h3>
                <a href="{{ route('panitia.statistik.index') }}" class="text-xs font-medium text-brand-600 dark:text-brand-400 hover:underline">
                    Lihat detail →
                </a>
            </div>

            @forelse($perJalur as $item)
                @php $pct = round(($item->total / $maxPerJalur) * 100); @endphp
                <div class="mb-4 last:mb-0">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $item->label }}</span>
                        <span class="text-sm font-semibold text-gray-800 dark:text-white/90">{{ number_format($item->total) }}</span>
                    </div>
                    <div class="h-2 rounded-full bg-gray-100 dark:bg-gray-800 overflow-hidden">
                        <div class="h-full rounded-full bg-brand-500 transition-all" style="width: {{ $pct }}%"></div>
                    </div>
                </div>
            @empty
                <div class="py-12 text-center">
                    <p class="text-sm text-gray-400">Belum ada data peserta.</p>
                </div>
            @endforelse
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90 mb-5">Per Jurusan</h3>

            @forelse($perJurusan as $i => $item)
                @php
                    $pct = round(($item->total / $maxPerJurusan) * 100);
                    $colors = ['bg-brand-500', 'bg-success-500', 'bg-warning-500', 'bg-error-500'];
                    $color = $colors[$i % count($colors)];
                @endphp
                <div class="mb-4 last:mb-0">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300 truncate pr-2">{{ $item->label }}</span>
                        <span class="text-xs font-semibold text-gray-600 dark:text-gray-400 shrink-0">{{ $item->total }}</span>
                    </div>
                    <div class="h-1.5 rounded-full bg-gray-100 dark:bg-gray-800 overflow-hidden">
                        <div class="h-full rounded-full {{ $color }} transition-all" style="width: {{ $pct }}%"></div>
                    </div>
                </div>
            @empty
                <div class="py-12 text-center">
                    <p class="text-sm text-gray-400">Belum ada data jurusan.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Status Verifikasi & Hasil --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:gap-6 mb-6">
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90 mb-4">Status Verifikasi</h3>
            <div class="space-y-3">
                @forelse($perVerifikasi as $item)
                    @php
                        $color = match($item['label']) {
                            'Terverifikasi' => ['bg-success-500', 'bg-success-50 dark:bg-success-500/10', 'text-success-700 dark:text-success-400'],
                            'Ditolak'       => ['bg-error-500',   'bg-error-50 dark:bg-error-500/10',     'text-error-700 dark:text-error-400'],
                            default         => ['bg-gray-400',    'bg-gray-50 dark:bg-gray-800/50',       'text-gray-600 dark:text-gray-400'],
                        };
                        $pct = $kpi['total'] > 0 ? round($item['total'] / $kpi['total'] * 100) : 0;
                    @endphp
                    <div class="flex items-center gap-3 rounded-xl {{ $color[1] }} px-4 py-3">
                        <span class="h-2.5 w-2.5 rounded-full {{ $color[0] }} shrink-0"></span>
                        <span class="text-sm font-medium {{ $color[2] }} flex-1">{{ $item['label'] }}</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $pct }}%</span>
                        <span class="text-sm font-bold text-gray-800 dark:text-white/90 min-w-8 text-right">{{ $item['total'] }}</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-400 text-center py-4">Belum ada data</p>
                @endforelse
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90 mb-4">Hasil Seleksi</h3>
            <div class="space-y-3">
                @forelse($perHasil as $item)
                    @php
                        $color = match($item['label']) {
                            'Lolos'       => ['bg-success-500', 'bg-success-50 dark:bg-success-500/10', 'text-success-700 dark:text-success-400'],
                            'Tidak Lolos' => ['bg-error-500',   'bg-error-50 dark:bg-error-500/10',     'text-error-700 dark:text-error-400'],
                            'Cadangan'    => ['bg-warning-500', 'bg-warning-50 dark:bg-warning-500/10', 'text-warning-700 dark:text-warning-400'],
                            default       => ['bg-gray-400',    'bg-gray-50 dark:bg-gray-800/50',       'text-gray-600 dark:text-gray-400'],
                        };
                        $pct = $kpi['total'] > 0 ? round($item['total'] / $kpi['total'] * 100) : 0;
                    @endphp
                    <div class="flex items-center gap-3 rounded-xl {{ $color[1] }} px-4 py-3">
                        <span class="h-2.5 w-2.5 rounded-full {{ $color[0] }} shrink-0"></span>
                        <span class="text-sm font-medium {{ $color[2] }} flex-1">{{ $item['label'] }}</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $pct }}%</span>
                        <span class="text-sm font-bold text-gray-800 dark:text-white/90 min-w-8 text-right">{{ $item['total'] }}</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-400 text-center py-4">Belum ada data</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Kuota Jalur --}}
    @if($jalurList->isNotEmpty())
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] p-5 md:p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90 mb-4">Kuota Jalur Pendaftaran</h3>
        <div class="space-y-3">
            @foreach($jalurList as $jalur)
            <form method="POST" action="{{ route('panitia.jalur-pendaftaran.kuota', $jalur) }}"
                  class="flex items-center gap-4">
                @csrf @method('PATCH')
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $jalur->nama }}</p>
                    <p class="text-xs text-gray-400">{{ $jalur->is_active ? 'Aktif' : 'Nonaktif' }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <input type="number" name="persentase_kuota"
                           value="{{ $jalur->persentase_kuota ?? 0 }}"
                           min="0" max="100"
                           class="h-9 w-20 rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-3 text-sm text-center dark:text-white focus:border-brand-300 focus:outline-hidden">
                    <span class="text-sm text-gray-500">%</span>
                    <button type="submit"
                            class="h-9 px-3 text-xs font-semibold bg-brand-500 hover:bg-brand-600 text-white rounded-lg transition">
                        Simpan
                    </button>
                </div>
            </form>
            @endforeach
        </div>
        @if(session('warning'))
        <div class="mt-3 rounded-lg border border-warning-200 bg-warning-50 dark:border-warning-800 dark:bg-warning-500/10 px-4 py-2.5">
            <p class="text-sm text-warning-700 dark:text-warning-400">{{ session('warning') }}</p>
        </div>
        @endif
    </div>
    @endif

    {{-- Quick Links --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:gap-6 mb-6">
        <a href="{{ route('panitia.nilai-rekap.index') }}"
           class="group rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] hover:border-brand-300 dark:hover:border-brand-700 hover:shadow-theme-md transition-all">
            <div class="flex items-center gap-4">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-brand-50 dark:bg-brand-500/10 text-brand-500 group-hover:bg-brand-500 group-hover:text-white transition-colors">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M3.25 5.5C3.25 4.26 4.26 3.25 5.5 3.25H18.5C19.74 3.25 20.75 4.26 20.75 5.5V18.5C20.75 19.74 19.74 20.75 18.5 20.75H5.5C4.26 20.75 3.25 19.74 3.25 18.5V5.5Z" fill="currentColor"/></svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-800 dark:text-white/90">Rekap Nilai</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Lihat nilai rapor peserta</p>
                </div>
            </div>
        </a>

        <a href="{{ route('panitia.asal-sekolah.index') }}"
           class="group rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] hover:border-success-300 dark:hover:border-success-700 hover:shadow-theme-md transition-all">
            <div class="flex items-center gap-4">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-success-50 dark:bg-success-500/10 text-success-600 group-hover:bg-success-500 group-hover:text-white transition-colors">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M3.25 5.5C3.25 4.26 4.26 3.25 5.5 3.25H18.5C19.74 3.25 20.75 4.26 20.75 5.5V18.5C20.75 19.74 19.74 20.75 18.5 20.75H5.5C4.26 20.75 3.25 19.74 3.25 18.5V5.5Z" fill="currentColor"/></svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-800 dark:text-white/90">Asal Sekolah</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Kelola data asal sekolah</p>
                </div>
            </div>
        </a>
    </div>

    {{-- Pengumuman --}}
    @if($pengumuman->isNotEmpty())
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 md:px-6 border-b border-gray-100 dark:border-gray-800">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Pengumuman Terbaru</h3>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-800">
            @foreach($pengumuman as $item)
            <div class="flex items-start gap-4 px-5 py-4 md:px-6 hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-brand-50 dark:bg-brand-500/10 text-brand-500">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24"><path d="M18 8a6 6 0 11-12 0 6 6 0 0112 0zM3 13.5l3 3.5L9 13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-800 dark:text-white/90 truncate">{{ $item->judul }}</p>
                    <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                        {{ $item->tanggal_publish?->translatedFormat('d M Y') ?? '-' }}
                    </p>
                </div>
                <span class="inline-flex items-center rounded-full bg-success-50 px-2 py-0.5 text-xs font-medium text-success-700 dark:bg-success-500/15 dark:text-success-500">
                    Published
                </span>
            </div>
            @endforeach
        </div>
    </div>
    @endif
@endsection
