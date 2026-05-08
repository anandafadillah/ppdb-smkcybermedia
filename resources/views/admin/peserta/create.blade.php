@extends('layouts.app')

@section('title', 'Tambah Peserta Manual')

@section('content')
<div class="max-w-lg space-y-6">
    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Tambah Peserta Manual (Luring)</h2>

    <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-theme-sm p-6">
        <form method="POST" action="{{ route(request()->segment(1) . '.peserta.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nama Lengkap <span class="text-error-500">*</span></label>
                <input type="text" name="nama" value="{{ old('nama') }}"
                    class="h-11 w-full rounded-lg border @error('nama') border-error-500 @else border-gray-300 dark:border-gray-700 @enderror bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:outline-hidden dark:text-white">
                @error('nama') <p class="mt-1 text-xs text-error-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">NISN <span class="text-error-500">*</span></label>
                <input type="text" name="nisn" value="{{ old('nisn') }}" maxlength="10"
                    class="h-11 w-full rounded-lg border @error('nisn') border-error-500 @else border-gray-300 dark:border-gray-700 @enderror bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:outline-hidden dark:text-white">
                @error('nisn') <p class="mt-1 text-xs text-error-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Password <span class="text-error-500">*</span></label>
                <input type="password" name="password"
                    class="h-11 w-full rounded-lg border @error('password') border-error-500 @else border-gray-300 dark:border-gray-700 @enderror bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:outline-hidden dark:text-white">
                @error('password') <p class="mt-1 text-xs text-error-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Jalur Pendaftaran <span class="text-error-500">*</span></label>
                <select name="jalur_id"
                    class="h-11 w-full rounded-lg border @error('jalur_id') border-error-500 @else border-gray-300 dark:border-gray-700 @enderror bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:outline-hidden dark:text-white dark:bg-gray-900">
                    <option value="">-- Pilih Jalur --</option>
                    @foreach($jalur as $j)
                    <option value="{{ $j->id }}" {{ old('jalur_id') == $j->id ? 'selected' : '' }}>{{ $j->nama }}</option>
                    @endforeach
                </select>
                @error('jalur_id') <p class="mt-1 text-xs text-error-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Jurusan <span class="text-error-500">*</span></label>
                <select name="jurusan_id"
                    class="h-11 w-full rounded-lg border @error('jurusan_id') border-error-500 @else border-gray-300 dark:border-gray-700 @enderror bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:outline-hidden dark:text-white dark:bg-gray-900">
                    <option value="">-- Pilih Jurusan --</option>
                    @foreach($jurusan as $j)
                    <option value="{{ $j->id }}" {{ old('jurusan_id') == $j->id ? 'selected' : '' }}>{{ $j->nama }}</option>
                    @endforeach
                </select>
                @error('jurusan_id') <p class="mt-1 text-xs text-error-500">{{ $message }}</p> @enderror
            </div>

            <div class="pt-2 flex gap-3">
                <button type="submit"
                    class="bg-brand-500 hover:bg-brand-600 text-white text-sm font-semibold px-6 py-2.5 rounded-lg transition">
                    Simpan & Generate No. Pendaftaran
                </button>
                <a href="{{ route(request()->segment(1) . '.peserta.index') }}"
                    class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 flex items-center">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
