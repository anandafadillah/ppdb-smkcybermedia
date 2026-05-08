@extends('layouts.app')

@section('title', 'Detail Peserta')

@section('content')
@php $prefix = request()->segment(1); @endphp
<div class="max-w-4xl space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route($prefix . '.peserta.index') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400">← Kembali</a>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
                Detail Peserta — {{ $peserta->dataDiri?->nama_lengkap ?? $peserta->user?->name ?? '-' }}
            </h2>
        </div>
        <div class="flex items-center gap-2">
            @if($peserta->status_verifikasi === 'terverifikasi')
            <a href="{{ route($prefix . '.peserta.pdf', $peserta) }}"
               class="inline-flex items-center gap-2 bg-success-500 hover:bg-success-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h4a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                </svg>
                Unduh PDF
            </a>
            @endif
            <a href="{{ route($prefix . '.peserta.edit', $peserta) }}"
               class="inline-flex items-center gap-2 bg-brand-500 hover:bg-brand-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                Edit Jalur / Jurusan
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="rounded-xl border border-success-200 bg-success-50 dark:border-success-800 dark:bg-success-500/10 p-4">
        <p class="text-sm text-success-700 dark:text-success-400">{{ session('success') }}</p>
    </div>
    @endif

    {{-- Status Management --}}
    <div class="grid grid-cols-3 gap-4">
        {{-- Status Verifikasi --}}
        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-theme-sm p-5">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Status Verifikasi</h3>
            <p class="text-xs text-gray-400 mb-3">Diperbarui: {{ $peserta->updated_at->format('d/m/Y H:i') }}</p>
            <form method="POST" action="{{ route($prefix . '.peserta.verifikasi', $peserta) }}">
                @csrf @method('PATCH')
                <select name="status_verifikasi"
                    class="h-9 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-3 text-sm dark:text-white dark:bg-gray-900 mb-2">
                    @foreach(['belum_diverifikasi' => 'Belum Diverifikasi', 'terverifikasi' => 'Terverifikasi', 'ditolak' => 'Ditolak'] as $val => $label)
                    <option value="{{ $val }}" {{ $peserta->status_verifikasi === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('status_verifikasi') <p class="text-xs text-error-500 mb-1">{{ $message }}</p> @enderror
                <button type="submit" class="w-full bg-brand-500 hover:bg-brand-600 text-white text-xs font-semibold py-1.5 rounded-lg transition">Simpan</button>
            </form>
        </div>

        {{-- Status Hasil --}}
        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-theme-sm p-5">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Status Hasil Seleksi</h3>
            <p class="text-xs text-gray-400 mb-3">Diperbarui: {{ $peserta->updated_at->format('d/m/Y H:i') }}</p>
            <form method="POST" action="{{ route($prefix . '.peserta.hasil', $peserta) }}">
                @csrf @method('PATCH')
                <select name="status_hasil"
                    class="h-9 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-3 text-sm dark:text-white dark:bg-gray-900 mb-2">
                    @foreach(['belum' => 'Belum', 'lolos' => 'Lolos', 'tidak_lolos' => 'Tidak Lolos', 'cadangan' => 'Cadangan'] as $val => $label)
                    <option value="{{ $val }}" {{ $peserta->status_hasil === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('status_hasil') <p class="text-xs text-error-500 mb-1">{{ $message }}</p> @enderror
                <button type="submit" class="w-full bg-brand-500 hover:bg-brand-600 text-white text-xs font-semibold py-1.5 rounded-lg transition">Simpan</button>
            </form>
        </div>

        {{-- Status Daftar Ulang --}}
        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-theme-sm p-5">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Status Daftar Ulang</h3>
            <p class="text-xs text-gray-400 mb-3">Diperbarui: {{ $peserta->updated_at->format('d/m/Y H:i') }}</p>
            <form method="POST" action="{{ route($prefix . '.peserta.daftar-ulang', $peserta) }}">
                @csrf @method('PATCH')
                <select name="status_daftar_ulang"
                    class="h-9 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-3 text-sm dark:text-white dark:bg-gray-900 mb-2">
                    @foreach(['belum' => 'Belum', 'sudah' => 'Sudah'] as $val => $label)
                    <option value="{{ $val }}" {{ $peserta->status_daftar_ulang === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('status_daftar_ulang') <p class="text-xs text-error-500 mb-1">{{ $message }}</p> @enderror
                <button type="submit" class="w-full bg-brand-500 hover:bg-brand-600 text-white text-xs font-semibold py-1.5 rounded-lg transition">Simpan</button>
            </form>
        </div>
    </div>

    {{-- Reset Password --}}
    <div class="rounded-xl border border-warning-200 bg-warning-50 dark:border-warning-800 dark:bg-warning-500/10 shadow-theme-sm p-5"
         x-data="{ open: false }">
        <button type="button" @click="open = !open"
            class="flex w-full items-center justify-between text-sm font-semibold text-warning-700 dark:text-warning-400">
            <span>Reset Password Peserta</span>
            <svg :class="open ? 'rotate-180' : ''" class="transition-transform" width="16" height="16" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
        <div x-show="open" x-transition class="mt-4">
            <form method="POST" action="{{ route($prefix . '.peserta.reset-password', $peserta) }}">
                @csrf @method('PATCH')
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Password Baru</label>
                        <input type="password" name="password" required minlength="8"
                            class="h-9 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-3 text-sm dark:text-white focus:outline-none focus:ring-2 focus:ring-warning-400">
                        @error('password') <p class="text-xs text-error-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" required minlength="8"
                            class="h-9 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-3 text-sm dark:text-white focus:outline-none focus:ring-2 focus:ring-warning-400">
                    </div>
                </div>
                <button type="submit" class="mt-3 rounded-lg bg-warning-500 hover:bg-warning-600 text-white text-xs font-semibold px-4 py-2 transition">
                    Reset Password
                </button>
            </form>
        </div>
    </div>

    {{-- Info Peserta --}}
    <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-theme-sm p-6 space-y-3 text-sm">
        <h3 class="font-semibold text-gray-800 dark:text-white">Data Pendaftaran</h3>
        <div class="grid grid-cols-2 gap-3 text-gray-600 dark:text-gray-300">
            <div><span class="font-medium">No. Pendaftaran:</span> {{ $peserta->no_pendaftaran ?? '-' }}</div>
            <div><span class="font-medium">NISN:</span> {{ $peserta->user?->nisn ?? '-' }}</div>
            <div><span class="font-medium">Jalur:</span> {{ $peserta->jalur?->nama ?? '-' }}</div>
            <div><span class="font-medium">Jurusan:</span> {{ $peserta->jurusan?->nama ?? '-' }}</div>
        </div>
    </div>

    {{-- Nilai Rapor --}}
    @if($peserta->nilai->count())
    <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-theme-sm p-6">
        <h3 class="font-semibold text-gray-800 dark:text-white mb-4">Nilai Rapor</h3>
        @php
            $nilaiGrouped = $peserta->nilai->groupBy('mata_pelajaran_id');
            $semesters = $peserta->nilai->pluck('semester')->unique()->sort()->values();
        @endphp
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                        <th class="px-4 py-2.5 text-left font-semibold text-gray-700 dark:text-gray-300">Mata Pelajaran</th>
                        @foreach($semesters as $sem)
                        <th class="px-4 py-2.5 text-center font-semibold text-gray-700 dark:text-gray-300">Sem {{ $sem }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach($nilaiGrouped as $mapelId => $nilaiRows)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                        <td class="px-4 py-2.5 text-gray-700 dark:text-gray-300">{{ $nilaiRows->first()->mataPelajaran?->nama ?? '-' }}</td>
                        @foreach($semesters as $sem)
                        @php $n = $nilaiRows->firstWhere('semester', $sem); @endphp
                        <td class="px-4 py-2.5 text-center text-gray-600 dark:text-gray-300">{{ $n?->nilai ?? '-' }}</td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Berkas --}}
    <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-theme-sm p-6">
        <h3 class="font-semibold text-gray-800 dark:text-white mb-4">Berkas yang Diupload</h3>
        @php
            $berkasByTipe = $peserta->berkas->keyBy('tipe_berkas');
            $tipeList = \App\Models\PesertaBerkas::tipeList();
        @endphp
        <div class="divide-y divide-gray-100 dark:divide-gray-800">
            @foreach($tipeList as $tipe => $label)
            @php $b = $berkasByTipe->get($tipe); @endphp
            <div class="flex items-center justify-between py-2.5">
                <span class="text-sm text-gray-700 dark:text-gray-300">{{ $label }}</span>
                @if($b && $b->file_path)
                    <a href="{{ route($prefix . '.berkas.download', $b) }}"
                       target="_blank"
                       class="text-xs font-medium text-brand-600 hover:text-brand-700 dark:text-brand-400">
                        Download
                    </a>
                @else
                    <span class="text-xs text-gray-400">Belum diupload</span>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
