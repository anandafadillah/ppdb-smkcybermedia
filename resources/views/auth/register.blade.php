@extends('layouts.guest')

@section('title', 'Daftar Peserta')

@section('content')
<div class="relative z-1 bg-white dark:bg-gray-900">
    <div class="relative flex min-h-screen w-full flex-col justify-center lg:flex-row dark:bg-gray-900">

        {{-- Form Panel --}}
        <div class="flex w-full flex-1 flex-col lg:w-1/2">
            <div class="mx-auto flex w-full max-w-md flex-1 flex-col justify-center px-6 py-10">
                <div>
                    <div class="mb-8">
                        <h1 class="text-title-sm mb-2 font-semibold text-gray-800 dark:text-white/90">
                            Pendaftaran Peserta Baru
                        </h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Isi data di bawah untuk membuat akun PPDB.
                        </p>
                    </div>

                    <form method="POST" action="{{ route('register') }}" novalidate>
                        @csrf
                        <div class="space-y-5">

                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    Nama Lengkap <span class="text-error-500">*</span>
                                </label>
                                <input type="text" name="nama" value="{{ old('nama') }}"
                                    placeholder="Nama lengkap sesuai akta kelahiran"
                                    class="shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30
                                    {{ $errors->has('nama') ? 'border-error-400' : 'border-gray-300' }}"
                                    autofocus required>
                                @error('nama')
                                    <p class="mt-1 text-xs text-error-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    NISN <span class="text-error-500">*</span>
                                </label>
                                <input type="text" name="nisn" value="{{ old('nisn') }}"
                                    placeholder="10 digit angka"
                                    maxlength="10" inputmode="numeric"
                                    class="shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30
                                    {{ $errors->has('nisn') ? 'border-error-400' : 'border-gray-300' }}"
                                    required>
                                @error('nisn')
                                    <p class="mt-1 text-xs text-error-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    Password <span class="text-error-500">*</span>
                                </label>
                                <div x-data="{ show: false }" class="relative">
                                    <input :type="show ? 'text' : 'password'" name="password"
                                        placeholder="Minimal 8 karakter"
                                        class="shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border bg-transparent py-2.5 pr-11 pl-4 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30
                                        {{ $errors->has('password') ? 'border-error-400' : 'border-gray-300' }}"
                                        required>
                                    <span @click="show = !show"
                                        class="absolute top-1/2 right-4 z-30 -translate-y-1/2 cursor-pointer text-gray-500 dark:text-gray-400">
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M10.0002 13.8619C7.23361 13.8619 4.86803 12.1372 3.92328 9.70241C4.86804 7.26761 7.23361 5.54297 10.0002 5.54297C12.7667 5.54297 15.1323 7.26762 16.0771 9.70243C15.1323 12.1372 12.7667 13.8619 10.0002 13.8619ZM9.99151 7.84413C8.96527 7.84413 8.13333 8.67606 8.13333 9.70231C8.13333 10.7286 8.96527 11.5605 9.99151 11.5605H10.0064C11.0326 11.5605 11.8646 10.7286 11.8646 9.70231C11.8646 8.67606 11.0326 7.84413 10.0064 7.84413H9.99151Z" fill="#98A2B3"/>
                                        </svg>
                                    </span>
                                </div>
                                @error('password')
                                    <p class="mt-1 text-xs text-error-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    Konfirmasi Password <span class="text-error-500">*</span>
                                </label>
                                <input type="password" name="password_confirmation"
                                    placeholder="Ulangi password"
                                    class="shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                                    required>
                            </div>

                            <div>
                                <button type="submit"
                                    class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 flex w-full items-center justify-center rounded-lg px-4 py-3 text-sm font-medium text-white transition">
                                    Daftar Sekarang
                                </button>
                            </div>
                        </div>
                    </form>

                    <div class="mt-5">
                        <p class="text-center text-sm font-normal text-gray-700 dark:text-gray-400">
                            Sudah punya akun?
                            <a href="{{ route('login') }}" class="text-brand-500 hover:text-brand-600 dark:text-brand-400">Masuk di sini</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Branding Panel --}}
        <div class="bg-brand-950 relative hidden h-full w-full items-center lg:grid lg:w-1/2 dark:bg-white/5">
            <div class="z-1 flex items-center justify-center">
                <div class="flex max-w-xs flex-col items-center text-center px-6">
                    <h2 class="mb-4 text-2xl font-bold text-white">SMK Cyber Media Jakarta</h2>
                    <p class="text-gray-400 dark:text-white/60">
                        Sistem Penerimaan Peserta Didik Baru (PPDB) — Daftarkan dirimu sekarang!
                    </p>
                </div>
            </div>
        </div>

        {{-- Theme Toggle --}}
        <div class="fixed right-6 bottom-6 z-50">
            <button
                class="bg-brand-500 hover:bg-brand-600 inline-flex size-14 items-center justify-center rounded-full text-white transition-colors"
                @click.prevent="$store.theme.toggle()">
                <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none">
                    <path d="M17.4547 11.97L18.1799 12.1611C18.265 11.8383 18.1265 11.4982 17.8401 11.3266C17.5538 11.1551 17.1885 11.1934 16.944 11.4207L17.4547 11.97ZM10.0003 16.9586C6.15734 16.9586 3.04199 13.8433 3.04199 10.0003H1.54199C1.54199 14.6717 5.32892 18.4586 10.0003 18.4586V16.9586Z" fill="currentColor"/>
                </svg>
            </button>
        </div>
    </div>
</div>
@endsection
