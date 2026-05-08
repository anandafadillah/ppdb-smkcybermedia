@extends('layouts.guest')

@section('title', 'Pengumuman')

@section('content')
@php
    use App\Models\Setting;
    use Illuminate\Support\Facades\Storage;
    $namaSekolah  = Setting::get('nama_sekolah', 'SMK Cyber Media Jakarta');
    $logoSekolah  = Setting::get('logo');
    $alamat       = Setting::get('alamat', '');
    $telepon      = Setting::get('telepon', '');
    $emailSekolah = Setting::get('email', '');
@endphp

<div class="min-h-screen bg-gray-50 dark:bg-gray-950" x-data>

    {{-- Navbar --}}
    <nav class="sticky top-0 z-50 border-b border-gray-200 bg-white/90 backdrop-blur dark:border-gray-800 dark:bg-gray-900/90">
        <div class="mx-auto max-w-6xl px-4 sm:px-6">
            <div class="flex h-16 items-center justify-between">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    @if($logoSekolah && Storage::disk('public')->exists($logoSekolah))
                        <img src="{{ '/storage/' . $logoSekolah }}"
                             alt="Logo" class="h-8 w-8 rounded-lg object-contain">
                    @else
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-500 text-xs font-bold text-white">
                            {{ strtoupper(substr($namaSekolah, 0, 2)) }}
                        </span>
                    @endif
                    <span class="hidden font-bold text-gray-900 dark:text-white sm:block">{{ $namaSekolah }}</span>
                </a>

                <div class="flex items-center gap-3">
                    <button @click="$store.theme.toggle()"
                        class="flex h-9 w-9 items-center justify-center rounded-full border border-gray-200 text-gray-500 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-800">
                        <svg class="hidden dark:block" width="18" height="18" viewBox="0 0 20 20" fill="none">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M10 2.5a.75.75 0 01.75.75V4a.75.75 0 01-1.5 0V3.25A.75.75 0 0110 2.5zm4.33 1.84a.75.75 0 010 1.06l-.53.53a.75.75 0 01-1.06-1.06l.53-.53a.75.75 0 011.06 0zm-9.72 0a.75.75 0 011.06 0l.53.53A.75.75 0 015.14 6l-.53-.53a.75.75 0 010-1.06zM10 6.5a3.5 3.5 0 100 7 3.5 3.5 0 000-7zM2.5 10a.75.75 0 01.75-.75H4a.75.75 0 010 1.5h-.75A.75.75 0 012.5 10zm12.75-.75H16a.75.75 0 010 1.5h-.75a.75.75 0 010-1.5zM4.61 14.39a.75.75 0 011.06 0l.53.53a.75.75 0 01-1.06 1.06l-.53-.53a.75.75 0 010-1.06zm9.72 0a.75.75 0 010 1.06l-.53.53a.75.75 0 01-1.06-1.06l.53-.53a.75.75 0 011.06 0zM10 15.25a.75.75 0 01.75.75v.75a.75.75 0 01-1.5 0V16a.75.75 0 01.75-.75z" fill="currentColor"/>
                        </svg>
                        <svg class="dark:hidden" width="18" height="18" viewBox="0 0 20 20" fill="none">
                            <path d="M17.5 11.97a8.5 8.5 0 01-9.47-9.47A7.5 7.5 0 1017.5 11.97z" fill="currentColor"/>
                        </svg>
                    </button>
                    <a href="{{ route('login') }}"
                       class="text-sm font-medium text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}"
                       class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-600 transition-colors">
                        Daftar Sekarang
                    </a>
                </div>
            </div>
        </div>
    </nav>

    {{-- Header --}}
    <section class="border-b border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 py-12">
        <div class="mx-auto max-w-6xl px-4 sm:px-6">
            <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-3">
                <a href="{{ route('home') }}" class="hover:text-brand-600 dark:hover:text-brand-400">Beranda</a>
                <span>/</span>
                <span class="text-gray-800 dark:text-white">Pengumuman</span>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Pengumuman</h1>
            <p class="mt-2 text-gray-500 dark:text-gray-400">Informasi dan pengumuman terbaru dari {{ $namaSekolah }}</p>
        </div>
    </section>

    {{-- Content --}}
    <section class="py-12">
        <div class="mx-auto max-w-6xl px-4 sm:px-6">
            <div class="max-w-3xl mx-auto">

                @forelse($pengumuman as $item)
                <a href="{{ route('pengumuman.show', $item) }}"
                   class="block mb-4 rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900 p-6 shadow-sm hover:shadow-md hover:border-brand-300 dark:hover:border-brand-700 transition-all">
                    <div class="flex items-start justify-between gap-4">
                        <h2 class="font-semibold text-gray-900 dark:text-white group-hover:text-brand-600">{{ $item->judul }}</h2>
                        <span class="shrink-0 text-xs text-gray-400 mt-0.5">
                            {{ $item->tanggal_publish?->translatedFormat('d M Y') ?? $item->created_at->translatedFormat('d M Y') }}
                        </span>
                    </div>
                    <p class="mt-3 text-sm text-gray-600 dark:text-gray-300 line-clamp-3">
                        {{ strip_tags($item->isi) }}
                    </p>
                    <span class="mt-4 inline-flex items-center gap-1 text-xs font-medium text-brand-600 dark:text-brand-400">
                        Baca selengkapnya
                        <svg width="12" height="12" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-7-7l7 7-7 7"/></svg>
                    </span>
                </a>
                @empty
                <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900 p-12 text-center">
                    <p class="text-gray-400">Belum ada pengumuman yang dipublikasikan.</p>
                </div>
                @endforelse

                {{-- Pagination --}}
                @if($pengumuman->hasPages())
                <div class="mt-6">
                    {{ $pengumuman->links() }}
                </div>
                @endif

            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="border-t border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 py-10">
        <div class="mx-auto max-w-6xl px-4 sm:px-6">
            <div class="flex flex-col items-center gap-4 sm:flex-row sm:justify-between">
                <div class="flex items-center gap-3">
                    @if($logoSekolah && Storage::disk('public')->exists($logoSekolah))
                        <img src="{{ '/storage/' . $logoSekolah }}"
                             alt="Logo" class="h-8 w-8 rounded-lg object-contain">
                    @endif
                    <span class="font-semibold text-gray-800 dark:text-white">{{ $namaSekolah }}</span>
                </div>
                <div class="flex flex-col items-center gap-1 text-sm text-gray-400 sm:items-end">
                    @if($alamat)<span>{{ $alamat }}</span>@endif
                    <div class="flex gap-4">
                        @if($telepon)<span>{{ $telepon }}</span>@endif
                        @if($emailSekolah)<span>{{ $emailSekolah }}</span>@endif
                    </div>
                </div>
            </div>
            <p class="mt-6 text-center text-xs text-gray-400">
                &copy; {{ date('Y') }} {{ $namaSekolah }}. Sistem PPDB Online.
            </p>
        </div>
    </footer>

</div>
@endsection
