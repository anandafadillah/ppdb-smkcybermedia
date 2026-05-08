@extends('layouts.app')

@section('title', 'Mata Pelajaran')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Mata Pelajaran</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">Kelola mata pelajaran untuk penilaian rapor</p>
        </div>
        <a href="{{ route('admin.mata-pelajaran.create') }}"
            class="bg-brand-500 hover:bg-brand-600 text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition">
            + Tambah Mata Pelajaran
        </a>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-theme-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-800 text-left">
                <tr>
                    <th class="px-5 py-3.5 font-medium text-gray-700 dark:text-gray-300">Nama Mata Pelajaran</th>
                    <th class="px-5 py-3.5 font-medium text-gray-700 dark:text-gray-300">Status</th>
                    <th class="px-5 py-3.5 font-medium text-gray-700 dark:text-gray-300">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse($mataPelajaran as $mapel)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                    <td class="px-5 py-4 text-gray-800 dark:text-white font-medium">{{ $mapel->nama }}</td>
                    <td class="px-5 py-4">
                        @if($mapel->is_active)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-success-50 dark:bg-success-500/10 text-success-700 dark:text-success-400">Aktif</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400">Nonaktif</span>
                        @endif
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <form method="POST" action="{{ route('admin.mata-pelajaran.toggle-aktif', $mapel) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="text-sm font-medium {{ $mapel->is_active ? 'text-warning-600 hover:text-warning-700 dark:text-warning-400' : 'text-success-600 hover:text-success-700 dark:text-success-400' }}">
                                    {{ $mapel->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                            </form>
                            <a href="{{ route('admin.mata-pelajaran.edit', $mapel) }}"
                                class="text-brand-600 hover:text-brand-700 dark:text-brand-400 text-sm font-medium">Edit</a>
                            <form method="POST" action="{{ route('admin.mata-pelajaran.destroy', $mapel) }}"
                                onsubmit="return confirm('Hapus mata pelajaran ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-error-600 hover:text-error-700 dark:text-error-400 text-sm font-medium">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="px-5 py-10 text-center text-gray-400 dark:text-gray-500">
                        Belum ada mata pelajaran.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
