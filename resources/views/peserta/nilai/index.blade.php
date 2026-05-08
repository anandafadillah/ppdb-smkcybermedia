@extends('layouts.app')

@section('title', 'Nilai Rapor')

@section('content')
@if($config && !$config->isFieldActive('nilai_rapor'))
<div class="max-w-4xl">
    <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 p-8 text-center">
        <p class="text-sm text-gray-500 dark:text-gray-400">Pengisian nilai rapor tidak diperlukan untuk pendaftaran ini.</p>
    </div>
</div>
@else
<div class="max-w-4xl space-y-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Nilai Rapor</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400">Isi nilai rapor semester 1–5 per mata pelajaran</p>
    </div>

    @if(session('success'))
    <div class="rounded-xl border border-success-200 bg-success-50 dark:border-success-800 dark:bg-success-500/10 p-4">
        <p class="text-sm text-success-700 dark:text-success-400">{{ session('success') }}</p>
    </div>
    @endif

    @if(session('error'))
    <div class="rounded-xl border border-error-200 bg-error-50 dark:border-error-800 dark:bg-error-500/10 p-4">
        <p class="text-sm text-error-700 dark:text-error-400">{{ session('error') }}</p>
    </div>
    @endif

    @if($peserta?->uploadTerkunci())
    <div class="rounded-xl border border-warning-200 bg-warning-50 dark:border-warning-800 dark:bg-warning-500/10 p-4">
        <p class="text-sm text-warning-700 dark:text-warning-400">Input nilai terkunci. Data sedang dalam proses verifikasi.</p>
    </div>
    @endif

    @if($mataPelajaran->isEmpty())
    <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 p-6">
        <p class="text-sm text-gray-500">Belum ada mata pelajaran aktif.</p>
    </div>
    @else
    <form method="POST" action="{{ route('peserta.nilai.store') }}">
        @csrf

        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-theme-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-800">
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">Mata Pelajaran</th>
                        @for($s = 1; $s <= 5; $s++)
                        <th class="px-3 py-3 text-center font-semibold text-gray-700 dark:text-gray-300">Sem {{ $s }}</th>
                        @endfor
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach($mataPelajaran as $mapel)
                    <tr>
                        <td class="px-4 py-3 text-gray-800 dark:text-gray-200 font-medium">{{ $mapel->nama }}</td>
                        @for($s = 1; $s <= 5; $s++)
                        <td class="px-3 py-3">
                            <input type="number" name="nilai[{{ $mapel->id }}][{{ $s }}]"
                                value="{{ $nilaiByMapelSemester[$mapel->id][$s] ?? '' }}"
                                min="0" max="100" step="0.01"
                                {{ $peserta?->uploadTerkunci() ? 'disabled' : '' }}
                                class="h-9 w-20 rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-2 py-1.5 text-sm text-center focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white disabled:opacity-50 disabled:cursor-not-allowed">
                        </td>
                        @endfor
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if(!$peserta?->uploadTerkunci())
        <div class="mt-4">
            <button type="submit"
                class="bg-brand-500 hover:bg-brand-600 text-white text-sm font-semibold px-6 py-2.5 rounded-lg transition">
                Simpan Nilai
            </button>
        </div>
        @endif
    </form>
    @endif
</div>
@endif
@endsection
