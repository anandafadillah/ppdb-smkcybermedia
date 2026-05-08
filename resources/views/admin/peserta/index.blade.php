@extends('layouts.app')

@section('title', 'Manajemen Peserta')

@section('content')
<div class="space-y-5">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Manajemen Peserta</h2>
        <div class="flex gap-2">
            <a href="{{ route(request()->segment(1) . '.peserta.export') . '?' . http_build_query(request()->all()) }}"
               class="inline-flex items-center gap-2 bg-success-500 hover:bg-success-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                Export Excel
            </a>
            <a href="{{ route(request()->segment(1) . '.peserta.create') }}"
               class="inline-flex items-center gap-2 bg-brand-500 hover:bg-brand-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                Tambah Manual
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="rounded-xl border border-success-200 bg-success-50 dark:border-success-800 dark:bg-success-500/10 p-4">
        <p class="text-sm text-success-700 dark:text-success-400">{{ session('success') }}</p>
    </div>
    @endif

    {{-- Filter & Search --}}
    <form method="GET" class="flex flex-wrap gap-3">
        <input type="text" name="search" value="{{ request('search') }}"
            placeholder="Cari nama, NISN, no. pendaftaran..."
            class="h-10 rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white w-72">

        <select name="jalur_id"
            class="h-10 rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 text-sm focus:border-brand-300 focus:outline-hidden dark:text-white dark:bg-gray-900">
            <option value="">-- Semua Jalur --</option>
            @foreach($jalurList as $j)
            <option value="{{ $j->id }}" {{ request('jalur_id') == $j->id ? 'selected' : '' }}>{{ $j->nama }}</option>
            @endforeach
        </select>

        <select name="status_verifikasi"
            class="h-10 rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 text-sm focus:border-brand-300 focus:outline-hidden dark:text-white dark:bg-gray-900">
            <option value="">-- Semua Status Verifikasi --</option>
            <option value="belum_diverifikasi" {{ request('status_verifikasi') === 'belum_diverifikasi' ? 'selected' : '' }}>Belum Diverifikasi</option>
            <option value="terverifikasi" {{ request('status_verifikasi') === 'terverifikasi' ? 'selected' : '' }}>Terverifikasi</option>
            <option value="ditolak" {{ request('status_verifikasi') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
        </select>

        <button type="submit"
            class="h-10 bg-brand-500 hover:bg-brand-600 text-white text-sm font-semibold px-4 rounded-lg transition">
            Filter
        </button>
        @if(request()->hasAny(['search', 'jalur_id', 'status_verifikasi', 'status_hasil']))
        <a href="{{ url()->current() }}" class="h-10 flex items-center px-4 text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400">Reset</a>
        @endif
    </form>

    {{-- Table --}}
    <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-theme-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">Nama / NISN</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">No. Pendaftaran</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">Jalur</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">Jurusan</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">Status</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse($pesertaList as $p)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-800 dark:text-white">{{ $p->dataDiri?->nama_lengkap ?? $p->user?->name ?? '-' }}</div>
                        <div class="text-xs text-gray-400">{{ $p->user?->nisn ?? '-' }}</div>
                    </td>
                    <td class="px-4 py-3 font-mono text-gray-600 dark:text-gray-300">{{ $p->no_pendaftaran ?? '-' }}</td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $p->jalur?->nama ?? '-' }}</td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $p->jurusan?->nama ?? '-' }}</td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                            {{ $p->status_verifikasi === 'terverifikasi' ? 'bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400' : ($p->status_verifikasi === 'ditolak' ? 'bg-error-50 text-error-700 dark:bg-error-500/10 dark:text-error-400' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300') }}">
                            {{ str_replace('_', ' ', $p->status_verifikasi) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route(request()->segment(1) . '.peserta.show', $p) }}"
                               class="text-xs text-brand-600 hover:text-brand-700 dark:text-brand-400 font-medium">Detail</a>
                            <a href="{{ route(request()->segment(1) . '.peserta.edit', $p) }}"
                               class="text-xs text-gray-500 hover:text-gray-700 dark:text-gray-400 font-medium">Edit</a>
                            <form method="POST" action="{{ route(request()->segment(1) . '.peserta.destroy', $p) }}"
                                onsubmit="return confirm('Hapus peserta ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs text-error-600 hover:text-error-700 font-medium">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-400">Tidak ada peserta.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($pesertaList->hasPages())
        <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-800">
            {{ $pesertaList->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
