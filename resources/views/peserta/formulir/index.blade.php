@extends('layouts.app')

@section('title', 'Formulir Pendaftaran')

@section('content')
@php
    $sudahSubmit  = $peserta && $peserta->sudahSubmit();
    $terkunciKel  = $peserta && $peserta->status_verifikasi !== 'belum_diverifikasi';
    $isAfirmasi   = $peserta?->jalur?->isAfirmasi();
    $c            = $config; // shorthand
@endphp

<div class="max-w-3xl space-y-6" x-data="{ tab: '{{ $activeTab }}' }">

    {{-- Header --}}
    <div>
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Formulir Pendaftaran</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400">Lengkapi semua data dengan benar sebelum submit</p>
    </div>

    {{-- Keterangan Formulir --}}
    @if($keterangan)
    <div class="rounded-xl border border-brand-200 bg-brand-50 dark:border-brand-800 dark:bg-brand-500/10 p-4">
        <p class="text-sm text-brand-700 dark:text-brand-400 whitespace-pre-line">{{ $keterangan }}</p>
    </div>
    @endif

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="rounded-xl border border-success-200 bg-success-50 dark:border-success-800 dark:bg-success-500/10 p-4">
        <p class="text-sm text-success-700 dark:text-success-400">{{ session('success') }}</p>
    </div>
    @endif
    @if(session('error'))
    <div class="rounded-xl border border-error-200 bg-error-50 dark:border-error-800 dark:bg-error-500/10 p-4">
        <p class="text-sm text-error-700 dark:text-error-400">{{ session('error') }}</p>
    </div>
    @endif

    {{-- Tab Navigation --}}
    <div class="flex gap-1 border-b border-gray-200 dark:border-gray-700">
        <button type="button" @click="tab = 'data-diri'"
            :class="tab === 'data-diri'
                ? 'border-b-2 border-brand-500 text-brand-600 dark:text-brand-400'
                : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'"
            class="px-4 py-2.5 text-sm font-medium transition-colors -mb-px flex items-center gap-2">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M12 3.5C7.30558 3.5 3.5 7.30558 3.5 12C3.5 14.1526 4.3002 16.1184 5.61936 17.616C6.17279 15.3096 8.24852 13.5955 10.7246 13.5955H13.2746C15.7509 13.5955 17.8268 15.31 18.38 17.6167C19.6996 16.119 20.5 14.153 20.5 12C20.5 7.30558 16.6944 3.5 12 3.5Z" fill="currentColor" opacity=".4"/><path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2Z" stroke="currentColor" stroke-width="1.5"/></svg>
            Data Diri
            @if($sudahSubmit)
            <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400">✓</span>
            @endif
        </button>
        <button type="button" @click="tab = 'data-keluarga'"
            :class="tab === 'data-keluarga'
                ? 'border-b-2 border-brand-500 text-brand-600 dark:text-brand-400'
                : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'"
            class="px-4 py-2.5 text-sm font-medium transition-colors -mb-px flex items-center gap-2">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M8.5 7C8.5 5.067 10.067 3.5 12 3.5C13.933 3.5 15.5 5.067 15.5 7C15.5 8.933 13.933 10.5 12 10.5C10.067 10.5 8.5 8.933 8.5 7ZM5.25 19C5.25 16.3766 7.37665 14.25 10 14.25H14C16.6234 14.25 18.75 16.3766 18.75 19V20.25H17.25V19C17.25 17.2051 15.7949 15.75 14 15.75H10C8.20507 15.75 6.75 17.2051 6.75 19V20.25H5.25V19Z" fill="currentColor"/></svg>
            Keluarga & Alamat
            @if($peserta?->dataAyah || $peserta?->dataIbu)
            <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400">✓</span>
            @endif
        </button>
    </div>

    {{-- ═══════════════════════════════════════ --}}
    {{-- TAB: DATA DIRI                          --}}
    {{-- ═══════════════════════════════════════ --}}
    <div x-show="tab === 'data-diri'" x-transition>
        @if($sudahSubmit)
        <div class="rounded-xl border border-success-200 bg-success-50 dark:border-success-800 dark:bg-success-500/10 p-5 mb-4">
            <p class="text-sm font-semibold text-success-700 dark:text-success-400">Formulir Telah Disubmit</p>
            <p class="text-sm text-success-700 dark:text-success-400 mt-1">
                Nomor Pendaftaran Anda: <strong class="font-mono text-base">{{ $peserta->no_pendaftaran }}</strong>
            </p>
            <p class="mt-1.5 text-xs text-success-600 dark:text-success-500">Data diri tidak dapat diubah setelah submit.</p>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-theme-sm p-6 space-y-3 text-sm text-gray-700 dark:text-gray-300">
            <div class="grid grid-cols-2 gap-3">
                <div><span class="font-medium text-gray-500 dark:text-gray-400">Nama Lengkap</span><p class="mt-0.5">{{ $peserta->dataDiri?->nama_lengkap ?? '-' }}</p></div>
                @if(!$c || $c->isFieldActive('diri_nisn'))
                <div><span class="font-medium text-gray-500 dark:text-gray-400">NISN</span><p class="mt-0.5 font-mono">{{ $peserta->user?->nisn ?? '-' }}</p></div>
                @endif
                <div><span class="font-medium text-gray-500 dark:text-gray-400">Jalur</span><p class="mt-0.5">{{ $peserta->jalur?->nama ?? '-' }}</p></div>
                <div><span class="font-medium text-gray-500 dark:text-gray-400">Jurusan</span><p class="mt-0.5">{{ $peserta->jurusan?->nama ?? '-' }}</p></div>
                <div><span class="font-medium text-gray-500 dark:text-gray-400">Jenis Kelamin</span><p class="mt-0.5">{{ $peserta->dataDiri?->jenis_kelamin === 'L' ? 'Laki-laki' : ($peserta->dataDiri?->jenis_kelamin === 'P' ? 'Perempuan' : '-') }}</p></div>
                @if(!$c || $c->isFieldActive('diri_agama'))
                <div><span class="font-medium text-gray-500 dark:text-gray-400">Agama</span><p class="mt-0.5">{{ $peserta->dataDiri?->agama ?? '-' }}</p></div>
                @endif
                @if(!$c || $c->isFieldActive('diri_tempat_lahir'))
                <div><span class="font-medium text-gray-500 dark:text-gray-400">Tempat Lahir</span><p class="mt-0.5">{{ $peserta->dataDiri?->tempat_lahir ?? '-' }}</p></div>
                @endif
                @if(!$c || $c->isFieldActive('diri_tanggal_lahir'))
                <div><span class="font-medium text-gray-500 dark:text-gray-400">Tanggal Lahir</span><p class="mt-0.5">{{ $peserta->dataDiri?->tanggal_lahir?->format('d/m/Y') ?? '-' }}</p></div>
                @endif
                @if(!$c || $c->isFieldActive('diri_no_hp'))
                <div><span class="font-medium text-gray-500 dark:text-gray-400">No. HP</span><p class="mt-0.5">{{ $peserta->dataDiri?->no_hp ?? '-' }}</p></div>
                @endif
                @if(!$c || $c->isFieldActive('diri_asal_sekolah'))
                <div><span class="font-medium text-gray-500 dark:text-gray-400">Asal Sekolah</span><p class="mt-0.5">{{ $peserta->dataDiri?->asalSekolah?->nama ?? $peserta->dataDiri?->asal_sekolah_custom ?? '-' }}</p></div>
                @endif
            </div>
        </div>
        @else
        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-theme-sm p-6">
            <form method="POST" action="{{ route('peserta.formulir.store') }}" class="space-y-5">
                @csrf

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Jalur Pendaftaran <span class="text-error-500">*</span></label>
                        <select name="jalur_id"
                            class="h-11 w-full rounded-lg border @error('jalur_id') border-error-500 @else border-gray-300 dark:border-gray-700 @enderror bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white dark:bg-gray-900">
                            <option value="">-- Pilih Jalur --</option>
                            @foreach($jalur as $j)
                                <option value="{{ $j->id }}" {{ old('jalur_id', $peserta?->jalur_id) == $j->id ? 'selected' : '' }}>{{ $j->nama }}</option>
                            @endforeach
                        </select>
                        @error('jalur_id') <p class="mt-1 text-xs text-error-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Jurusan <span class="text-error-500">*</span></label>
                        <select name="jurusan_id"
                            class="h-11 w-full rounded-lg border @error('jurusan_id') border-error-500 @else border-gray-300 dark:border-gray-700 @enderror bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white dark:bg-gray-900">
                            <option value="">-- Pilih Jurusan --</option>
                            @foreach($jurusan as $j)
                                <option value="{{ $j->id }}" {{ old('jurusan_id', $peserta?->jurusan_id) == $j->id ? 'selected' : '' }}>{{ $j->nama }}</option>
                            @endforeach
                        </select>
                        @error('jurusan_id') <p class="mt-1 text-xs text-error-500">{{ $message }}</p> @enderror
                    </div>
                </div>

                <hr class="border-gray-100 dark:border-gray-800">

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nama Lengkap <span class="text-error-500">*</span></label>
                    <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $peserta?->dataDiri?->nama_lengkap) }}"
                        class="h-11 w-full rounded-lg border @error('nama_lengkap') border-error-500 @else border-gray-300 dark:border-gray-700 @enderror bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white">
                    @error('nama_lengkap') <p class="mt-1 text-xs text-error-500">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Jenis Kelamin <span class="text-error-500">*</span></label>
                        <select name="jenis_kelamin"
                            class="h-11 w-full rounded-lg border @error('jenis_kelamin') border-error-500 @else border-gray-300 dark:border-gray-700 @enderror bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white dark:bg-gray-900">
                            <option value="">-- Pilih --</option>
                            <option value="L" {{ old('jenis_kelamin', $peserta?->dataDiri?->jenis_kelamin) === 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('jenis_kelamin', $peserta?->dataDiri?->jenis_kelamin) === 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        @error('jenis_kelamin') <p class="mt-1 text-xs text-error-500">{{ $message }}</p> @enderror
                    </div>
                    @if(!$c || $c->isFieldActive('diri_agama'))
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Agama</label>
                        <input type="text" name="agama" value="{{ old('agama', $peserta?->dataDiri?->agama) }}"
                            placeholder="Islam, Kristen, dll."
                            class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white">
                    </div>
                    @endif
                </div>

                @if(!$c || $c->isFieldActive('diri_tempat_lahir') || $c->isFieldActive('diri_tanggal_lahir'))
                <div class="grid grid-cols-2 gap-4">
                    @if(!$c || $c->isFieldActive('diri_tempat_lahir'))
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Tempat Lahir</label>
                        <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir', $peserta?->dataDiri?->tempat_lahir) }}"
                            class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white">
                    </div>
                    @endif
                    @if(!$c || $c->isFieldActive('diri_tanggal_lahir'))
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $peserta?->dataDiri?->tanggal_lahir?->format('Y-m-d')) }}"
                            class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white">
                    </div>
                    @endif
                </div>
                @endif

                @if(!$c || $c->isFieldActive('diri_no_hp') || $c->isFieldActive('diri_tinggi_badan') || $c->isFieldActive('diri_berat_badan'))
                <div class="grid grid-cols-3 gap-4">
                    @if(!$c || $c->isFieldActive('diri_no_hp'))
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">No. HP</label>
                        <input type="text" name="no_hp" value="{{ old('no_hp', $peserta?->dataDiri?->no_hp) }}"
                            class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white">
                    </div>
                    @endif
                    @if(!$c || $c->isFieldActive('diri_tinggi_badan'))
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Tinggi Badan (cm)</label>
                        <input type="number" name="tinggi_badan" value="{{ old('tinggi_badan', $peserta?->dataDiri?->tinggi_badan) }}"
                            class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white">
                    </div>
                    @endif
                    @if(!$c || $c->isFieldActive('diri_berat_badan'))
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Berat Badan (kg)</label>
                        <input type="number" name="berat_badan" value="{{ old('berat_badan', $peserta?->dataDiri?->berat_badan) }}"
                            class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white">
                    </div>
                    @endif
                </div>
                @endif

                @if(!$c || $c->isFieldActive('diri_jumlah_saudara'))
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Jumlah Saudara</label>
                    <input type="number" name="jumlah_saudara" value="{{ old('jumlah_saudara', $peserta?->dataDiri?->jumlah_saudara) }}" min="0" max="20"
                        class="h-11 w-48 rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white">
                </div>
                @endif

                @if(!$c || $c->isFieldActive('diri_asal_sekolah'))
                <div x-data="{ custom: {{ (old('asal_sekolah_id') === 'other' || old('asal_sekolah_custom') || $peserta?->dataDiri?->asal_sekolah_custom) ? 'true' : 'false' }} }">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Asal Sekolah</label>
                    <select name="asal_sekolah_id"
                        @change="custom = $event.target.value === 'other'"
                        class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white dark:bg-gray-900">
                        <option value="">-- Pilih Sekolah --</option>
                        @foreach($asalSekolah as $sekolah)
                            <option value="{{ $sekolah->id }}" {{ old('asal_sekolah_id', $peserta?->dataDiri?->asal_sekolah_id) == $sekolah->id ? 'selected' : '' }}>
                                {{ $sekolah->nama }} ({{ $sekolah->npsn }})
                            </option>
                        @endforeach
                        <option value="other" {{ old('asal_sekolah_id') === 'other' || $peserta?->dataDiri?->asal_sekolah_custom ? 'selected' : '' }}>Lainnya...</option>
                    </select>
                    <div x-show="custom" x-transition class="mt-2">
                        <input type="text" name="asal_sekolah_custom" value="{{ old('asal_sekolah_custom', $peserta?->dataDiri?->asal_sekolah_custom) }}"
                            placeholder="Nama sekolah asal"
                            class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white">
                    </div>
                </div>
                @endif

                <div class="pt-2 flex items-center gap-4">
                    <button type="submit"
                        onclick="return confirm('Yakin ingin submit? Data diri tidak dapat diubah setelah submit.')"
                        class="bg-brand-500 hover:bg-brand-600 text-white text-sm font-semibold px-6 py-2.5 rounded-lg transition">
                        Submit Data Diri
                    </button>
                    <p class="text-xs text-gray-400">Setelah submit, data diri terkunci. Data keluarga masih bisa dilengkapi.</p>
                </div>
            </form>
        </div>
        @endif
    </div>

    {{-- ═══════════════════════════════════════ --}}
    {{-- TAB: DATA KELUARGA & ALAMAT             --}}
    {{-- ═══════════════════════════════════════ --}}
    <div x-show="tab === 'data-keluarga'" x-transition>

        @if($terkunciKel)
        <div class="rounded-xl border border-warning-200 bg-warning-50 dark:border-warning-800 dark:bg-warning-500/10 p-4 mb-4">
            <p class="text-sm text-warning-700 dark:text-warning-400">
                <strong>Data terkunci.</strong> Data keluarga tidak dapat diubah karena berkas sudah diverifikasi.
            </p>
        </div>
        @endif

        <form method="POST" action="{{ route('peserta.keluarga.store') }}" class="space-y-5">
            @csrf

            {{-- DATA AYAH --}}
            <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-theme-sm p-6 space-y-4">
                <h3 class="text-base font-semibold text-gray-800 dark:text-white border-b border-gray-100 dark:border-gray-800 pb-3">Data Ayah</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nama Ayah <span class="text-error-500">*</span></label>
                        <input type="text" name="nama_ayah" value="{{ old('nama_ayah', $peserta?->dataAyah?->nama) }}"
                            {{ $terkunciKel ? 'disabled' : '' }}
                            class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white disabled:opacity-60 disabled:cursor-not-allowed">
                    </div>
                    @if(!$c || $c->isFieldActive('ayah_nik'))
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">NIK Ayah</label>
                        <input type="text" name="nik_ayah" value="{{ old('nik_ayah', $peserta?->dataAyah?->nik) }}" maxlength="16"
                            {{ $terkunciKel ? 'disabled' : '' }}
                            class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white disabled:opacity-60 disabled:cursor-not-allowed">
                    </div>
                    @endif
                    @if(!$c || $c->isFieldActive('ayah_tahun_lahir'))
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Tahun Lahir</label>
                        @php $valAyah = (int) old('tahun_lahir_ayah', $peserta?->dataAyah?->tahun_lahir); @endphp
                        <select name="tahun_lahir_ayah" {{ $terkunciKel ? 'disabled' : '' }}
                            class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 text-sm focus:border-brand-300 focus:outline-hidden dark:text-white dark:bg-gray-900 disabled:opacity-60 disabled:cursor-not-allowed">
                            <option value="">-- Pilih --</option>
                            @for($y = date('Y'); $y >= 1940; $y--)
                                <option value="{{ $y }}" {{ $valAyah === $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    @endif
                    @if(!$c || $c->isFieldActive('ayah_pendidikan'))
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Pendidikan</label>
                        <input type="text" name="pendidikan_ayah" value="{{ old('pendidikan_ayah', $peserta?->dataAyah?->pendidikan) }}"
                            {{ $terkunciKel ? 'disabled' : '' }}
                            class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white disabled:opacity-60 disabled:cursor-not-allowed">
                    </div>
                    @endif
                    @if(!$c || $c->isFieldActive('ayah_pekerjaan'))
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Pekerjaan</label>
                        <input type="text" name="pekerjaan_ayah" value="{{ old('pekerjaan_ayah', $peserta?->dataAyah?->pekerjaan) }}"
                            {{ $terkunciKel ? 'disabled' : '' }}
                            class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white disabled:opacity-60 disabled:cursor-not-allowed">
                    </div>
                    @endif
                    @if(!$c || $c->isFieldActive('ayah_penghasilan'))
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Penghasilan / Bulan</label>
                        <input type="text" name="penghasilan_ayah" value="{{ old('penghasilan_ayah', $peserta?->dataAyah?->penghasilan) }}"
                            {{ $terkunciKel ? 'disabled' : '' }}
                            class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white disabled:opacity-60 disabled:cursor-not-allowed">
                    </div>
                    @endif
                </div>
                @if(!$c || $c->isFieldActive('ayah_ketidakmampuan'))
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Ketidakmampuan Khusus</label>
                    <input type="text" name="ketidakmampuan_khusus_ayah" value="{{ old('ketidakmampuan_khusus_ayah', $peserta?->dataAyah?->ketidakmampuan_khusus) }}"
                        placeholder="Kosongkan jika tidak ada" {{ $terkunciKel ? 'disabled' : '' }}
                        class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white disabled:opacity-60 disabled:cursor-not-allowed">
                </div>
                @endif
            </div>

            {{-- DATA IBU --}}
            <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-theme-sm p-6 space-y-4">
                <h3 class="text-base font-semibold text-gray-800 dark:text-white border-b border-gray-100 dark:border-gray-800 pb-3">Data Ibu</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nama Ibu <span class="text-error-500">*</span></label>
                        <input type="text" name="nama_ibu" value="{{ old('nama_ibu', $peserta?->dataIbu?->nama) }}"
                            {{ $terkunciKel ? 'disabled' : '' }}
                            class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white disabled:opacity-60 disabled:cursor-not-allowed">
                    </div>
                    @if(!$c || $c->isFieldActive('ibu_nik'))
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">NIK Ibu</label>
                        <input type="text" name="nik_ibu" value="{{ old('nik_ibu', $peserta?->dataIbu?->nik) }}" maxlength="16"
                            {{ $terkunciKel ? 'disabled' : '' }}
                            class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white disabled:opacity-60 disabled:cursor-not-allowed">
                    </div>
                    @endif
                    @if(!$c || $c->isFieldActive('ibu_tahun_lahir'))
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Tahun Lahir</label>
                        @php $valIbu = (int) old('tahun_lahir_ibu', $peserta?->dataIbu?->tahun_lahir); @endphp
                        <select name="tahun_lahir_ibu" {{ $terkunciKel ? 'disabled' : '' }}
                            class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 text-sm focus:border-brand-300 focus:outline-hidden dark:text-white dark:bg-gray-900 disabled:opacity-60 disabled:cursor-not-allowed">
                            <option value="">-- Pilih --</option>
                            @for($y = date('Y'); $y >= 1940; $y--)
                                <option value="{{ $y }}" {{ $valIbu === $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    @endif
                    @if(!$c || $c->isFieldActive('ibu_pendidikan'))
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Pendidikan</label>
                        <input type="text" name="pendidikan_ibu" value="{{ old('pendidikan_ibu', $peserta?->dataIbu?->pendidikan) }}"
                            {{ $terkunciKel ? 'disabled' : '' }}
                            class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white disabled:opacity-60 disabled:cursor-not-allowed">
                    </div>
                    @endif
                    @if(!$c || $c->isFieldActive('ibu_pekerjaan'))
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Pekerjaan</label>
                        <input type="text" name="pekerjaan_ibu" value="{{ old('pekerjaan_ibu', $peserta?->dataIbu?->pekerjaan) }}"
                            {{ $terkunciKel ? 'disabled' : '' }}
                            class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white disabled:opacity-60 disabled:cursor-not-allowed">
                    </div>
                    @endif
                    @if(!$c || $c->isFieldActive('ibu_penghasilan'))
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Penghasilan / Bulan</label>
                        <input type="text" name="penghasilan_ibu" value="{{ old('penghasilan_ibu', $peserta?->dataIbu?->penghasilan) }}"
                            {{ $terkunciKel ? 'disabled' : '' }}
                            class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white disabled:opacity-60 disabled:cursor-not-allowed">
                    </div>
                    @endif
                </div>
                @if(!$c || $c->isFieldActive('ibu_ketidakmampuan'))
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Ketidakmampuan Khusus</label>
                    <input type="text" name="ketidakmampuan_khusus_ibu" value="{{ old('ketidakmampuan_khusus_ibu', $peserta?->dataIbu?->ketidakmampuan_khusus) }}"
                        placeholder="Kosongkan jika tidak ada" {{ $terkunciKel ? 'disabled' : '' }}
                        class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white disabled:opacity-60 disabled:cursor-not-allowed">
                </div>
                @endif
            </div>

            {{-- DATA WALI --}}
            @if(!$c || $c->isFieldActive('data_wali'))
            <div x-data="{ open: {{ $peserta?->dataWali ? 'true' : 'false' }} }"
                 class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-theme-sm p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-semibold text-gray-800 dark:text-white">Data Wali <span class="text-xs font-normal text-gray-400">(opsional)</span></h3>
                    <button type="button" @click="open = !open" {{ $terkunciKel ? 'disabled' : '' }}
                        class="text-sm text-brand-600 hover:text-brand-700 dark:text-brand-400 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span x-text="open ? 'Sembunyikan' : 'Tambah Data Wali'"></span>
                    </button>
                </div>
                <div x-show="open" x-transition class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nama Wali</label>
                        <input type="text" name="nama_wali" value="{{ old('nama_wali', $peserta?->dataWali?->nama) }}"
                            {{ $terkunciKel ? 'disabled' : '' }}
                            class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white disabled:opacity-60 disabled:cursor-not-allowed">
                    </div>
                    @if(!$c || $c->isFieldActive('wali_nik'))
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">NIK Wali</label>
                        <input type="text" name="nik_wali" value="{{ old('nik_wali', $peserta?->dataWali?->nik) }}" maxlength="16"
                            {{ $terkunciKel ? 'disabled' : '' }}
                            class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white disabled:opacity-60 disabled:cursor-not-allowed">
                    </div>
                    @endif
                    @if(!$c || $c->isFieldActive('wali_tahun_lahir'))
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Tahun Lahir</label>
                        @php $valWali = (int) old('tahun_lahir_wali', $peserta?->dataWali?->tahun_lahir); @endphp
                        <select name="tahun_lahir_wali" {{ $terkunciKel ? 'disabled' : '' }}
                            class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 text-sm focus:border-brand-300 focus:outline-hidden dark:text-white dark:bg-gray-900 disabled:opacity-60 disabled:cursor-not-allowed">
                            <option value="">-- Pilih --</option>
                            @for($y = date('Y'); $y >= 1940; $y--)
                                <option value="{{ $y }}" {{ $valWali === $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    @endif
                    @if(!$c || $c->isFieldActive('wali_pekerjaan'))
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Pekerjaan</label>
                        <input type="text" name="pekerjaan_wali" value="{{ old('pekerjaan_wali', $peserta?->dataWali?->pekerjaan) }}"
                            {{ $terkunciKel ? 'disabled' : '' }}
                            class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white disabled:opacity-60 disabled:cursor-not-allowed">
                    </div>
                    @endif
                    @if(!$c || $c->isFieldActive('wali_penghasilan'))
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Penghasilan / Bulan</label>
                        <input type="text" name="penghasilan_wali" value="{{ old('penghasilan_wali', $peserta?->dataWali?->penghasilan) }}"
                            {{ $terkunciKel ? 'disabled' : '' }}
                            class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white disabled:opacity-60 disabled:cursor-not-allowed">
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- ALAMAT --}}
            <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-theme-sm p-6 space-y-4">
                <h3 class="text-base font-semibold text-gray-800 dark:text-white border-b border-gray-100 dark:border-gray-800 pb-3">Alamat Tempat Tinggal</h3>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">RT <span class="text-error-500">*</span></label>
                        <input type="text" name="rt" value="{{ old('rt', $peserta?->dataAlamat?->rt) }}" maxlength="3"
                            {{ $terkunciKel ? 'disabled' : '' }}
                            class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white disabled:opacity-60 disabled:cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">RW <span class="text-error-500">*</span></label>
                        <input type="text" name="rw" value="{{ old('rw', $peserta?->dataAlamat?->rw) }}" maxlength="3"
                            {{ $terkunciKel ? 'disabled' : '' }}
                            class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white disabled:opacity-60 disabled:cursor-not-allowed">
                    </div>
                    @if(!$c || $c->isFieldActive('alamat_jarak'))
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Jarak (km)</label>
                        <input type="number" name="jarak_tempat_tinggal" value="{{ old('jarak_tempat_tinggal', $peserta?->dataAlamat?->jarak_tempat_tinggal) }}" min="0"
                            {{ $terkunciKel ? 'disabled' : '' }}
                            class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white disabled:opacity-60 disabled:cursor-not-allowed">
                    </div>
                    @endif
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Kelurahan <span class="text-error-500">*</span></label>
                        <input type="text" name="kelurahan" value="{{ old('kelurahan', $peserta?->dataAlamat?->kelurahan) }}"
                            {{ $terkunciKel ? 'disabled' : '' }}
                            class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white disabled:opacity-60 disabled:cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Kecamatan <span class="text-error-500">*</span></label>
                        <input type="text" name="kecamatan" value="{{ old('kecamatan', $peserta?->dataAlamat?->kecamatan) }}"
                            {{ $terkunciKel ? 'disabled' : '' }}
                            class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white disabled:opacity-60 disabled:cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Kota / Kabupaten <span class="text-error-500">*</span></label>
                        <input type="text" name="kota" value="{{ old('kota', $peserta?->dataAlamat?->kota) }}"
                            {{ $terkunciKel ? 'disabled' : '' }}
                            class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white disabled:opacity-60 disabled:cursor-not-allowed">
                    </div>
                </div>
                @if(!$c || $c->isFieldActive('alamat_koordinat'))
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Latitude</label>
                        <input type="text" name="latitude" value="{{ old('latitude', $peserta?->dataAlamat?->latitude) }}"
                            placeholder="-6.200000" {{ $terkunciKel ? 'disabled' : '' }}
                            class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white disabled:opacity-60 disabled:cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Longitude</label>
                        <input type="text" name="longitude" value="{{ old('longitude', $peserta?->dataAlamat?->longitude) }}"
                            placeholder="106.816666" {{ $terkunciKel ? 'disabled' : '' }}
                            class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white disabled:opacity-60 disabled:cursor-not-allowed">
                    </div>
                </div>
                @endif
            </div>

            {{-- DATA KIP/PKH --}}
            @if((!$c || $c->isFieldActive('data_kip')) && $isAfirmasi)
            <div class="rounded-xl border border-warning-200 bg-warning-50 dark:border-warning-800 dark:bg-warning-500/10 shadow-theme-sm p-6 space-y-4">
                <div>
                    <h3 class="text-base font-semibold text-warning-800 dark:text-warning-300">Data KIP / PKH</h3>
                    <p class="text-xs text-warning-600 dark:text-warning-400 mt-0.5">Diisi khusus untuk jalur Afirmasi</p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    @if(!$c || $c->isFieldActive('kip_no_kip'))
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">No. KIP</label>
                        <input type="text" name="no_kip" value="{{ old('no_kip', $peserta?->dataKip?->no_kip) }}"
                            {{ $terkunciKel ? 'disabled' : '' }}
                            class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white disabled:opacity-60 disabled:cursor-not-allowed">
                    </div>
                    @endif
                    @if(!$c || $c->isFieldActive('kip_no_kps_pkh'))
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">No. KPS/PKH</label>
                        <input type="text" name="no_kps_pkh" value="{{ old('no_kps_pkh', $peserta?->dataKip?->no_kps_pkh) }}"
                            {{ $terkunciKel ? 'disabled' : '' }}
                            class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white disabled:opacity-60 disabled:cursor-not-allowed">
                    </div>
                    @endif
                    @if(!$c || $c->isFieldActive('kip_nama_di_kip'))
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nama di KIP</label>
                        <input type="text" name="nama_di_kip" value="{{ old('nama_di_kip', $peserta?->dataKip?->nama_di_kip) }}"
                            {{ $terkunciKel ? 'disabled' : '' }}
                            class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white disabled:opacity-60 disabled:cursor-not-allowed">
                    </div>
                    @endif
                    @if(!$c || $c->isFieldActive('kip_terima'))
                    <div class="col-span-2">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="terima_kip" value="1"
                                {{ old('terima_kip', $peserta?->dataKip?->terima_kip) ? 'checked' : '' }}
                                {{ $terkunciKel ? 'disabled' : '' }}
                                class="h-4 w-4 rounded border-gray-300 text-brand-500 focus:ring-brand-500 disabled:cursor-not-allowed dark:border-gray-600 dark:bg-gray-800">
                            <span class="text-sm text-gray-700 dark:text-gray-300">Saya adalah penerima KIP</span>
                        </label>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            @if(!$terkunciKel)
            <div class="pt-2">
                <button type="submit"
                    class="bg-brand-500 hover:bg-brand-600 text-white text-sm font-semibold px-6 py-2.5 rounded-lg transition">
                    Simpan Data Keluarga & Alamat
                </button>
            </div>
            @endif
        </form>
    </div>

</div>
@endsection
