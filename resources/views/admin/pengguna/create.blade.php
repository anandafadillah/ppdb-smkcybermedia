@extends('layouts.app')

@section('title', 'Tambah Pengguna')

@section('content')
<div class="max-w-xl space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.pengguna.index') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400">← Kembali</a>
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Tambah Pengguna</h2>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-theme-sm p-6 space-y-4">
        <form method="POST" action="{{ route('admin.pengguna.store') }}" x-data="{ role: '{{ old('role', 'peserta') }}' }">
            @csrf

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                        class="h-10 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-3 text-sm dark:text-white dark:bg-gray-900">
                    @error('name') <p class="mt-1 text-xs text-error-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Role</label>
                    <select name="role" x-model="role"
                        class="h-10 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-3 text-sm dark:text-white dark:bg-gray-900">
                        <option value="peserta">Peserta</option>
                        <option value="panitia">Panitia</option>
                        <option value="admin">Admin</option>
                    </select>
                    @error('role') <p class="mt-1 text-xs text-error-500">{{ $message }}</p> @enderror
                </div>

                <div x-show="role !== 'peserta'">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        class="h-10 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-3 text-sm dark:text-white dark:bg-gray-900">
                    @error('email') <p class="mt-1 text-xs text-error-500">{{ $message }}</p> @enderror
                </div>

                <div x-show="role === 'peserta'">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">NISN</label>
                    <input type="text" name="nisn" value="{{ old('nisn') }}" maxlength="10"
                        class="h-10 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-3 text-sm dark:text-white dark:bg-gray-900">
                    @error('nisn') <p class="mt-1 text-xs text-error-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Password</label>
                    <input type="password" name="password"
                        class="h-10 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-3 text-sm dark:text-white dark:bg-gray-900">
                    @error('password') <p class="mt-1 text-xs text-error-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation"
                        class="h-10 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-3 text-sm dark:text-white dark:bg-gray-900">
                </div>

                <button type="submit"
                    class="w-full bg-brand-500 hover:bg-brand-600 text-white text-sm font-semibold py-2.5 rounded-lg transition">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
