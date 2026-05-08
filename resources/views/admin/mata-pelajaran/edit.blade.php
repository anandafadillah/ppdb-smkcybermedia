@extends('layouts.app')

@section('title', 'Edit Mata Pelajaran')

@section('content')
<div class="max-w-lg space-y-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Edit Mata Pelajaran</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400">Perbarui nama mata pelajaran</p>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-theme-sm p-6">
        <form method="POST" action="{{ route('admin.mata-pelajaran.update', $mataPelajaran) }}" class="space-y-5">
            @csrf @method('PUT')
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    Nama Mata Pelajaran <span class="text-error-500">*</span>
                </label>
                <input type="text" name="nama" value="{{ old('nama', $mataPelajaran->nama) }}"
                    class="h-11 w-full rounded-lg border @error('nama') border-error-500 @else border-gray-300 dark:border-gray-700 @enderror bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white">
                @error('nama') <p class="mt-1 text-xs text-error-500">{{ $message }}</p> @enderror
            </div>
            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                    class="bg-brand-500 hover:bg-brand-600 text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition">
                    Perbarui
                </button>
                <a href="{{ route('admin.mata-pelajaran.index') }}"
                    class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
