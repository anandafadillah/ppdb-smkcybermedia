@extends('layouts.app')

@section('title', 'Pengumuman')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Pengumuman</h2>
        <a href="{{ route('admin.pengumuman.create') }}"
           class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-600 transition">
            + Tambah Pengumuman
        </a>
    </div>

    @if(session('success'))
    <div class="rounded-xl border border-success-200 bg-success-50 dark:border-success-800 dark:bg-success-500/10 p-4">
        <p class="text-sm text-success-700 dark:text-success-400">{{ session('success') }}</p>
    </div>
    @endif

    <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-theme-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-800 text-gray-600 dark:text-gray-400">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold">Judul</th>
                    <th class="px-4 py-3 text-left font-semibold">Status</th>
                    <th class="px-4 py-3 text-left font-semibold">Tanggal Publish</th>
                    <th class="px-4 py-3 text-left font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse($pengumuman as $item)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                    <td class="px-4 py-3 text-gray-800 dark:text-white font-medium">{{ $item->judul }}</td>
                    <td class="px-4 py-3">
                        <span class="rounded-full px-2 py-0.5 text-xs font-medium
                            {{ $item->status === 'published' ? 'bg-success-100 text-success-700 dark:bg-success-500/10 dark:text-success-400' : 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400' }}">
                            {{ $item->status === 'published' ? 'Published' : 'Draft' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $item->tanggal_publish?->format('d/m/Y') ?? '-' }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <a href="{{ route('admin.pengumuman.edit', $item) }}"
                               class="text-xs font-medium text-brand-600 hover:text-brand-700 dark:text-brand-400">Edit</a>
                            <form method="POST" action="{{ route('admin.pengumuman.destroy', $item) }}"
                                  onsubmit="return confirm('Hapus pengumuman ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs font-medium text-error-600 hover:text-error-700 dark:text-error-400">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-4 py-8 text-center text-gray-400 text-sm">Belum ada pengumuman.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $pengumuman->links() }}</div>
</div>
@endsection
