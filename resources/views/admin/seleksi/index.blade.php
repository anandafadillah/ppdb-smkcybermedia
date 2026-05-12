@extends('layouts.app')

@section('title', 'Manajemen Seleksi')

@section('content')
@php
    $totalLolos     = $ranking->where('rekomendasi_status', 'lolos')->count();
    $totalCadangan  = $ranking->where('rekomendasi_status', 'cadangan')->count();
    $totalTidak     = $ranking->where('rekomendasi_status', 'tidak_lolos')->count();
@endphp

{{-- Header --}}
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
    <div>
        <h2 class="text-title-sm2 font-bold text-gray-800 dark:text-white/90">Manajemen Seleksi</h2>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            Ranking otomatis berdasarkan nilai rapor dan umur peserta
            @if($tahunAktif)
                · Tahun <span class="font-medium text-gray-700 dark:text-gray-300">{{ $tahunAktif->label ?? $tahunAktif->tahun }}</span>
            @endif
        </p>
    </div>
    <div class="flex flex-wrap gap-2 shrink-0">
        {{-- Hitung Ulang --}}
        <form method="POST" action="{{ route('admin.seleksi.hitung-semua') }}"
              x-data
              @submit.prevent="if(confirm('Hitung ulang skor semua peserta yang sudah submit? Proses ini mungkin membutuhkan waktu.')) $el.submit()">
            @csrf
            <button type="submit"
                class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Hitung Ulang Semua
            </button>
        </form>

        {{-- Terapkan Hasil --}}
        @if($selectedJurusan && $selectedJalur && $ranking->isNotEmpty())
        <form method="POST" action="{{ route('admin.seleksi.terapkan-hasil') }}"
              x-data
              @submit.prevent="if(confirm('Terapkan rekomendasi status hasil untuk {{ $selectedJurusan->nama }} — {{ $selectedJalur->nama }}? Status hasil peserta akan diperbarui.')) $el.submit()">
            @csrf
            <input type="hidden" name="jurusan_id" value="{{ $jurusanId }}">
            <input type="hidden" name="jalur_id" value="{{ $jalurId }}">
            <button type="submit"
                class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors shadow-theme-xs">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M5 13l4 4L19 7"/>
                </svg>
                Terapkan Hasil
            </button>
        </form>
        @endif
    </div>
</div>

{{-- Alert --}}
@if(session('success'))
    <div class="mb-4 rounded-xl border border-success-200 bg-success-50 px-4 py-3 text-sm text-success-700 dark:border-success-500/20 dark:bg-success-500/10 dark:text-success-400">
        {{ session('success') }}
    </div>
@endif

@if(!$tahunAktif)
    <div class="rounded-xl border border-warning-200 bg-warning-50 px-4 py-4 text-sm text-warning-700 dark:border-warning-500/20 dark:bg-warning-500/10 dark:text-warning-400">
        Tidak ada tahun penerimaan yang aktif. Aktifkan tahun penerimaan terlebih dahulu.
    </div>
@else

