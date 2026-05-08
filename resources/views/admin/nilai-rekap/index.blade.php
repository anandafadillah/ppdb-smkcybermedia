@extends('layouts.app')

@section('title', 'Rekap Nilai Rapor')

@section('content')
<div class="space-y-5">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Rekap Nilai Rapor</h2>
        <a href="{{ url()->current() . '/export?' . http_build_query(request()->all()) }}"
           class="bg-success-500 hover:bg-success-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
            Export Excel
        </a>
    </div>

    {{-- Filter --}}
    <form method="GET" class="flex flex-wrap gap-3">
        <select name="jalur_id"
            class="h-10 rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 text-sm focus:border-brand-300 focus:outline-hidden dark:text-white dark:bg-gray-900">
            <option value="">-- Semua Jalur --</option>
            @foreach($jalurList as $j)
            <option value="{{ $j->id }}" {{ request('jalur_id') == $j->id ? 'selected' : '' }}>{{ $j->nama }}</option>
            @endforeach
        </select>
        <select name="jurusan_id"
            class="h-10 rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 text-sm focus:border-brand-300 focus:outline-hidden dark:text-white dark:bg-gray-900">
            <option value="">-- Semua Jurusan --</option>
            @foreach($jurusanList as $j)
            <option value="{{ $j->id }}" {{ request('jurusan_id') == $j->id ? 'selected' : '' }}>{{ $j->nama }}</option>
            @endforeach
        </select>
        <button type="submit" class="h-10 bg-brand-500 hover:bg-brand-600 text-white text-sm font-semibold px-4 rounded-lg transition">Filter</button>
        @if(request()->hasAny(['jalur_id', 'jurusan_id']))
        <a href="{{ url()->current() }}" class="h-10 flex items-center px-3 text-sm text-gray-500 hover:text-gray-700">Reset</a>
        @endif
    </form>

    {{-- Table --}}
    <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-theme-sm">
        <table class="w-full text-xs whitespace-nowrap">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <th class="px-3 py-2.5 text-left font-semibold text-gray-700 dark:text-gray-300 sticky left-0 bg-gray-50 dark:bg-gray-800">Nama</th>
                    <th class="px-3 py-2.5 text-left font-semibold text-gray-700 dark:text-gray-300">Jalur</th>
                    <th class="px-3 py-2.5 text-left font-semibold text-gray-700 dark:text-gray-300">Jurusan</th>
                    @foreach($mapelList as $mapel)
                        @for($s = 1; $s <= 5; $s++)
                        <th class="px-2 py-2.5 text-center font-semibold text-gray-700 dark:text-gray-300 min-w-[52px]" title="{{ $mapel->nama }}">
                            {{ $mapel->nama }}<br><span class="font-normal text-gray-400">S{{ $s }}</span>
                        </th>
                        @endfor
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse($pesertaList as $p)
                @php
                    $nilaiMap = $p->nilai->groupBy('mata_pelajaran_id');
                @endphp
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                    <td class="px-3 py-2 text-gray-800 dark:text-white font-medium sticky left-0 bg-white dark:bg-gray-900">
                        {{ $p->dataDiri?->nama_lengkap ?? $p->user?->name ?? '-' }}
                    </td>
                    <td class="px-3 py-2 text-gray-500 dark:text-gray-400">{{ $p->jalur?->nama ?? '-' }}</td>
                    <td class="px-3 py-2 text-gray-500 dark:text-gray-400">{{ $p->jurusan?->nama ?? '-' }}</td>
                    @foreach($mapelList as $mapel)
                        @for($s = 1; $s <= 5; $s++)
                        @php
                            $n = $nilaiMap->get($mapel->id)?->firstWhere('semester', $s);
                        @endphp
                        <td class="px-2 py-2 text-center {{ $n ? 'text-gray-800 dark:text-white' : 'text-gray-300 dark:text-gray-600' }}">
                            {{ $n ? number_format($n->nilai, 1) : '-' }}
                        </td>
                        @endfor
                    @endforeach
                </tr>
                @empty
                <tr>
                    <td colspan="{{ 3 + ($mapelList->count() * 5) }}" class="px-4 py-8 text-center text-sm text-gray-400">
                        Tidak ada data.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
