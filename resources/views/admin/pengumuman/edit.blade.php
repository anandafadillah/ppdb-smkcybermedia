@extends('layouts.app')

@section('title', 'Edit Pengumuman')

@section('content')
<div class="max-w-2xl space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.pengumuman.index') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400">← Kembali</a>
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Edit Pengumuman</h2>
    </div>

    @if(session('success'))
    <div class="rounded-xl border border-success-200 bg-success-50 dark:border-success-800 dark:bg-success-500/10 p-4">
        <p class="text-sm text-success-700 dark:text-success-400">{{ session('success') }}</p>
    </div>
    @endif

    <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-theme-sm p-6">
        <form method="POST" action="{{ route('admin.pengumuman.update', $pengumuman) }}" class="space-y-4">
            @csrf @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Judul</label>
                <input type="text" name="judul" value="{{ old('judul', $pengumuman->judul) }}"
                    class="h-10 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-3 text-sm dark:text-white dark:bg-gray-900">
                @error('judul') <p class="mt-1 text-xs text-error-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Isi</label>
                <textarea name="isi" rows="8"
                    class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-3 py-2 text-sm dark:text-white dark:bg-gray-900">{{ old('isi', $pengumuman->isi) }}</textarea>
                @error('isi') <p class="mt-1 text-xs text-error-500">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                    <select name="status"
                        class="h-10 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-3 text-sm dark:text-white dark:bg-gray-900">
                        @foreach(['draft' => 'Draft', 'published' => 'Published'] as $val => $label)
                        <option value="{{ $val }}" {{ old('status', $pengumuman->status) === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('status') <p class="mt-1 text-xs text-error-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tanggal Publish</label>
                    <input type="date" name="tanggal_publish"
                        value="{{ old('tanggal_publish', $pengumuman->tanggal_publish?->format('Y-m-d')) }}"
                        class="h-10 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-3 text-sm dark:text-white dark:bg-gray-900">
                    @error('tanggal_publish') <p class="mt-1 text-xs text-error-500">{{ $message }}</p> @enderror
                </div>
            </div>

            <button type="submit"
                class="w-full bg-brand-500 hover:bg-brand-600 text-white text-sm font-semibold py-2.5 rounded-lg transition">
                Simpan Perubahan
            </button>
        </form>
    </div>
</div>
@endsection
