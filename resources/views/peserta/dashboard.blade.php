@extends('layouts.app')

@section('title', 'Dashboard Peserta')

@section('content')
@php
    use App\Models\PesertaBerkas;

    $labelVerifikasi = [
        'belum_diverifikasi' => 'Belum Diverifikasi',
        'terverifikasi'      => 'Terverifikasi',
        'ditolak'            => 'Ditolak',
    ];
    $labelHasil = [
        'belum'       => 'Belum Diproses',
        'lolos'       => 'Lolos',
        'tidak_lolos' => 'Tidak Lolos',
        'cadangan'    => 'Cadangan',
    ];
    $totalTipeBerkas  = count(PesertaBerkas::tipeList());
    $uploadedBerkas   = $peserta ? $peserta->berkas->count() : 0;
    $sudahIsiNilai    = $peserta && $peserta->nilai->isNotEmpty();
@endphp

    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">Dashboard Peserta</h2>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            Selamat datang, {{ Auth::user()->name }}.
        </p>
    </div>

    {{-- Status Pendaftaran --}}
    <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 p-6 shadow-theme-sm space-y-4">
        <h3 class="text-base font-semibold text-gray-800 dark:text-white">Status Pendaftaran</h3>

        @if($peserta && $peserta->sudahSubmit())
            {{-- Nomor Pendaftaran --}}
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-600 dark:text-gray-400">Nomor Pendaftaran</span>
                <span class="font-mono text-base font-semibold text-brand-600 dark:text-brand-400">
                    {{ $peserta->no_pendaftaran }}
                </span>
            </div>

            {{-- Status Formulir --}}
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-600 dark:text-gray-400">Status Formulir</span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400">
                    Submitted
                </span>
            </div>

            {{-- Nama & Jalur & Jurusan --}}
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-600 dark:text-gray-400">Nama Lengkap</span>
                <span class="text-sm text-gray-800 dark:text-white">{{ $peserta->dataDiri?->nama_lengkap ?? '-' }}</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-600 dark:text-gray-400">Jalur Pendaftaran</span>
                <span class="text-sm text-gray-800 dark:text-white">{{ $peserta->jalur?->nama ?? '-' }}</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-600 dark:text-gray-400">Jurusan</span>
                <span class="text-sm text-gray-800 dark:text-white">{{ $peserta->jurusan?->nama ?? '-' }}</span>
            </div>
        @else
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-600 dark:text-gray-400">Status Formulir</span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                    Belum diisi
                </span>
            </div>
            <a href="{{ route('peserta.formulir.index') }}"
               class="inline-flex items-center gap-2 bg-brand-500 hover:bg-brand-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                Isi Formulir Sekarang
            </a>
        @endif
    </div>

    @if($peserta && $peserta->sudahSubmit())
    {{-- Grid kartu status detail --}}
    <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-5">

        {{-- Status Verifikasi --}}
        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 p-5 shadow-theme-sm">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">Status Verifikasi</p>
            @php
                $sv = $peserta->status_verifikasi;
                $svColor = match($sv) {
                    'terverifikasi' => 'bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400',
                    'ditolak'       => 'bg-error-50 text-error-700 dark:bg-error-500/10 dark:text-error-400',
                    default         => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300',
                };
            @endphp
            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $svColor }}">
                {{ $labelVerifikasi[$sv] ?? $sv }}
            </span>
        </div>

        {{-- Status Hasil Seleksi --}}
        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 p-5 shadow-theme-sm">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">Hasil Seleksi</p>
            @php
                $sh = $peserta->status_hasil;
                $shColor = match($sh) {
                    'lolos'       => 'bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400',
                    'tidak_lolos' => 'bg-error-50 text-error-700 dark:bg-error-500/10 dark:text-error-400',
                    'cadangan'    => 'bg-warning-50 text-warning-700 dark:bg-warning-500/10 dark:text-warning-400',
                    default       => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300',
                };
            @endphp
            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $shColor }}">
                {{ $labelHasil[$sh] ?? $sh }}
            </span>
        </div>

        {{-- Status Berkas --}}
        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 p-5 shadow-theme-sm">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">Berkas Terupload</p>
            <p class="text-lg font-bold text-gray-800 dark:text-white/90">
                {{ $uploadedBerkas }} / {{ $totalTipeBerkas }}
            </p>
            <a href="{{ route('peserta.berkas.index') }}"
               class="mt-2 inline-block text-xs font-medium text-brand-600 dark:text-brand-400 hover:underline">
                Kelola Berkas →
            </a>
        </div>

        {{-- Status Nilai --}}
        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 p-5 shadow-theme-sm">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">Nilai Rapor</p>
            @if($sudahIsiNilai)
                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400">
                    Sudah Diisi
                </span>
            @else
                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                    Belum Diisi
                </span>
            @endif
            <a href="{{ route('peserta.nilai.index') }}"
               class="mt-2 block text-xs font-medium text-brand-600 dark:text-brand-400 hover:underline">
                Isi Nilai →
            </a>
        </div>

        {{-- Status Daftar Ulang --}}
        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 p-5 shadow-theme-sm">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">Daftar Ulang</p>
            @php
                $sdu = $peserta->status_daftar_ulang;
                $sduColor = $sdu === 'sudah'
                    ? 'bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400'
                    : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300';
                $sduLabel = $sdu === 'sudah' ? 'Sudah Daftar Ulang' : 'Belum Daftar Ulang';
            @endphp
            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $sduColor }}">
                {{ $sduLabel }}
            </span>
            @if($sdu === 'belum' && $peserta->status_hasil === 'lolos')
                <p class="mt-2 text-xs text-warning-600 dark:text-warning-400 font-medium">
                    Segera lakukan daftar ulang ke sekolah.
                </p>
            @endif
        </div>

    </div>

    {{-- Tombol Unduh PDF — hanya jika terverifikasi --}}
    @if($peserta->status_verifikasi === 'terverifikasi')
    <div class="mt-5">
        <a href="{{ route('peserta.formulir.pdf') }}"
           class="inline-flex items-center gap-2 rounded-xl bg-success-500 hover:bg-success-600 px-5 py-2.5 text-sm font-semibold text-white transition-colors shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h4a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
            </svg>
            Unduh Formulir PDF
        </a>
        <p class="mt-1.5 text-xs text-gray-400 dark:text-gray-500">Formulir dapat diunduh karena sudah diverifikasi oleh panitia.</p>
    </div>
    @endif
    @endif

    {{-- Pengumuman --}}
    @if(isset($pengumuman) && $pengumuman->isNotEmpty())
    <div class="mt-6 rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-theme-sm p-6 space-y-3">
        <h3 class="font-semibold text-gray-800 dark:text-white">Pengumuman Terbaru</h3>
        <div class="divide-y divide-gray-100 dark:divide-gray-800">
            @foreach($pengumuman as $item)
            <div class="py-3">
                <p class="text-sm font-medium text-gray-800 dark:text-white">{{ $item->judul }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $item->tanggal_publish?->format('d/m/Y') }}</p>
            </div>
            @endforeach
        </div>
    </div>
    @endif
@endsection
