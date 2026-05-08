@extends('layouts.guest')

@section('title', 'Selamat Datang')

@push('scripts')
<script>
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) target.scrollIntoView({ behavior: 'smooth' });
        });
    });
</script>
@endpush

@section('content')
@php
    use App\Models\Setting;
    use Illuminate\Support\Facades\Storage;
    $namaSekolah  = Setting::get('nama_sekolah', 'SMK Cyber Media Jakarta');
    $logoSekolah  = Setting::get('logo');
    $alamat       = Setting::get('alamat', 'Jl. Duren Tiga Raya No.12, Jakarta Selatan');
    $telepon      = Setting::get('telepon', '');
    $emailSekolah = Setting::get('email', '');

    $jalurIcons  = ['fa-user-check', 'fa-trophy', 'fa-users', 'fa-star', 'fa-medal'];
    $jalurIconBox = [
        'w-14 h-14 bg-blue-50 dark:bg-blue-900/30 rounded-2xl flex items-center justify-center text-blue-600 dark:text-blue-400 mb-6',
        'w-14 h-14 bg-indigo-50 dark:bg-indigo-900/30 rounded-2xl flex items-center justify-center text-indigo-600 dark:text-indigo-400 mb-6',
        'w-14 h-14 bg-emerald-50 dark:bg-emerald-900/30 rounded-2xl flex items-center justify-center text-emerald-600 dark:text-emerald-400 mb-6',
        'w-14 h-14 bg-violet-50 dark:bg-violet-900/30 rounded-2xl flex items-center justify-center text-violet-600 dark:text-violet-400 mb-6',
        'w-14 h-14 bg-amber-50 dark:bg-amber-900/30 rounded-2xl flex items-center justify-center text-amber-600 dark:text-amber-400 mb-6',
    ];

    $jurusanIcons      = ['fa-microchip', 'fa-code-branch', 'fa-bezier-curve', 'fa-gamepad', 'fa-network-wired'];
    $jurusanAccent     = ['text-indigo-600', 'text-violet-500', 'text-pink-500', 'text-purple-500', 'text-emerald-600'];
    $jurusanIconBox    = [
        'w-20 h-20 bg-white dark:bg-slate-800 rounded-3xl shadow-xl flex items-center justify-center text-indigo-600 z-10 transition-transform group-hover:scale-110 duration-300',
        'w-20 h-20 bg-white dark:bg-slate-800 rounded-3xl shadow-xl flex items-center justify-center text-violet-500 z-10 transition-transform group-hover:scale-110 duration-300',
        'w-20 h-20 bg-white dark:bg-slate-800 rounded-3xl shadow-xl flex items-center justify-center text-pink-500 z-10 transition-transform group-hover:scale-110 duration-300',
        'w-20 h-20 bg-white dark:bg-slate-800 rounded-3xl shadow-xl flex items-center justify-center text-purple-500 z-10 transition-transform group-hover:scale-110 duration-300',
        'w-20 h-20 bg-white dark:bg-slate-800 rounded-3xl shadow-xl flex items-center justify-center text-emerald-600 z-10 transition-transform group-hover:scale-110 duration-300',
    ];
    $jurusanBgLarge    = ['text-indigo-500/10', 'text-violet-500/10', 'text-pink-500/10', 'text-purple-500/10', 'text-emerald-500/10'];
@endphp

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    body { scroll-behavior: smooth; }
    .glass-nav { background: rgba(255,255,255,.85); backdrop-filter: blur(12px); }
    .dark .glass-nav { background: rgba(15,23,42,.85); }
    .hero-gradient {
        background: radial-gradient(ellipse 80% 60% at 50% -20%, rgba(99,102,241,.15) 0%, transparent 70%);
    }
    .dark .hero-gradient {
        background: radial-gradient(ellipse 80% 60% at 50% -20%, rgba(99,102,241,.12) 0%, transparent 70%);
    }
</style>

