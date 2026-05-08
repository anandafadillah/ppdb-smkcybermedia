@extends('layouts.app')

@section('title', 'Edit Tahun Penerimaan')

@section('content')
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">Edit Tahun Penerimaan</h2>
    </div>

    <div class="max-w-lg rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-theme-sm p-6">
        <form method="POST" action="{{ route('admin.tahun-penerimaan.update', $tahunPenerimaan) }}" class="space-y-4">
            @csrf @method('PUT')

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    Tahun <span class="text-error-500">*</span>
                </label>
                <input type="text" name="tahun" value="{{ old('tahun', $tahunPenerimaan->tahun) }}"
                    class="h-11 w-full rounded-lg border bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:text-white/90 dark:bg-gray-900 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden
                    @error('tahun') border-error-400 @else border-gray-300 dark:border-gray-700 @enderror">
                @error('tahun') <p class="mt-1 text-xs text-error-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    Label <span class="text-error-500">*</span>
                </label>
                <input type="text" name="label" value="{{ old('label', $tahunPenerimaan->label) }}"
                    class="h-11 w-full rounded-lg border bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:text-white/90 dark:bg-gray-900 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden
                    @error('label') border-error-400 @else border-gray-300 dark:border-gray-700 @enderror">
                @error('label') <p class="mt-1 text-xs text-error-500">{{ $message }}</p> @enderror
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                    class="bg-brand-500 hover:bg-brand-600 text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition">
                    Perbarui
                </button>
                <a href="{{ route('admin.tahun-penerimaan.index') }}"
                    class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 py-2.5">Batal</a>
            </div>
        </form>
    </div>
@endsection
