@extends('layouts.app')

@section('title', 'Tahun Penerimaan')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">Manajemen Tahun Penerimaan</h2>
        <a href="{{ route('admin.tahun-penerimaan.create') }}"
            class="inline-flex items-center gap-2 bg-brand-500 hover:bg-brand-600 text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M8 3.33398V12.6673M3.33337 8.00065H12.6667" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            Tambah
        </a>
    </div>

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-theme-sm">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-400 text-xs uppercase">
                <tr>
                    <th class="px-5 py-3.5 text-left font-medium">Tahun</th>
                    <th class="px-5 py-3.5 text-left font-medium">Label</th>
                    <th class="px-5 py-3.5 text-left font-medium">Status</th>
                    <th class="px-5 py-3.5 text-right font-medium">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse ($tahunPenerimaan as $item)
                    <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">
                        <td class="px-5 py-4 font-mono text-gray-800 dark:text-white/90">{{ $item->tahun }}</td>
                        <td class="px-5 py-4 text-gray-800 dark:text-white/90">{{ $item->label }}</td>
                        <td class="px-5 py-4">
                            @if ($item->is_active)
                                <span class="inline-flex items-center rounded-full bg-success-50 dark:bg-success-500/10 px-2.5 py-0.5 text-xs font-medium text-success-700 dark:text-success-400">Aktif</span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-gray-100 dark:bg-gray-800 px-2.5 py-0.5 text-xs font-medium text-gray-500 dark:text-gray-400">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-right">
                            <div class="flex items-center justify-end gap-3">
                                @unless ($item->is_active)
                                    <form method="POST" action="{{ route('admin.tahun-penerimaan.activate', $item) }}" class="inline">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="text-brand-500 hover:text-brand-600 text-xs font-medium">Aktifkan</button>
                                    </form>
                                @endunless
                                <a href="{{ route('admin.tahun-penerimaan.edit', $item) }}"
                                    class="text-warning-600 hover:text-warning-700 text-xs font-medium">Edit</a>
                                <form method="POST" action="{{ route('admin.tahun-penerimaan.destroy', $item) }}" class="inline"
                                    onsubmit="return confirm('Hapus tahun penerimaan ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-error-500 hover:text-error-600 text-xs font-medium">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-10 text-center text-gray-400 dark:text-gray-600">
                            Belum ada data tahun penerimaan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