<div class="min-h-screen bg-slate-50 text-slate-900 dark:bg-slate-950 dark:text-slate-100 transition-colors duration-300" x-data>

    {{-- ===== NAVBAR ===== --}}
    <nav class="glass-nav fixed top-0 w-full z-50 border-b border-slate-200 dark:border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">

                {{-- Brand --}}
                <a href="/" class="flex items-center gap-3">
                    @if($logoSekolah && Storage::disk('public')->exists($logoSekolah))
                        <img src="{{ '/storage/' . $logoSekolah }}"
                             alt="Logo" class="h-10 w-10 rounded-xl object-contain shadow">
                    @else
                        <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-indigo-500/30">
                            <i class="fas fa-graduation-cap text-lg"></i>
                        </div>
                    @endif
                    <div class="hidden sm:block">
                        <span class="font-bold text-lg leading-tight text-slate-900 dark:text-white">PPDB</span>
                        <span class="block text-xs text-indigo-500 font-medium -mt-0.5">{{ $namaSekolah }}</span>
                    </div>
                </a>

                {{-- Actions --}}
                <div class="flex items-center gap-3">
                    <button @click="$store.theme.toggle()"
                        class="p-2 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors text-slate-500 dark:text-slate-400"
                        title="Toggle Dark Mode">
                        <i class="fas fa-moon dark:hidden text-sm"></i>
                        <i class="fas fa-sun hidden dark:block text-sm text-yellow-400"></i>
                    </button>

                    <a href="{{ route('login') }}"
                       class="hidden md:block text-sm font-medium text-slate-600 hover:text-indigo-600 dark:text-slate-400 dark:hover:text-indigo-400 transition-colors">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}"
                       class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl shadow-lg shadow-indigo-500/25 transition-all hover:scale-105 active:scale-95">
                        Daftar Sekarang
                    </a>
                </div>
            </div>
        </div>
    </nav>

    {{-- ===== HERO ===== --}}
    <section class="hero-gradient pt-32 pb-20 px-4">
        <div class="max-w-4xl mx-auto text-center space-y-8">

            <div class="inline-flex items-center gap-2 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 px-4 py-1.5 rounded-full text-sm font-medium border border-blue-100 dark:border-blue-800">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
                </span>
                @if(isset($tahunAktif) && $tahunAktif)
                    Pendaftaran {{ $tahunAktif->label ?? $tahunAktif->tahun }} Dibuka
                @else
                    Penerimaan Peserta Didik Baru
                @endif
            </div>

            <h1 class="text-4xl md:text-6xl font-extrabold tracking-tight text-slate-900 dark:text-white leading-tight">
                Bergabung dengan <br>
                <span class="text-indigo-600 dark:text-indigo-400">{{ $namaSekolah }}</span>
            </h1>

            <p class="text-lg md:text-xl text-slate-500 dark:text-slate-400 max-w-2xl mx-auto leading-relaxed">
                Wujudkan masa depan cerah di bidang teknologi dan kreativitas.
                Daftarkan diri sekarang dan jadilah bagian dari keluarga kami.
            </p>

            <div class="flex flex-col items-center gap-4 pt-4">
                <a href="{{ route('register') }}"
                   class="bg-indigo-600 hover:bg-indigo-700 text-white px-10 py-4 rounded-2xl font-bold text-lg shadow-xl shadow-indigo-500/30 transition-all flex items-center gap-3 group">
                    <i class="fas fa-user-plus group-hover:rotate-12 transition-transform"></i>
                    Daftar Sekarang
                </a>
                <a href="{{ route('login') }}"
                   class="text-slate-500 hover:text-indigo-600 font-medium underline underline-offset-4 decoration-slate-300 transition-all">
                    Sudah Punya Akun? Masuk di sini
                </a>
            </div>
        </div>
    </section>

    {{-- ===== JALUR PENDAFTARAN ===== --}}
    @if(isset($jalur) && $jalur->isNotEmpty())
    <section class="py-20 bg-slate-100/50 dark:bg-slate-900/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-slate-900 dark:text-white mb-3">Jalur Pendaftaran</h2>
                <p class="text-slate-500 dark:text-slate-400">Pilih jalur yang sesuai dengan kondisi kamu</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($jalur as $idx => $j)
                @php
                    $ic      = $jalurIcons[$idx % count($jalurIcons)];
                    $iconBox = $jalurIconBox[$idx % count($jalurIconBox)];
                @endphp
                <div class="bg-white dark:bg-slate-800 p-8 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-700 transition-all hover:shadow-xl hover:-translate-y-1 duration-300">
                    <div class="{{ $iconBox }}">
                        <i class="fas {{ $ic }} text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">{{ $j->nama }}</h3>
                    @if($j->deskripsi)
                        <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed">{{ $j->deskripsi }}</p>
                    @endif
                    @if($j->persentase_kuota)
                        <p class="mt-4 inline-flex items-center gap-1 text-xs font-semibold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/30 px-3 py-1 rounded-full">
                            <i class="fas fa-chart-pie text-[10px]"></i> Kuota {{ $j->persentase_kuota }}%
                        </p>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- ===== PROGRAM KEAHLIAN ===== --}}
    @if(isset($jurusan) && $jurusan->isNotEmpty())
    <section class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-slate-900 dark:text-white mb-3">Program Keahlian</h2>
                <p class="text-slate-500 dark:text-slate-400">Temukan jurusan yang sesuai dengan minat dan bakatmu</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($jurusan as $idx => $j)
                @php
                    $ic      = $jurusanIcons[$idx % count($jurusanIcons)];
                    $acc     = $jurusanAccent[$idx % count($jurusanAccent)];
                    $icoBox  = $jurusanIconBox[$idx % count($jurusanIconBox)];
                    $bgL     = $jurusanBgLarge[$idx % count($jurusanBgLarge)];
                @endphp
                <div class="group relative bg-white dark:bg-slate-800 rounded-3xl overflow-hidden border border-slate-200 dark:border-slate-700 shadow-sm transition-all hover:shadow-2xl duration-300">
                    <div class="h-48 bg-slate-100 dark:bg-slate-700 flex items-center justify-center relative overflow-hidden">
                        <i class="fas {{ $ic }} text-7xl {{ $bgL }} absolute scale-150 rotate-12"></i>
                        <div class="{{ $icoBox }}">
                            <i class="fas {{ $ic }} text-3xl"></i>
                        </div>
                    </div>
                    <div class="p-8 text-center">
                        <span class="text-xs font-bold tracking-widest {{ $acc }} uppercase mb-2 block">{{ $j->kode }}</span>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-4 px-4 leading-snug">{{ $j->nama }}</h3>
                        <div class="flex flex-col gap-2 pt-4 border-t border-slate-100 dark:border-slate-700 mt-4">
                            @if($j->kapasitas)
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-slate-400">Kapasitas:</span>
                                <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $j->kapasitas }} siswa</span>
                            </div>
                            @endif
                            @if($j->deskripsi)
                            <div class="text-sm text-slate-500 dark:text-slate-400 line-clamp-2 text-left">{{ $j->deskripsi }}</div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- ===== PENGUMUMAN ===== --}}
    @if(isset($pengumuman) && $pengumuman->isNotEmpty())
    <section class="py-20 bg-slate-50 dark:bg-slate-950/50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6">
            <div class="flex justify-between items-end mb-8">
                <div>
                    <h2 class="text-3xl font-bold text-slate-900 dark:text-white mb-2">Pengumuman</h2>
                    <p class="text-slate-500 dark:text-slate-400">Informasi terbaru dari sekolah</p>
                </div>
                <a href="{{ route('pengumuman.index') }}"
                   class="text-indigo-600 dark:text-indigo-400 font-semibold flex items-center gap-2 hover:gap-3 transition-all text-sm">
                    Lihat semua <i class="fas fa-arrow-right text-xs"></i>
                </a>
            </div>

            <div class="space-y-4">
                @foreach($pengumuman as $item)
                <a href="{{ route('pengumuman.show', $item) }}"
                   class="block bg-white dark:bg-slate-800 p-6 md:p-8 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4 transition-all hover:border-indigo-500/50 hover:shadow-md duration-200">
                    <div class="space-y-1 flex-1">
                        <h4 class="text-lg font-bold text-slate-900 dark:text-white">{{ $item->judul }}</h4>
                        <p class="text-slate-500 dark:text-slate-400 text-sm line-clamp-2">
                            {!! Str::limit(strip_tags($item->isi), 120) !!}
                        </p>
                    </div>
                    <div class="shrink-0 text-sm font-medium text-slate-400 bg-slate-50 dark:bg-slate-900 px-4 py-2 rounded-xl whitespace-nowrap">
                        {{ $item->tanggal_publish?->translatedFormat('d M Y') ?? $item->created_at->translatedFormat('d M Y') }}
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- ===== CTA BANNER ===== --}}
    <section class="py-20 px-4">
        <div class="max-w-5xl mx-auto bg-indigo-600 rounded-[2.5rem] p-12 text-center text-white relative overflow-hidden shadow-2xl shadow-indigo-500/40">
            <div class="absolute -top-24 -left-24 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-24 -right-24 w-64 h-64 bg-indigo-400/20 rounded-full blur-3xl"></div>
            <div class="relative z-10 space-y-6">
                <h2 class="text-4xl md:text-5xl font-bold">Siap Bergabung?</h2>
                <p class="text-indigo-100 text-lg max-w-xl mx-auto">
                    Daftarkan diri sekarang sebelum kuota habis dan dapatkan kesempatan terbaik di gelombang pertama.
                </p>
                <div class="pt-4">
                    <a href="{{ route('register') }}"
                       class="inline-flex items-center gap-3 bg-white text-indigo-600 hover:bg-slate-100 px-10 py-4 rounded-2xl font-bold text-lg shadow-xl transition-all group">
                        Mulai Pendaftaran
                        <i class="fas fa-arrow-right group-hover:translate-x-2 transition-transform"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- ===== FOOTER ===== --}}
    <footer class="py-16 border-t border-slate-200 dark:border-slate-800 text-center">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 space-y-10">

            <div class="flex flex-col items-center space-y-4">
                @if($logoSekolah && Storage::disk('public')->exists($logoSekolah))
                    <img src="{{ '/storage/' . $logoSekolah }}"
                         alt="Logo" class="w-16 h-16 rounded-2xl object-contain border border-slate-200 dark:border-slate-700 shadow">
                @else
                    <div class="w-16 h-16 bg-slate-100 dark:bg-slate-800 rounded-2xl flex items-center justify-center text-indigo-600 border border-slate-200 dark:border-slate-700">
                        <i class="fas fa-school text-3xl"></i>
                    </div>
                @endif
                <h3 class="text-2xl font-bold text-slate-900 dark:text-white">PPDB {{ $namaSekolah }}</h3>
                @if($alamat)
                    <p class="text-slate-500 max-w-md mx-auto text-sm">{{ $alamat }}</p>
                @endif
            </div>

            <div class="flex flex-col md:flex-row justify-center items-center gap-6 text-slate-500 text-sm font-medium">
                @if($telepon)
                    <a href="tel:{{ $telepon }}" class="flex items-center gap-2 hover:text-indigo-600 transition-colors">
                        <i class="fas fa-phone-alt"></i> {{ $telepon }}
                    </a>
                    <span class="hidden md:block w-1.5 h-1.5 bg-slate-300 rounded-full"></span>
                @endif
                @if($emailSekolah)
                    <a href="mailto:{{ $emailSekolah }}" class="flex items-center gap-2 hover:text-indigo-600 transition-colors">
                        <i class="fas fa-envelope"></i> {{ $emailSekolah }}
                    </a>
                @endif
            </div>

            <div class="pt-10 border-t border-slate-100 dark:border-slate-800">
                <div class="flex justify-center gap-6 mb-8 text-slate-400 text-xl">
                    <a href="#" class="hover:text-indigo-600 transition-colors"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="hover:text-indigo-600 transition-colors"><i class="fab fa-facebook"></i></a>
                    <a href="#" class="hover:text-indigo-600 transition-colors"><i class="fab fa-youtube"></i></a>
                    <a href="#" class="hover:text-indigo-600 transition-colors"><i class="fab fa-tiktok"></i></a>
                </div>
                <p class="text-slate-400 text-sm">
                    &copy; {{ date('Y') }} {{ $namaSekolah }}. Hak cipta dilindungi.
                </p>
            </div>
        </div>
    </footer>

</div>
@endsection
