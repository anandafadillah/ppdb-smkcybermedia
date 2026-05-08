@extends('layouts.app')

@section('title', 'Edit Pengguna')

@section('content')
<div class="max-w-xl space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.pengguna.index') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400">← Kembali</a>
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Edit Pengguna</h2>
    </div>

    @if(session('success'))
    <div class="rounded-xl border border-success-200 bg-success-50 dark:border-success-800 dark:bg-success-500/10 p-4">
        <p class="text-sm text-success-700 dark:text-success-400">{{ session('success') }}</p>
    </div>
    @endif

    {{-- Edit Data --}}
    <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-theme-sm p-6 space-y-4">
        <h3 class="font-semibold text-gray-800 dark:text-white text-sm">Data Pengguna</h3>
        <form method="POST" action="{{ route('admin.pengguna.update', $user) }}">
            @csrf @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}"
                        class="h-10 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-3 text-sm dark:text-white dark:bg-gray-900">
                    @error('name') <p class="mt-1 text-xs text-error-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Role</label>
                    <select name="role"
                        class="h-10 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-3 text-sm dark:text-white dark:bg-gray-900">
                        @foreach(['peserta' => 'Peserta', 'panitia' => 'Panitia', 'admin' => 'Admin'] as $val => $label)
                        <option value="{{ $val }}" {{ old('role', $user->role) === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('role') <p class="mt-1 text-xs text-error-500">{{ $message }}</p> @enderror
                </div>

                @if($user->email !== null || $user->role !== 'peserta')
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}"
                        class="h-10 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-3 text-sm dark:text-white dark:bg-gray-900">
                    @error('email') <p class="mt-1 text-xs text-error-500">{{ $message }}</p> @enderror
                </div>
                @endif

                @if($user->nisn !== null || $user->role === 'peserta')
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">NISN</label>
                    <input type="text" name="nisn" value="{{ old('nisn', $user->nisn) }}" maxlength="10"
                        class="h-10 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-3 text-sm dark:text-white dark:bg-gray-900">
                    @error('nisn') <p class="mt-1 text-xs text-error-500">{{ $message }}</p> @enderror
                </div>
                @endif

                <button type="submit"
                    class="w-full bg-brand-500 hover:bg-brand-600 text-white text-sm font-semibold py-2.5 rounded-lg transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    {{-- Reset Password --}}
    <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-theme-sm p-6 space-y-4">
        <h3 class="font-semibold text-gray-800 dark:text-white text-sm">Reset Password</h3>
        <form method="POST" action="{{ route('admin.pengguna.reset-password', $user) }}">
            @csrf @method('PATCH')
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Password Baru</label>
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
                    class="w-full bg-warning-500 hover:bg-warning-600 text-white text-sm font-semibold py-2.5 rounded-lg transition">
                    Reset Password
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
