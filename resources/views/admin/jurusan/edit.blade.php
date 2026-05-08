@extends('layouts.app')

@section('title', 'Edit Jurusan — ' . $jurusan->kode)

@section('content')
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">Edit Jurusan — {{ $jurusan->kode }}</h2>
    </div>

    <div class="max-w-lg rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-theme-sm p-6">
        <form method="POST" action="{{ route('admin.jurusan.update', $jurusan) }}">
            @csrf @method('PUT')
            <div class="space-y-4">

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Kode Jurusan <span class="text-error-500">*</span>
                    </label>
                    <input type="text" name="kode" value="{{ old('kode', $jurusan->kode) }}" maxlength="10"
                        class="h-11 w-full rounded-lg border bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:text-white/90 dark:bg-gray-900 uppercase focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden
                        @error('kode') border-error-400 @else border-gray-300 dark:border-gray-700 @enderror">
                    @error('kode') <p class="mt-1 text-xs text-error-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Nama Jurusan <span class="text-error-500">*</span>
                    </label>
                    <input type="text" name="nama" value="{{ old('nama', $jurusan->nama) }}"
                        class="h-11 w-full rounded-lg border bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:text-white/90 dark:bg-gray-900 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden
                        @error('nama') border-error-400 @else border-gray-300 dark:border-gray-700 @enderror">
                    @error('nama') <p class="mt-1 text-xs text-error-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Kapasitas</label>
                    <input type="number" name="kapasitas" value="{{ old('kapasitas', $jurusan->kapasitas) }}" min="1"
                        class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:text-white/90 dark:bg-gray-900 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">
                    @error('kapasitas') <p class="mt-1 text-xs text-error-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Deskripsi</label>
                    <textarea name="deskripsi" rows="3"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:text-white/90 dark:bg-gray-900 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden">{{ old('deskripsi', $jurusan->deskripsi) }}</textarea>
                    @error('deskripsi') <p class="mt-1 text-xs text-error-500">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mt-6 flex items-center gap-3">
                <button type="submit"
                    class="bg-brand-500 hover:bg-brand-600 text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition">
                    Perbarui
                </button>
                <a href="{{ route('admin.jurusan.index') }}"
                    class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 py-2.5">Batal</a>
            </div>
        </form>
    </div>
@endsection
