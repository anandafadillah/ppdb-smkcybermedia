@extends('layouts.app')

@section('title', 'Edit Asal Sekolah')

@section('content')
<div class="max-w-2xl space-y-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Edit Asal Sekolah</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400">Perbarui data sekolah</p>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-theme-sm p-6">
        <form method="POST" action="{{ route('admin.asal-sekolah.update', $asalSekolah) }}" class="space-y-5">
            @csrf @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">NPSN <span class="text-error-500">*</span></label>
                <input type="text" name="npsn" value="{{ old('npsn', $asalSekolah->npsn) }}" maxlength="8"
                    class="h-11 w-full rounded-lg border @error('npsn') border-error-500 @else border-gray-300 dark:border-gray-700 @enderror bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white">
                @error('npsn') <p class="mt-1 text-xs text-error-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nama Sekolah <span class="text-error-500">*</span></label>
                <input type="text" name="nama" value="{{ old('nama', $asalSekolah->nama) }}"
                    class="h-11 w-full rounded-lg border @error('nama') border-error-500 @else border-gray-300 dark:border-gray-700 @enderror bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white">
                @error('nama') <p class="mt-1 text-xs text-error-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Status <span class="text-error-500">*</span></label>
                <select name="status"
                    class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white">
                    <option value="negeri" {{ old('status', $asalSekolah->status) === 'negeri' ? 'selected' : '' }}>Negeri</option>
                    <option value="swasta" {{ old('status', $asalSekolah->status) === 'swasta' ? 'selected' : '' }}>Swasta</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Alamat</label>
                <textarea name="alamat" rows="3"
                    class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white">{{ old('alamat', $asalSekolah->alamat) }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Kelurahan</label>
                    <input type="text" name="kelurahan" value="{{ old('kelurahan', $asalSekolah->kelurahan) }}"
                        class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Kecamatan</label>
                    <input type="text" name="kecamatan" value="{{ old('kecamatan', $asalSekolah->kecamatan) }}"
                        class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white">
                </div>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                    class="bg-brand-500 hover:bg-brand-600 text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition">
                    Perbarui
                </button>
                <a href="{{ route('admin.asal-sekolah.index') }}"
                    class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
