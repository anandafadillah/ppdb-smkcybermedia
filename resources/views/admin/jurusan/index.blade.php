@extends('layouts.app')

@section('title', 'Manajemen Jurusan')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">Manajemen Jurusan</h2>
        <a href="{{ route('admin.jurusan.create') }}"
            class="inline-flex items-center gap-2 bg-brand-500 hover:bg-brand-600 text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M8 3.33398V12.6673M3.33337 8.00065H12.6667" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            Tambah Jurusan
        </a>
    </div>

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-theme-sm">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-400 text-xs uppercase">
                <tr>
                    <th class="px-5 py-3.5 text-left font-medium">Kode</th>
                    <th class="px-5 py-3.5 text-left font-medium">Nama Jurusan</th>
                    <th class="px-5 py-3.5 text-left font-medium">Kapasitas</th>
                    <th class="px-5 py-3.5 text-left font-medium">Deskripsi</th>
                    <th class="px-5 py-3.5 text-right font-medium">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse ($jurusan as $item)
                    <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">
                        <td class="px-5 py-4 font-mono font-semibold text-gray-800 dark:text-white/90">{{ $item->kode }}</td>
                        <td class="px-5 py-4 text-gray-800 dark:text-white/90">{{ $item->nama }}</td>
                        <td class="px-5 py-4 text-gray-600 dark:text-gray-400">
                            {{ $item->kapasitas ? $item->kapasitas . ' siswa' : '—' }}
                        </td>
                        <td class="px-5 py-4 text-gray-500 dark:text-gray-400 max-w-xs truncate">
                            {{ $item->deskripsi ?? '—' }}
                        </td>
                        <td class="px-5 py-4 text-right">
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ route('admin.jurusan.edit', $item) }}"
                                    class="text-brand-500 hover:text-brand-600 text-xs font-medium">Edit</a>
                                <form method="POST" action="{{ route('admin.jurusan.destroy', $item) }}"
                                    onsubmit="return confirm('Hapus jurusan {{ $item->kode }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-error-500 hover:text-error-600 text-xs font-medium">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-10 text-center text-gray-400 dark:text-gray-600">
                            Belum ada data jurusan.
                            <a href="{{ route('admin.jurusan.create') }}" class="text-brand-500 hover:text-brand-600">Tambah sekarang</a>.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
