@extends('layouts.app')

@section('title', 'Asal Sekolah')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Asal Sekolah</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">Kelola data asal sekolah peserta</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.asal-sekolah.create') }}"
                class="bg-brand-500 hover:bg-brand-600 text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition">
                + Tambah Sekolah
            </a>
        </div>
    </div>

    {{-- Search --}}
    <form method="GET" action="{{ route('admin.asal-sekolah.index') }}" class="flex gap-3">
        <input type="text" name="search" value="{{ request('search') }}"
            placeholder="Cari nama atau NPSN..."
            class="h-11 w-full max-w-sm rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white">
        <button type="submit"
            class="bg-brand-500 hover:bg-brand-600 text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition">
            Cari
        </button>
        @if(request('search'))
            <a href="{{ route('admin.asal-sekolah.index') }}"
                class="bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-semibold px-4 py-2.5 rounded-lg transition">
                Reset
            </a>
        @endif
    </form>

    {{-- Tabel --}}
    <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-theme-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-800 text-left">
                <tr>
                    <th class="px-5 py-3.5 font-medium text-gray-700 dark:text-gray-300">NPSN</th>
                    <th class="px-5 py-3.5 font-medium text-gray-700 dark:text-gray-300">Nama Sekolah</th>
                    <th class="px-5 py-3.5 font-medium text-gray-700 dark:text-gray-300">Kecamatan</th>
                    <th class="px-5 py-3.5 font-medium text-gray-700 dark:text-gray-300">Status</th>
                    <th class="px-5 py-3.5 font-medium text-gray-700 dark:text-gray-300">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse($asalSekolah as $sekolah)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                    <td class="px-5 py-4 font-mono text-gray-700 dark:text-gray-300">{{ $sekolah->npsn }}</td>
                    <td class="px-5 py-4 text-gray-800 dark:text-white font-medium">{{ $sekolah->nama }}</td>
                    <td class="px-5 py-4 text-gray-500 dark:text-gray-400">{{ $sekolah->kecamatan ?? '-' }}</td>
                    <td class="px-5 py-4">
                        @if($sekolah->status === 'negeri')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-success-50 dark:bg-success-500/10 text-success-700 dark:text-success-400">
                                Negeri
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400">
                                Swasta
                            </span>
                        @endif
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <a href="{{ route('admin.asal-sekolah.edit', $sekolah) }}"
                                class="text-brand-600 hover:text-brand-700 dark:text-brand-400 text-sm font-medium">
                                Edit
                            </a>
                            <form method="POST" action="{{ route('admin.asal-sekolah.destroy', $sekolah) }}"
                                onsubmit="return confirm('Hapus sekolah ini?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="text-error-600 hover:text-error-700 dark:text-error-400 text-sm font-medium">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-5 py-10 text-center text-gray-400 dark:text-gray-500">
                        Belum ada data asal sekolah.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($asalSekolah->hasPages())
        <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-800">
            {{ $asalSekolah->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
