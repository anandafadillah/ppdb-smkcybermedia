@extends('layouts.app')

@section('title', 'Edit Peserta')

@section('content')
@php
    $prefix = request()->segment(1);
    $c = $config ?? null;
    $d = $peserta->dataDiri;
    $a = $peserta->dataAyah;
    $i = $peserta->dataIbu;
    $w = $peserta->dataWali;
    $al = $peserta->dataAlamat;
    $k = $peserta->dataKip;
@endphp

<div class="max-w-5xl space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route($prefix . '.peserta.show', $peserta) }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400">← Kembali ke Detail</a>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Edit Data Peserta</h2>
        </div>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-theme-sm p-5">
        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $d?->nama_lengkap ?? $peserta->user?->name ?? '-' }}</p>
        <p class="text-xs text-gray-400">NISN: {{ $peserta->user?->nisn ?? '-' }} · No. Pendaftaran: {{ $peserta->no_pendaftaran ?? '-' }}</p>
    </div>

    @if($errors->any())
    <div class="rounded-xl border border-error-200 bg-error-50 dark:border-error-800 dark:bg-error-500/10 p-4">
        <p class="text-sm font-semibold text-error-700 dark:text-error-400 mb-1">Periksa kembali isian berikut:</p>
        <ul class="text-xs text-error-600 dark:text-error-400 list-disc list-inside space-y-0.5">
            @foreach($errors->all() as $err) <li>{{ $err }}</li> @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route($prefix . '.peserta.update', $peserta) }}" class="space-y-6">
        @csrf @method('PUT')

        {{-- Inti: Jalur & Jurusan --}}
        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-theme-sm p-6 space-y-4">
            <h3 class="text-base font-semibold text-gray-800 dark:text-white">Pendaftaran</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Jalur Pendaftaran <span class="text-error-500">*</span></label>
                    <select name="jalur_id" class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 text-sm dark:text-white dark:bg-gray-900">
                        <option value="">-- Pilih Jalur --</option>
                        @foreach($jalur as $j)
                        <option value="{{ $j->id }}" {{ old('jalur_id', $peserta->jalur_id) == $j->id ? 'selected' : '' }}>{{ $j->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Jurusan <span class="text-error-500">*</span></label>
                    <select name="jurusan_id" class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 text-sm dark:text-white dark:bg-gray-900">
                        <option value="">-- Pilih Jurusan --</option>
                        @foreach($jurusan as $jr)
                        <option value="{{ $jr->id }}" {{ old('jurusan_id', $peserta->jurusan_id) == $jr->id ? 'selected' : '' }}>{{ $jr->nama }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Data Diri --}}
        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-theme-sm p-6 space-y-4">
            <h3 class="text-base font-semibold text-gray-800 dark:text-white">Data Diri</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nama Lengkap <span class="text-error-500">*</span></label>
                    <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $d?->nama_lengkap) }}"
                        class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 text-sm dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Jenis Kelamin <span class="text-error-500">*</span></label>
                    <select name="jenis_kelamin" class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 text-sm dark:text-white dark:bg-gray-900">
                        <option value="">-- Pilih --</option>
                        <option value="L" {{ old('jenis_kelamin', $d?->jenis_kelamin) === 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ old('jenis_kelamin', $d?->jenis_kelamin) === 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
                @if(!$c || $c->isFieldActive('diri_tempat_lahir'))
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir', $d?->tempat_lahir) }}"
                        class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 text-sm dark:text-white">
                </div>
                @endif
                @if(!$c || $c->isFieldActive('diri_tanggal_lahir'))
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', optional($d?->tanggal_lahir)->format('Y-m-d')) }}"
                        class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 text-sm dark:text-white">
                </div>
                @endif
                @if(!$c || $c->isFieldActive('diri_agama'))
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Agama</label>
                    <input type="text" name="agama" value="{{ old('agama', $d?->agama) }}"
                        class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 text-sm dark:text-white">
                </div>
                @endif
                @if(!$c || $c->isFieldActive('diri_no_hp'))
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">No. HP</label>
                    <input type="text" name="no_hp" value="{{ old('no_hp', $d?->no_hp) }}"
                        class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 text-sm dark:text-white">
                </div>
                @endif
                @if(!$c || $c->isFieldActive('diri_tinggi_badan'))
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Tinggi Badan (cm)</label>
                    <input type="number" name="tinggi_badan" value="{{ old('tinggi_badan', $d?->tinggi_badan) }}" min="50" max="250"
                        class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 text-sm dark:text-white">
                </div>
                @endif
                @if(!$c || $c->isFieldActive('diri_berat_badan'))
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Berat Badan (kg)</label>
                    <input type="number" name="berat_badan" value="{{ old('berat_badan', $d?->berat_badan) }}" min="10" max="200"
                        class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 text-sm dark:text-white">
                </div>
                @endif
                @if(!$c || $c->isFieldActive('diri_jumlah_saudara'))
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Jumlah Saudara</label>
                    <input type="number" name="jumlah_saudara" value="{{ old('jumlah_saudara', $d?->jumlah_saudara) }}" min="0" max="20"
                        class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 text-sm dark:text-white">
                </div>
                @endif
            </div>
            @if(!$c || $c->isFieldActive('diri_asal_sekolah'))
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Asal Sekolah</label>
                    <select name="asal_sekolah_id" class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 text-sm dark:text-white dark:bg-gray-900">
                        <option value="">-- Pilih (atau isi manual di samping) --</option>
                        @foreach($asalSekolah as $as)
                        <option value="{{ $as->id }}" {{ old('asal_sekolah_id', $d?->asal_sekolah_id) == $as->id ? 'selected' : '' }}>{{ $as->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Asal Sekolah (Lainnya)</label>
                    <input type="text" name="asal_sekolah_custom" value="{{ old('asal_sekolah_custom', $d?->asal_sekolah_custom) }}" placeholder="Tulis bila tidak ada di daftar"
                        class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 text-sm dark:text-white">
                </div>
            </div>
            @endif
        </div>

        {{-- Data Ayah --}}
        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-theme-sm p-6 space-y-4">
            <h3 class="text-base font-semibold text-gray-800 dark:text-white">Data Ayah</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nama Ayah</label>
                    <input type="text" name="nama_ayah" value="{{ old('nama_ayah', $a?->nama) }}"
                        class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 text-sm dark:text-white">
                </div>
                @if(!$c || $c->isFieldActive('ayah_nik'))
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">NIK Ayah</label>
                    <input type="text" name="nik_ayah" value="{{ old('nik_ayah', $a?->nik) }}" maxlength="16"
                        class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 text-sm dark:text-white">
                </div>
                @endif
                @if(!$c || $c->isFieldActive('ayah_tahun_lahir'))
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Tahun Lahir</label>
                    @php $vAyah = (int) old('tahun_lahir_ayah', $a?->tahun_lahir); @endphp
                    <select name="tahun_lahir_ayah" class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 text-sm dark:text-white dark:bg-gray-900">
                        <option value="">-- Pilih tahun --</option>
                        @for($y = date('Y'); $y >= 1940; $y--)
                            <option value="{{ $y }}" {{ $vAyah === $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                @endif
                @if(!$c || $c->isFieldActive('ayah_pendidikan'))
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Pendidikan</label>
                    <input type="text" name="pendidikan_ayah" value="{{ old('pendidikan_ayah', $a?->pendidikan) }}"
                        class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 text-sm dark:text-white">
                </div>
                @endif
                @if(!$c || $c->isFieldActive('ayah_pekerjaan'))
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Pekerjaan</label>
                    <input type="text" name="pekerjaan_ayah" value="{{ old('pekerjaan_ayah', $a?->pekerjaan) }}"
                        class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 text-sm dark:text-white">
                </div>
                @endif
                @if(!$c || $c->isFieldActive('ayah_penghasilan'))
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Penghasilan / Bulan</label>
                    <input type="text" name="penghasilan_ayah" value="{{ old('penghasilan_ayah', $a?->penghasilan) }}"
                        class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 text-sm dark:text-white">
                </div>
                @endif
            </div>
            @if(!$c || $c->isFieldActive('ayah_ketidakmampuan'))
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Ketidakmampuan Khusus</label>
                <input type="text" name="ketidakmampuan_khusus_ayah" value="{{ old('ketidakmampuan_khusus_ayah', $a?->ketidakmampuan_khusus) }}" placeholder="Kosongkan jika tidak ada"
                    class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 text-sm dark:text-white">
            </div>
            @endif
        </div>

        {{-- Data Ibu --}}
        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-theme-sm p-6 space-y-4">
            <h3 class="text-base font-semibold text-gray-800 dark:text-white">Data Ibu</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nama Ibu</label>
                    <input type="text" name="nama_ibu" value="{{ old('nama_ibu', $i?->nama) }}"
                        class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 text-sm dark:text-white">
                </div>
                @if(!$c || $c->isFieldActive('ibu_nik'))
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">NIK Ibu</label>
                    <input type="text" name="nik_ibu" value="{{ old('nik_ibu', $i?->nik) }}" maxlength="16"
                        class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 text-sm dark:text-white">
                </div>
                @endif
                @if(!$c || $c->isFieldActive('ibu_tahun_lahir'))
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Tahun Lahir</label>
                    @php $vIbu = (int) old('tahun_lahir_ibu', $i?->tahun_lahir); @endphp
                    <select name="tahun_lahir_ibu" class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 text-sm dark:text-white dark:bg-gray-900">
                        <option value="">-- Pilih tahun --</option>
                        @for($y = date('Y'); $y >= 1940; $y--)
                            <option value="{{ $y }}" {{ $vIbu === $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                @endif
                @if(!$c || $c->isFieldActive('ibu_pendidikan'))
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Pendidikan</label>
                    <input type="text" name="pendidikan_ibu" value="{{ old('pendidikan_ibu', $i?->pendidikan) }}"
                        class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 text-sm dark:text-white">
                </div>
                @endif
                @if(!$c || $c->isFieldActive('ibu_pekerjaan'))
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Pekerjaan</label>
                    <input type="text" name="pekerjaan_ibu" value="{{ old('pekerjaan_ibu', $i?->pekerjaan) }}"
                        class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 text-sm dark:text-white">
                </div>
                @endif
                @if(!$c || $c->isFieldActive('ibu_penghasilan'))
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Penghasilan / Bulan</label>
                    <input type="text" name="penghasilan_ibu" value="{{ old('penghasilan_ibu', $i?->penghasilan) }}"
                        class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 text-sm dark:text-white">
                </div>
                @endif
            </div>
            @if(!$c || $c->isFieldActive('ibu_ketidakmampuan'))
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Ketidakmampuan Khusus</label>
                <input type="text" name="ketidakmampuan_khusus_ibu" value="{{ old('ketidakmampuan_khusus_ibu', $i?->ketidakmampuan_khusus) }}" placeholder="Kosongkan jika tidak ada"
                    class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 text-sm dark:text-white">
            </div>
            @endif
        </div>

        {{-- Data Wali --}}
        @if(!$c || $c->isFieldActive('data_wali'))
        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-theme-sm p-6 space-y-4">
            <h3 class="text-base font-semibold text-gray-800 dark:text-white">Data Wali <span class="text-xs font-normal text-gray-400">(opsional)</span></h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nama Wali</label>
                    <input type="text" name="nama_wali" value="{{ old('nama_wali', $w?->nama) }}"
                        class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 text-sm dark:text-white">
                </div>
                @if(!$c || $c->isFieldActive('wali_nik'))
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">NIK Wali</label>
                    <input type="text" name="nik_wali" value="{{ old('nik_wali', $w?->nik) }}" maxlength="16"
                        class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 text-sm dark:text-white">
                </div>
                @endif
                @if(!$c || $c->isFieldActive('wali_tahun_lahir'))
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Tahun Lahir</label>
                    @php $vWali = (int) old('tahun_lahir_wali', $w?->tahun_lahir); @endphp
                    <select name="tahun_lahir_wali" class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 text-sm dark:text-white dark:bg-gray-900">
                        <option value="">-- Pilih tahun --</option>
                        @for($y = date('Y'); $y >= 1940; $y--)
                            <option value="{{ $y }}" {{ $vWali === $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                @endif
                @if(!$c || $c->isFieldActive('wali_pekerjaan'))
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Pekerjaan</label>
                    <input type="text" name="pekerjaan_wali" value="{{ old('pekerjaan_wali', $w?->pekerjaan) }}"
                        class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 text-sm dark:text-white">
                </div>
                @endif
                @if(!$c || $c->isFieldActive('wali_penghasilan'))
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Penghasilan / Bulan</label>
                    <input type="text" name="penghasilan_wali" value="{{ old('penghasilan_wali', $w?->penghasilan) }}"
                        class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 text-sm dark:text-white">
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- Alamat --}}
        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-theme-sm p-6 space-y-4">
            <h3 class="text-base font-semibold text-gray-800 dark:text-white">Alamat Tempat Tinggal</h3>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">RT</label>
                    <input type="text" name="rt" value="{{ old('rt', $al?->rt) }}" maxlength="3"
                        class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 text-sm dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">RW</label>
                    <input type="text" name="rw" value="{{ old('rw', $al?->rw) }}" maxlength="3"
                        class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 text-sm dark:text-white">
                </div>
                @if(!$c || $c->isFieldActive('alamat_jarak'))
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Jarak (km)</label>
                    <input type="number" name="jarak_tempat_tinggal" value="{{ old('jarak_tempat_tinggal', $al?->jarak_tempat_tinggal) }}" min="0"
                        class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 text-sm dark:text-white">
                </div>
                @endif
            </div>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Kelurahan</label>
                    <input type="text" name="kelurahan" value="{{ old('kelurahan', $al?->kelurahan) }}"
                        class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 text-sm dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Kecamatan</label>
                    <input type="text" name="kecamatan" value="{{ old('kecamatan', $al?->kecamatan) }}"
                        class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 text-sm dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Kota / Kabupaten</label>
                    <input type="text" name="kota" value="{{ old('kota', $al?->kota) }}"
                        class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 text-sm dark:text-white">
                </div>
            </div>
            @if(!$c || $c->isFieldActive('alamat_koordinat'))
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Latitude</label>
                    <input type="text" name="latitude" value="{{ old('latitude', $al?->latitude) }}" placeholder="-6.200000"
                        class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 text-sm dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Longitude</label>
                    <input type="text" name="longitude" value="{{ old('longitude', $al?->longitude) }}" placeholder="106.816666"
                        class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 text-sm dark:text-white">
                </div>
            </div>
            @endif
        </div>

        {{-- Data KIP/PKH (hanya Afirmasi) --}}
        @if($peserta->jalur?->isAfirmasi() && (!$c || $c->isFieldActive('data_kip')))
        <div class="rounded-xl border border-warning-200 bg-warning-50 dark:border-warning-800 dark:bg-warning-500/10 shadow-theme-sm p-6 space-y-4">
            <h3 class="text-base font-semibold text-warning-800 dark:text-warning-300">Data KIP / PKH</h3>
            <p class="text-xs text-warning-600 dark:text-warning-400">Diisi khusus untuk jalur Afirmasi</p>
            <div class="grid grid-cols-2 gap-4">
                @if(!$c || $c->isFieldActive('kip_no_kip'))
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">No. KIP</label>
                    <input type="text" name="no_kip" value="{{ old('no_kip', $k?->no_kip) }}"
                        class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 text-sm dark:text-white">
                </div>
                @endif
                @if(!$c || $c->isFieldActive('kip_no_kps_pkh'))
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">No. KPS/PKH</label>
                    <input type="text" name="no_kps_pkh" value="{{ old('no_kps_pkh', $k?->no_kps_pkh) }}"
                        class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 text-sm dark:text-white">
                </div>
                @endif
                @if(!$c || $c->isFieldActive('kip_nama_di_kip'))
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nama di KIP</label>
                    <input type="text" name="nama_di_kip" value="{{ old('nama_di_kip', $k?->nama_di_kip) }}"
                        class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 text-sm dark:text-white">
                </div>
                @endif
                @if(!$c || $c->isFieldActive('kip_terima'))
                <div class="col-span-2">
                    <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                        <input type="hidden" name="terima_kip" value="0">
                        <input type="checkbox" name="terima_kip" value="1" {{ old('terima_kip', $k?->terima_kip) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-brand-500 focus:ring-brand-500/20">
                        <span>Penerima KIP</span>
                    </label>
                </div>
                @endif
            </div>
        </div>
        @endif

        <div class="pt-2 flex gap-3">
            <button type="submit" class="bg-brand-500 hover:bg-brand-600 text-white text-sm font-semibold px-6 py-2.5 rounded-lg transition">
                Simpan Perubahan
            </button>
            <a href="{{ route($prefix . '.peserta.show', $peserta) }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 flex items-center">Batal</a>
        </div>
    </form>
</div>
@endsection
