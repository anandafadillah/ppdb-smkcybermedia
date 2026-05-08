@extends('layouts.app')

@section('title', 'Data Keluarga & Alamat')

@section('content')
<div class="max-w-3xl space-y-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Data Keluarga & Alamat</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400">Isi data orang tua/wali dan alamat tempat tinggal</p>
    </div>

    @if(session('success'))
    <div class="rounded-xl border border-success-200 bg-success-50 dark:border-success-800 dark:bg-success-500/10 p-4">
        <p class="text-sm text-success-700 dark:text-success-400">{{ session('success') }}</p>
    </div>
    @endif

    @php $c = $config ?? null; @endphp

    <form method="POST" action="{{ route('peserta.keluarga.store') }}" class="space-y-6">
        @csrf

        {{-- Data Ayah --}}
        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-theme-sm p-6 space-y-4">
            <h3 class="text-base font-semibold text-gray-800 dark:text-white">Data Ayah</h3>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nama Ayah</label>
                    <input type="text" name="nama_ayah" value="{{ old('nama_ayah', $peserta?->dataAyah?->nama) }}"
                        class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white">
                </div>
                @if(!$c || $c->isFieldActive('ayah_nik'))
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">NIK Ayah</label>
                    <input type="text" name="nik_ayah" value="{{ old('nik_ayah', $peserta?->dataAyah?->nik) }}" maxlength="16"
                        class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white">
                </div>
                @endif
                @if(!$c || $c->isFieldActive('ayah_tahun_lahir'))
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Tahun Lahir</label>
                    @php $valAyah = (int) old('tahun_lahir_ayah', $peserta?->dataAyah?->tahun_lahir); @endphp
                    <select name="tahun_lahir_ayah"
                        class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white dark:bg-gray-900">
                        <option value="">-- Pilih tahun --</option>
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
                        class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white">
                </div>
                @endif
                @if(!$c || $c->isFieldActive('ayah_pekerjaan'))
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Pekerjaan</label>
                    <input type="text" name="pekerjaan_ayah" value="{{ old('pekerjaan_ayah', $peserta?->dataAyah?->pekerjaan) }}"
                        class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white">
                </div>
                @endif
                @if(!$c || $c->isFieldActive('ayah_penghasilan'))
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Penghasilan / Bulan</label>
                    <input type="text" name="penghasilan_ayah" value="{{ old('penghasilan_ayah', $peserta?->dataAyah?->penghasilan) }}"
                        class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white">
                </div>
                @endif
            </div>
            @if(!$c || $c->isFieldActive('ayah_ketidakmampuan'))
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Ketidakmampuan Khusus</label>
                <input type="text" name="ketidakmampuan_khusus_ayah" value="{{ old('ketidakmampuan_khusus_ayah', $peserta?->dataAyah?->ketidakmampuan_khusus) }}"
                    placeholder="Kosongkan jika tidak ada"
                    class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white">
            </div>
            @endif
        </div>

        {{-- Data Ibu --}}
        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-theme-sm p-6 space-y-4">
            <h3 class="text-base font-semibold text-gray-800 dark:text-white">Data Ibu</h3>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nama Ibu</label>
                    <input type="text" name="nama_ibu" value="{{ old('nama_ibu', $peserta?->dataIbu?->nama) }}"
                        class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white">
                </div>
                @if(!$c || $c->isFieldActive('ibu_nik'))
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">NIK Ibu</label>
                    <input type="text" name="nik_ibu" value="{{ old('nik_ibu', $peserta?->dataIbu?->nik) }}" maxlength="16"
                        class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white">
                </div>
                @endif
                @if(!$c || $c->isFieldActive('ibu_tahun_lahir'))
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Tahun Lahir</label>
                    @php $valIbu = (int) old('tahun_lahir_ibu', $peserta?->dataIbu?->tahun_lahir); @endphp
                    <select name="tahun_lahir_ibu"
                        class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white dark:bg-gray-900">
                        <option value="">-- Pilih tahun --</option>
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
                        class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white">
                </div>
                @endif
                @if(!$c || $c->isFieldActive('ibu_pekerjaan'))
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Pekerjaan</label>
                    <input type="text" name="pekerjaan_ibu" value="{{ old('pekerjaan_ibu', $peserta?->dataIbu?->pekerjaan) }}"
                        class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white">
                </div>
                @endif
                @if(!$c || $c->isFieldActive('ibu_penghasilan'))
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Penghasilan / Bulan</label>
                    <input type="text" name="penghasilan_ibu" value="{{ old('penghasilan_ibu', $peserta?->dataIbu?->penghasilan) }}"
                        class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white">
                </div>
                @endif
            </div>
            @if(!$c || $c->isFieldActive('ibu_ketidakmampuan'))
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Ketidakmampuan Khusus</label>
                <input type="text" name="ketidakmampuan_khusus_ibu" value="{{ old('ketidakmampuan_khusus_ibu', $peserta?->dataIbu?->ketidakmampuan_khusus) }}"
                    placeholder="Kosongkan jika tidak ada"
                    class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white">
            </div>
            @endif
        </div>

        {{-- Data Wali (opsional) --}}
        @if(!$c || $c->isFieldActive('data_wali'))
        <div x-data="{ open: {{ $peserta?->dataWali ? 'true' : 'false' }} }"
             class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-theme-sm p-6 space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-semibold text-gray-800 dark:text-white">Data Wali <span class="text-xs font-normal text-gray-400">(opsional)</span></h3>
                <button type="button" @click="open = !open"
                    class="text-sm text-brand-600 hover:text-brand-700 dark:text-brand-400">
                    <span x-text="open ? 'Sembunyikan' : 'Tambah Data Wali'"></span>
                </button>
            </div>
            <div x-show="open" x-transition class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nama Wali</label>
                    <input type="text" name="nama_wali" value="{{ old('nama_wali', $peserta?->dataWali?->nama) }}"
                        class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white">
                </div>
                @if(!$c || $c->isFieldActive('wali_nik'))
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">NIK Wali</label>
                    <input type="text" name="nik_wali" value="{{ old('nik_wali', $peserta?->dataWali?->nik) }}" maxlength="16"
                        class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white">
                </div>
                @endif
                @if(!$c || $c->isFieldActive('wali_tahun_lahir'))
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Tahun Lahir</label>
                    @php $valWali = (int) old('tahun_lahir_wali', $peserta?->dataWali?->tahun_lahir); @endphp
                    <select name="tahun_lahir_wali"
                        class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white dark:bg-gray-900">
                        <option value="">-- Pilih tahun --</option>
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
                        class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white">
                </div>
                @endif
                @if(!$c || $c->isFieldActive('wali_penghasilan'))
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Penghasilan / Bulan</label>
                    <input type="text" name="penghasilan_wali" value="{{ old('penghasilan_wali', $peserta?->dataWali?->penghasilan) }}"
                        class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white">
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- Data Alamat --}}
        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-theme-sm p-6 space-y-4">
            <h3 class="text-base font-semibold text-gray-800 dark:text-white">Alamat Tempat Tinggal</h3>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">RT</label>
                    <input type="text" name="rt" value="{{ old('rt', $peserta?->dataAlamat?->rt) }}" maxlength="3"
                        class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">RW</label>
                    <input type="text" name="rw" value="{{ old('rw', $peserta?->dataAlamat?->rw) }}" maxlength="3"
                        class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white">
                </div>
                @if(!$c || $c->isFieldActive('alamat_jarak'))
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Jarak (km)</label>
                    <input type="number" name="jarak_tempat_tinggal" value="{{ old('jarak_tempat_tinggal', $peserta?->dataAlamat?->jarak_tempat_tinggal) }}" min="0"
                        class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white">
                </div>
                @endif
            </div>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Kelurahan</label>
                    <input type="text" name="kelurahan" value="{{ old('kelurahan', $peserta?->dataAlamat?->kelurahan) }}"
                        class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Kecamatan</label>
                    <input type="text" name="kecamatan" value="{{ old('kecamatan', $peserta?->dataAlamat?->kecamatan) }}"
                        class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Kota / Kabupaten</label>
                    <input type="text" name="kota" value="{{ old('kota', $peserta?->dataAlamat?->kota) }}"
                        class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white">
                </div>
            </div>
            @if(!$c || $c->isFieldActive('alamat_koordinat'))
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Latitude</label>
                    <input type="text" name="latitude" value="{{ old('latitude', $peserta?->dataAlamat?->latitude) }}" placeholder="-6.200000"
                        class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Longitude</label>
                    <input type="text" name="longitude" value="{{ old('longitude', $peserta?->dataAlamat?->longitude) }}" placeholder="106.816666"
                        class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white">
                </div>
            </div>
            @endif
        </div>

        {{-- Data KIP/PKH (hanya Afirmasi + section aktif di FormConfig) --}}
        @if($peserta?->jalur?->isAfirmasi() && (!$c || $c->isFieldActive('data_kip')))
        <div class="rounded-xl border border-warning-200 bg-warning-50 dark:border-warning-800 dark:bg-warning-500/10 shadow-theme-sm p-6 space-y-4">
            <h3 class="text-base font-semibold text-warning-800 dark:text-warning-300">Data KIP / PKH</h3>
            <p class="text-xs text-warning-600 dark:text-warning-400">Diisi khusus untuk jalur Afirmasi</p>

            <div class="grid grid-cols-2 gap-4">
                @if(!$c || $c->isFieldActive('kip_no_kip'))
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">No. KIP</label>
                    <input type="text" name="no_kip" value="{{ old('no_kip', $peserta?->dataKip?->no_kip) }}"
                        class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white">
                </div>
                @endif
                @if(!$c || $c->isFieldActive('kip_no_kps_pkh'))
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">No. KPS/PKH</label>
                    <input type="text" name="no_kps_pkh" value="{{ old('no_kps_pkh', $peserta?->dataKip?->no_kps_pkh) }}"
                        class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white">
                </div>
                @endif
                @if(!$c || $c->isFieldActive('kip_nama_di_kip'))
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nama di KIP</label>
                    <input type="text" name="nama_di_kip" value="{{ old('nama_di_kip', $peserta?->dataKip?->nama_di_kip) }}"
                        class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white">
                </div>
                @endif
                @if(!$c || $c->isFieldActive('kip_terima'))
                <div class="col-span-2">
                    <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                        <input type="hidden" name="terima_kip" value="0">
                        <input type="checkbox" name="terima_kip" value="1" {{ old('terima_kip', $peserta?->dataKip?->terima_kip) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-brand-500 focus:ring-brand-500/20">
                        <span>Penerima KIP</span>
                    </label>
                </div>
                @endif
            </div>
        </div>
        @endif

        <div class="pt-2">
            <button type="submit"
                class="bg-brand-500 hover:bg-brand-600 text-white text-sm font-semibold px-6 py-2.5 rounded-lg transition">
                Simpan Data Keluarga
            </button>
        </div>
    </form>
</div>
@endsection
