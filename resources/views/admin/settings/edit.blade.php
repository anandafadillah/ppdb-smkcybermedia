@extends('layouts.app')

@section('title', 'Pengaturan Sekolah')

@section('content')
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">Pengaturan Sekolah</h2>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Informasi sekolah yang ditampilkan di seluruh aplikasi</p>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-theme-sm">
        <form method="POST"
              action="{{ auth()->user()->role === 'admin' ? route('admin.settings.update') : route('panitia.settings.update') }}"
              enctype="multipart/form-data"
              class="p-6 space-y-6">
            @csrf
            @method('PUT')

            {{-- Logo --}}
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Logo Sekolah
                </label>

                @if($settings['logo'])
                    <div class="mb-3">
                        <img src="{{ '/storage/' . $settings['logo'] }}"
                             alt="Logo sekolah"
                             class="h-20 w-auto rounded-lg border border-gray-200 dark:border-gray-700 object-contain bg-gray-50 dark:bg-gray-800 p-2">
                    </div>
                @endif

                <input type="file" name="logo" accept="image/jpeg,image/png"
                    class="block w-full text-sm text-gray-600 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-brand-700 hover:file:bg-brand-100 dark:text-gray-400 dark:file:bg-brand-500/10 dark:file:text-brand-400">
                <p class="mt-1 text-xs text-gray-400">Format JPG/PNG, maks. 2MB.
                    {{ $settings['logo'] ? 'Upload file baru untuk mengganti logo.' : '' }}
                </p>
                @error('logo')
                    <p class="mt-1 text-xs text-error-600">{{ $message }}</p>
                @enderror
            </div>

            <hr class="border-gray-100 dark:border-gray-800">

            {{-- Nama Sekolah --}}
            <div>
                <label for="nama_sekolah" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Nama Sekolah <span class="text-error-500">*</span>
                </label>
                <input type="text" id="nama_sekolah" name="nama_sekolah"
                    value="{{ old('nama_sekolah', $settings['nama_sekolah']) }}"
                    placeholder="contoh: SMK Cyber Media Jakarta"
                    class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white/90 @error('nama_sekolah') border-error-500 @enderror">
                @error('nama_sekolah')
                    <p class="mt-1 text-xs text-error-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Alamat --}}
            <div>
                <label for="alamat" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Alamat
                </label>
                <textarea id="alamat" name="alamat" rows="3"
                    placeholder="Jl. Contoh No. 1, Jakarta"
                    class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white/90 @error('alamat') border-error-500 @enderror">{{ old('alamat', $settings['alamat']) }}</textarea>
                @error('alamat')
                    <p class="mt-1 text-xs text-error-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Telepon & Email --}}
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <label for="telepon" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Telepon
                    </label>
                    <input type="text" id="telepon" name="telepon"
                        value="{{ old('telepon', $settings['telepon']) }}"
                        placeholder="021-xxxxxxx"
                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white/90 @error('telepon') border-error-500 @enderror">
                    @error('telepon')
                        <p class="mt-1 text-xs text-error-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Email
                    </label>
                    <input type="email" id="email" name="email"
                        value="{{ old('email', $settings['email']) }}"
                        placeholder="info@sekolah.sch.id"
                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white/90 @error('email') border-error-500 @enderror">
                    @error('email')
                        <p class="mt-1 text-xs text-error-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Deskripsi --}}
            <div>
                <label for="deskripsi" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Deskripsi
                </label>
                <textarea id="deskripsi" name="deskripsi" rows="4"
                    placeholder="Deskripsi singkat tentang sekolah..."
                    class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white/90 @error('deskripsi') border-error-500 @enderror">{{ old('deskripsi', $settings['deskripsi']) }}</textarea>
                @error('deskripsi')
                    <p class="mt-1 text-xs text-error-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Keterangan Formulir --}}
            <div>
                <label for="keterangan_formulir" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Keterangan / Ketentuan Formulir Pendaftaran
                </label>
                <p class="mb-2 text-xs text-gray-400 dark:text-gray-500">Teks ini tampil di bagian atas formulir pendaftaran sebagai panduan pengisian bagi peserta.</p>
                <textarea id="keterangan_formulir" name="keterangan_formulir" rows="4"
                    placeholder="Contoh: Setiap calon peserta didik wajib mengisi formulir ini dengan benar dan jujur..."
                    class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white/90">{{ old('keterangan_formulir', $settings['keterangan_formulir'] ?? '') }}</textarea>
            </div>

            {{-- Submit --}}
            <div class="flex justify-end pt-2">
                <button type="submit"
                    class="rounded-lg bg-brand-500 px-6 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2">
                    Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>
@endsection
