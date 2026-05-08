@extends('layouts.app')

@section('title', 'Konfigurasi Formulir')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">Konfigurasi Formulir</h2>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            Tahun Penerimaan: <strong class="text-gray-700 dark:text-gray-300">{{ $tahunPenerimaan->label ?? $tahunPenerimaan->tahun }}</strong>
        </p>
    </div>
    @if($config->is_locked)
        <span class="inline-flex items-center gap-1.5 rounded-full bg-error-50 dark:bg-error-500/10 px-3 py-1.5 text-xs font-semibold text-error-600 dark:text-error-400">
            <svg width="12" height="12" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M12 17v-1m0 0a4 4 0 100-8 4 4 0 000 8zm-8 4h16a1 1 0 001-1V10a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1zM8 9V7a4 4 0 118 0v2"/></svg>
            Terkunci — sudah ada peserta yang submit
        </span>
    @endif
</div>

@if(session('success'))
<div class="mb-4 rounded-xl border border-success-200 bg-success-50 dark:border-success-800 dark:bg-success-500/10 p-4">
    <p class="text-sm text-success-700 dark:text-success-400">{{ session('success') }}</p>
</div>
@endif
@if(session('error'))
<div class="mb-4 rounded-xl border border-error-200 bg-error-50 dark:border-error-800 dark:bg-error-500/10 p-4">
    <p class="text-sm text-error-700 dark:text-error-400">{{ session('error') }}</p>
</div>
@endif

<form method="POST"
    action="{{ auth()->user()->role === 'admin' ? route('admin.tahun-penerimaan.form-config.update', $tahunPenerimaan) : route('panitia.tahun-penerimaan.form-config.update', $tahunPenerimaan) }}">
    @csrf
    @method('PUT')

    {{-- Keterangan Formulir --}}
    <div class="mb-5 rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-theme-sm p-6">
        <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-300">
            Keterangan / Ketentuan Formulir
        </label>
        <p class="mb-3 text-xs text-gray-400">Teks ini akan tampil di bagian atas formulir pendaftaran peserta sebagai panduan pengisian.</p>
        <textarea name="keterangan_formulir" rows="4"
            {{ $config->is_locked ? 'disabled' : '' }}
            placeholder="Contoh: Setiap calon peserta didik wajib mengisi formulir ini dengan benar dan jujur..."
            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white/90 disabled:opacity-60 disabled:cursor-not-allowed">{{ old('keterangan_formulir', $keterangan) }}</textarea>
    </div>

    {{-- Legend --}}
    <div class="mb-4 flex flex-wrap items-center gap-x-6 gap-y-2 text-xs text-gray-500 dark:text-gray-400">
        <span class="flex items-center gap-1.5">
            <span class="inline-block w-3 h-3 rounded border-2 border-brand-500 bg-brand-500"></span>
            Field aktif
        </span>
        <span class="flex items-center gap-1.5">
            <span class="inline-block w-3 h-3 rounded border-2 border-gray-300 dark:border-gray-600"></span>
            Field nonaktif
        </span>
        <span class="flex items-center gap-1.5">
            <span class="inline-flex items-center rounded-full bg-warning-50 dark:bg-warning-500/10 px-2 py-0.5 text-[10px] font-semibold text-warning-700 dark:text-warning-400">Wajib</span>
            Selalu aktif, tidak bisa dinonaktifkan
        </span>
    </div>

    {{-- Field Groups --}}
    <div class="space-y-4">
        @foreach($fieldGroups as $groupKey => $group)
        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-theme-sm overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 bg-gray-50 dark:bg-gray-800/50 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-sm font-semibold text-gray-800 dark:text-white">{{ $group['label'] }}</h3>
                @php
                    $activeCount = collect($group['fields'])->keys()->filter(fn($k) => $config->isFieldActive($k))->count();
                    $totalCount  = count($group['fields']);
                @endphp
                <span class="text-xs text-gray-400 dark:text-gray-500">{{ $activeCount }}/{{ $totalCount }} aktif</span>
            </div>

            <div class="p-6 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($group['fields'] as $key => $label)
                @php $isFixed = in_array($key, $fixedFields); @endphp
                <label class="flex items-start gap-3 {{ $isFixed ? 'cursor-default' : 'cursor-pointer' }}">
                    <input type="checkbox"
                        name="fields[]"
                        value="{{ $key }}"
                        {{ $config->isFieldActive($key) ? 'checked' : '' }}
                        {{ ($isFixed || $config->is_locked) ? 'disabled' : '' }}
                        class="mt-0.5 h-4 w-4 rounded border-gray-300 text-brand-500 focus:ring-brand-500 disabled:cursor-not-allowed dark:border-gray-600 dark:bg-gray-800">
                    <span class="flex flex-col gap-0.5 text-sm">
                        <span class="text-gray-700 dark:text-gray-300 leading-snug">{{ $label }}</span>
                        @if($isFixed)
                            <span class="inline-flex self-start items-center rounded-full bg-warning-50 dark:bg-warning-500/10 px-1.5 py-0.5 text-[10px] font-semibold text-warning-700 dark:text-warning-400">Wajib</span>
                        @endif
                    </span>
                </label>
                {{-- Hidden input untuk FIXED fields agar tetap terkirim meski checkbox disabled --}}
                @if($isFixed)
                <input type="hidden" name="fields[]" value="{{ $key }}">
                @endif
                @endforeach
            </div>
        </div>
        @endforeach
    </div>

    @if(!$config->is_locked)
    <div class="mt-6 flex items-center justify-between">
        <p class="text-xs text-gray-400 dark:text-gray-500">
            Konfigurasi akan terkunci otomatis saat peserta pertama submit formulir.
        </p>
        <button type="submit"
            class="rounded-lg bg-brand-500 px-6 py-2.5 text-sm font-semibold text-white hover:bg-brand-600 transition-colors focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2">
            Simpan Konfigurasi
        </button>
    </div>
    @else
    <div class="mt-6">
        <p class="text-xs text-error-500 dark:text-error-400">
            Konfigurasi terkunci. Reset data peserta terlebih dahulu jika ingin mengubah.
        </p>
    </div>
    @endif
</form>
@endsection
