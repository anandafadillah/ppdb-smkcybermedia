@extends('layouts.app')

@section('title', 'Tambah Jalur Pendaftaran')

@section('content')
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">Tambah Jalur Pendaftaran</h2>
    </div>

    <div class="max-w-xl rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-theme-sm p-6">
        <form method="POST" action="{{ route('admin.jalur-pendaftaran.store') }}">
            @csrf

            <div class="mb-4">
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    Tahun Penerimaan <span class="text-error-500">*</span>
                </label>
                <select name="tahun_penerimaan_id" required
                    class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:text-white/90 dark:bg-gray-900 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                    @foreach ($tahunList as $tahun)
                        <option value="{{ $tahun->id }}" {{ old('tahun_penerimaan_id') == $tahun->id ? 'selected' : '' }}>
                            {{ $tahun->label }}
                        </option>
                    @endforeach
                </select>
                @error('tahun_penerimaan_id') <p class="mt-1 text-xs text-error-500">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    Nama Jalur <span class="text-error-500">*</span>
                </label>
                <input type="text" name="nama" value="{{ old('nama') }}" required
                    placeholder="cth: Afirmasi"
                    class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:text-white/90 dark:bg-gray-900 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                @error('nama') <p class="mt-1 text-xs text-error-500">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Deskripsi</label>
                <textarea name="deskripsi" rows="3"
                    class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:text-white/90 dark:bg-gray-900 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">{{ old('deskripsi') }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Kode Awal Daring</label>
                    <input type="text" name="kode_awal_daring" value="{{ old('kode_awal_daring') }}"
                        placeholder="cth: AFR-D"
                        class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:text-white/90 dark:bg-gray-900 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Kode Awal Luring</label>
                    <input type="text" name="kode_awal_luring" value="{{ old('kode_awal_luring') }}"
                        placeholder="cth: AFR-L"
                        class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:text-white/90 dark:bg-gray-900 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit"
                    class="bg-brand-500 hover:bg-brand-600 text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition">
                    Simpan
                </button>
                <a href="{{ route('admin.jalur-pendaftaran.index') }}"
                    class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 py-2.5">Batal</a>
            </div>
        </form>
    </div>
@endsection