{{-- Filter --}}
<div class="mb-5 rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 p-4">
    <form method="GET" action="{{ route('admin.seleksi.index') }}" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-40">
            <label class="mb-1.5 block text-xs font-medium text-gray-600 dark:text-gray-400">Jurusan</label>
            <select name="jurusan_id" onchange="this.form.submit()"
                class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white/90">
                @foreach($jurusanList as $j)
                    <option value="{{ $j->id }}" @selected($jurusanId == $j->id)>{{ $j->nama }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex-1 min-w-40">
            <label class="mb-1.5 block text-xs font-medium text-gray-600 dark:text-gray-400">Jalur Pendaftaran</label>
            <select name="jalur_id" onchange="this.form.submit()"
                class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white/90">
                @forelse($jalurList as $j)
                    <option value="{{ $j->id }}" @selected($jalurId == $j->id)>{{ $j->nama }}</option>
                @empty
                    <option value="">-- Tidak ada jalur aktif --</option>
                @endforelse
            </select>
        </div>
    </form>
</div>

{{-- KPI Cards --}}
@if($selectedJurusan && $selectedJalur)
<div class="grid grid-cols-2 gap-4 sm:grid-cols-4 mb-5">
    <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
        <p class="text-xs text-gray-500 dark:text-gray-400">Kapasitas Kuota</p>
        <p class="mt-1 text-2xl font-bold text-gray-800 dark:text-white/90">{{ $kuota }}</p>
        <p class="text-xs text-gray-400 mt-0.5">dari {{ $selectedJurusan->kapasitas }} kursi ({{ $selectedJalur->persentase_kuota }}%)</p>
    </div>
    <div class="rounded-2xl border border-success-200 bg-success-50 p-4 dark:border-success-500/20 dark:bg-success-500/10">
        <p class="text-xs text-success-600 dark:text-success-400">Rekomendasi Lolos</p>
        <p class="mt-1 text-2xl font-bold text-success-700 dark:text-success-400">{{ $totalLolos }}</p>
        <p class="text-xs text-success-500 mt-0.5">peserta</p>
    </div>
    <div class="rounded-2xl border border-warning-200 bg-warning-50 p-4 dark:border-warning-500/20 dark:bg-warning-500/10">
        <p class="text-xs text-warning-600 dark:text-warning-400">Cadangan</p>
        <p class="mt-1 text-2xl font-bold text-warning-700 dark:text-warning-400">{{ $totalCadangan }}</p>
        <p class="text-xs text-warning-500 mt-0.5">peserta</p>
    </div>
    <div class="rounded-2xl border border-error-200 bg-error-50 p-4 dark:border-error-500/20 dark:bg-error-500/10">
        <p class="text-xs text-error-600 dark:text-error-400">Tidak Lolos</p>
        <p class="mt-1 text-2xl font-bold text-error-700 dark:text-error-400">{{ $totalTidak }}</p>
        <p class="text-xs text-error-500 mt-0.5">peserta</p>
    </div>
</div>
@endif

{{-- Tabel Ranking --}}
<div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 overflow-hidden shadow-theme-sm">
    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-800">
        <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">
            Ranking Peserta
            @if($selectedJurusan && $selectedJalur)
                <span class="ml-2 text-sm font-normal text-gray-500">{{ $selectedJurusan->nama }} · {{ $selectedJalur->nama }}</span>
            @endif
        </h3>
        @if($ranking->isNotEmpty())
            <span class="text-xs text-gray-400">{{ $ranking->count() }} peserta</span>
        @endif
    </div>

    @if($ranking->isEmpty())
        <div class="py-16 text-center">
            <svg class="mx-auto mb-3 text-gray-300 dark:text-gray-700" width="40" height="40" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                    d="M9 12h6m-3-3v6M12 3a9 9 0 100 18A9 9 0 0012 3z"/>
            </svg>
            <p class="text-sm text-gray-400 dark:text-gray-500">
                @if(!$selectedJurusan || !$selectedJalur)
                    Pilih jurusan dan jalur untuk melihat ranking.
                @else
                    Belum ada data skor. Klik <strong>Hitung Ulang Semua</strong> untuk memulai perhitungan.
                @endif
            </p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/50">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 w-12">Rank</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Nama Peserta</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400">Skor Nilai</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400">Skor Umur</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400">Skor Total</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400">Umur</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400">Rekomendasi</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400">Status Saat Ini</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach($ranking as $skor)
                    @php
                        $rekStatus = $skor->rekomendasi_status;
                        $rekBadge  = match($rekStatus) {
                            'lolos'       => 'bg-success-50 text-success-700 dark:bg-success-500/15 dark:text-success-400',
                            'cadangan'    => 'bg-warning-50 text-warning-700 dark:bg-warning-500/15 dark:text-warning-400',
                            default       => 'bg-error-50 text-error-700 dark:bg-error-500/15 dark:text-error-400',
                        };
                        $rekLabel  = match($rekStatus) {
                            'lolos'    => 'Lolos',
                            'cadangan' => 'Cadangan',
                            default    => 'Tidak Lolos',
                        };
                        $hasilBadge = match($skor->peserta->status_hasil) {
                            'lolos'       => 'bg-success-50 text-success-700 dark:bg-success-500/15 dark:text-success-400',
                            'cadangan'    => 'bg-warning-50 text-warning-700 dark:bg-warning-500/15 dark:text-warning-400',
                            'tidak_lolos' => 'bg-error-50 text-error-700 dark:bg-error-500/15 dark:text-error-400',
                            default       => 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400',
                        };
                        $hasilLabel = match($skor->peserta->status_hasil) {
                            'lolos'       => 'Lolos',
                            'cadangan'    => 'Cadangan',
                            'tidak_lolos' => 'Tidak Lolos',
                            default       => 'Belum',
                        };
                        $rowBg = $skor->kuota_posisi <= $kuota
                            ? ''
                            : ($skor->kuota_posisi <= $batasCadangan ? 'bg-warning-50/30 dark:bg-warning-500/5' : '');
                    @endphp
                    <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors {{ $rowBg }}">
                        <td class="px-4 py-3 text-center">
                            @if($skor->kuota_posisi <= 3)
                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full
                                    {{ $skor->kuota_posisi == 1 ? 'bg-yellow-100 text-yellow-700' : ($skor->kuota_posisi == 2 ? 'bg-gray-100 text-gray-600' : 'bg-orange-100 text-orange-700') }}
                                    text-xs font-bold">
                                    {{ $skor->kuota_posisi }}
                                </span>
                            @else
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $skor->kuota_posisi }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.peserta.show', $skor->peserta_id) }}"
                               class="text-sm font-medium text-gray-800 dark:text-white/90 hover:text-brand-600 dark:hover:text-brand-400">
                                {{ $skor->peserta->dataDiri?->nama_lengkap ?? 'N/A' }}
                            </a>
                            <p class="text-xs text-gray-400">{{ $skor->peserta->no_pendaftaran ?? '-' }}</p>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="font-mono text-sm text-gray-700 dark:text-gray-300">
                                {{ number_format($skor->skor_nilai, 1) }}
                            </span>
                            <p class="text-xs text-gray-400">bobot {{ $skor->bobot_nilai_snapshot }}%</p>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="font-mono text-sm text-gray-700 dark:text-gray-300">
                                {{ number_format($skor->skor_umur, 1) }}
                            </span>
                            <p class="text-xs text-gray-400">bobot {{ $skor->bobot_umur_snapshot }}%</p>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="font-mono text-base font-bold text-gray-800 dark:text-white/90">
                                {{ number_format($skor->skor_total, 2) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center text-sm text-gray-600 dark:text-gray-400">
                            {{ $skor->umur_saat_dihitung ?? '-' }} thn
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $rekBadge }}">
                                {{ $rekLabel }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $hasilBadge }}">
                                {{ $hasilLabel }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Keterangan batas kuota --}}
        @if($kuota > 0)
        <div class="px-5 py-3 border-t border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/30 flex flex-wrap gap-4 text-xs text-gray-500 dark:text-gray-400">
            <span class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-sm bg-success-100 dark:bg-success-500/20 border border-success-300 dark:border-success-500/30 inline-block"></span>
                Rank 1–{{ $kuota }}: Lolos (kuota)
            </span>
            @if($batasCadangan > $kuota)
            <span class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-sm bg-warning-50 dark:bg-warning-500/10 border border-warning-200 dark:border-warning-500/20 inline-block"></span>
                Rank {{ $kuota+1 }}–{{ $batasCadangan }}: Cadangan (10% dari kuota)
            </span>
            @endif
            <span class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-sm bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 inline-block"></span>
                Rank > {{ $batasCadangan }}: Tidak Lolos
            </span>
        </div>
        @endif
    @endif
</div>
@endif
@endsection
