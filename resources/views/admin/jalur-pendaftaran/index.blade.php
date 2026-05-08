@extends('layouts.app')

@section('title', 'Jalur Pendaftaran')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">Jalur Pendaftaran</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Tahun aktif: <span class="font-medium text-gray-700 dark:text-gray-300">{{ $tahunAktif?->label ?? '-' }}</span>
            </p>
        </div>
        <a href="{{ route('admin.jalur-pendaftaran.create') }}"
            class="inline-flex items-center gap-2 bg-brand-500 hover:bg-brand-600 text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M8 3.33398V12.6673M3.33337 8.00065H12.6667" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            Tambah Jalur
        </a>
    </div>

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-theme-sm">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-400 text-xs uppercase">
                <tr>
                    <th class="px-5 py-3.5 text-left font-medium">Nama Jalur</th>
                    <th class="px-5 py-3.5 text-center font-medium">Status</th>
                    <th class="px-5 py-3.5 text-center font-medium">Kuota (%)</th>
                    <th class="px-5 py-3.5 text-center font-medium">Kode Daring</th>
                    <th class="px-5 py-3.5 text-center font-medium">Kode Luring</th>
                    <th class="px-5 py-3.5 text-center font-medium">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse ($jalur as $item)
                    <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">
                        <td class="px-5 py-4 font-medium text-gray-800 dark:text-white/90">{{ $item->nama }}</td>
                        <td class="px-5 py-4 text-center">
                            @if ($item->is_active)
                                <span class="inline-flex items-center rounded-full bg-success-50 dark:bg-success-500/10 px-2.5 py-0.5 text-xs font-medium text-success-700 dark:text-success-400">Aktif</span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-gray-100 dark:bg-gray-800 px-2.5 py-0.5 text-xs font-medium text-gray-500 dark:text-gray-400">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-center text-gray-600 dark:text-gray-400">{{ $item->persentase_kuota }}%</td>
                        <td class="px-5 py-4 text-center text-gray-600 dark:text-gray-400">{{ $item->kode_awal_daring ?? '-' }}</td>
                        <td class="px-5 py-4 text-center text-gray-600 dark:text-gray-400">{{ $item->kode_awal_luring ?? '-' }}</td>
                        <td class="px-5 py-4 text-center">
                            <div class="flex items-center justify-center gap-3">
                                <a href="{{ route('admin.jalur-pendaftaran.edit', $item) }}"
                                    class="text-brand-500 hover:text-brand-600 text-xs font-medium">Edit</a>

                                <form method="POST" action="{{ route('admin.jalur-pendaftaran.toggle-aktif', $item) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="text-warning-600 hover:text-warning-700 text-xs font-medium">
                                        {{ $item->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('admin.jalur-pendaftaran.destroy', $item) }}"
                                    onsubmit="return confirm('Hapus jalur ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-error-500 hover:text-error-600 text-xs font-medium">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-10 text-center text-gray-400 dark:text-gray-600">
                            Belum ada jalur pendaftaran.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
