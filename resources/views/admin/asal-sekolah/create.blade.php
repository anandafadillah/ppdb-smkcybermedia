@extends('layouts.app')

@section('title', 'Tambah Asal Sekolah')

@section('content')
<div class="max-w-2xl space-y-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Tambah Asal Sekolah</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400">Isi data sekolah asal peserta</p>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-theme-sm p-6">
        <form method="POST" action="{{ route('admin.asal-sekolah.store') }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">NPSN <span class="text-error-500">*</span></label>
                <input type="text" name="npsn" value="{{ old('npsn') }}" maxlength="8"
                    placeholder="8 digit NPSN"
                    class="h-11 w-full rounded-lg border @error('npsn') border-error-500 @else border-gray-300 dark:border-gray-700 @enderror bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white">
                @error('npsn') <p class="mt-1 text-xs text-error-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nama Sekolah <span class="text-error-500">*</span></label>
                <input type="text" name="nama" value="{{ old('nama') }}"
                    placeholder="Nama lengkap sekolah"
                    class="h-11 w-full rounded-lg border @error('nama') border-error-500 @else border-gray-300 dark:border-gray-700 @enderror bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white">
                @error('nama') <p class="mt-1 text-xs text-error-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Status <span class="text-error-500">*</span></label>
                <select name="status"
                    class="h-11 w-full rounded-lg border @error('status') border-error-500 @else border-gray-300 dark:border-gray-700 @enderror bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white">
                    <option value="">-- Pilih Status --</option>
                    <option value="negeri" {{ old('status') === 'negeri' ? 'selected' : '' }}>Negeri</option>
                    <option value="swasta" {{ old('status') === 'swasta' ? 'selected' : '' }}>Swasta</option>
                </select>
                @error('status') <p class="mt-1 text-xs text-error-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Alamat</label>
                <textarea name="alamat" rows="3"
                    placeholder="Alamat lengkap sekolah"
                    class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white">{{ old('alamat') }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Kelurahan</label>
                    <input type="text" name="kelurahan" value="{{ old('kelurahan') }}"
                        class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Kecamatan</label>
                    <input type="text" name="kecamatan" value="{{ old('kecamatan') }}"
                        class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white">
                </div>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                    class="bg-brand-500 hover:bg-brand-600 text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition">
                    Simpan
                </button>
                <a href="{{ route('admin.asal-sekolah.index') }}"
                    class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                    Batal
                </a>
            </div>
        </form>
    </div>

    {{-- Import dari Excel --}}
    <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-theme-sm p-6">
        <h3 class="text-base font-semibold text-gray-800 dark:text-white mb-1">Import dari Excel</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
            File Excel harus memiliki kolom: <code class="bg-gray-100 dark:bg-gray-800 px-1 rounded">NPSN</code>,
            <code class="bg-gray-100 dark:bg-gray-800 px-1 rounded">Nama Sekolah</code>,
            <code class="bg-gray-100 dark:bg-gray-800 px-1 rounded">Status</code>,
            <code class="bg-gray-100 dark:bg-gray-800 px-1 rounded">Alamat</code>,
            <code class="bg-gray-100 dark:bg-gray-800 px-1 rounded">Kelurahan</code>,
            <code class="bg-gray-100 dark:bg-gray-800 px-1 rounded">Kecamatan</code>
        </p>
        <form method="POST" action="{{ route('admin.asal-sekolah.import') }}" enctype="multipart/form-data" class="flex items-center gap-3">
            @csrf
            <input type="file" name="file" accept=".xlsx,.xls,.csv"
                class="text-sm text-gray-600 dark:text-gray-300 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-brand-700 hover:file:bg-brand-100">
            @error('file') <p class="text-xs text-error-500">{{ $message }}</p> @enderror
            <button type="submit"
                class="bg-brand-500 hover:bg-brand-600 text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition">
                Import
            </button>
        </form>
    </div>
</div>
@endsection
