@extends('layouts.app')

@section('title', 'Upload Berkas')

@section('content')
<div class="max-w-3xl space-y-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Upload Berkas</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400">Upload dokumen pendukung pendaftaran (max 2MB per file)</p>
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
        <p class="text-sm text-warning-700 dark:text-warning-400">Upload berkas terkunci. Berkas sedang dalam proses verifikasi.</p>
    </div>
    @endif

    @foreach($tipeList as $tipe => $label)
    @php $existing = $berkasByTipe->get($tipe); @endphp
    <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-theme-sm p-6">
        <div class="flex items-start justify-between mb-4">
            <div>
                <h3 class="text-sm font-semibold text-gray-800 dark:text-white">{{ $label }}</h3>
                @if($existing)
                <p class="text-xs text-success-600 dark:text-success-400 mt-0.5">
                    ✓ Sudah diupload — {{ $existing->mime_type }}
                </p>
                @else
                <p class="text-xs text-gray-400 mt-0.5">Belum diupload</p>
                @endif
            </div>
        </div>

        @if(!$peserta?->uploadTerkunci())
        <form method="POST" action="{{ route('peserta.berkas.store') }}" enctype="multipart/form-data" class="space-y-3">
            @csrf
            <input type="hidden" name="tipe_berkas" value="{{ $tipe }}">

            <div>
                <input type="file" name="file"
                    accept="{{ in_array($tipe, ['foto_3x4']) ? 'image/jpeg,image/png' : 'application/pdf' }}"
                    class="block w-full text-sm text-gray-500 dark:text-gray-400
                           file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0
                           file:text-sm file:font-semibold file:bg-brand-50 file:text-brand-700
                           hover:file:bg-brand-100 dark:file:bg-brand-900 dark:file:text-brand-300">
                @error('file') <p class="mt-1 text-xs text-error-500">{{ $message }}</p> @enderror
            </div>

            @if($tipe === 'berkas_lainnya')
            <div>
                <input type="text" name="keterangan" value="{{ old('keterangan', $existing?->keterangan) }}"
                    placeholder="Keterangan berkas (opsional)"
                    class="h-9 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-3 py-2 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:text-white">
            </div>
            @endif

            <button type="submit"
                class="bg-brand-500 hover:bg-brand-600 text-white text-xs font-semibold px-4 py-2 rounded-lg transition">
                {{ $existing ? 'Ganti File' : 'Upload' }}
            </button>
        </form>
        @endif
    </div>
    @endforeach
</div>
@endsection
