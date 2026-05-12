<?php

use App\Http\Controllers\Admin\AsalSekolahController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\StatistikController as AdminStatistikController;
use App\Http\Controllers\Admin\PengumumanController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\BerkasViewController;
use App\Http\Controllers\Admin\NilaiRekapController;
use App\Http\Controllers\Admin\SeleksiController;
use App\Http\Controllers\Admin\PesertaController as AdminPesertaController;
use App\Http\Controllers\Admin\JalurPendaftaranController;
use App\Http\Controllers\Admin\JurusanController;
use App\Http\Controllers\Admin\MataPelajaranController;
use App\Http\Controllers\Admin\TahunPenerimaanController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Peserta\DashboardController as PesertaDashboardController;
use App\Http\Controllers\Peserta\FormulirController;
use App\Http\Controllers\Peserta\FormulirPdfController;
use App\Http\Controllers\Peserta\BerkasController;
use App\Http\Controllers\Peserta\NilaiController;
use App\Http\Controllers\Peserta\KeluargaController;
use App\Http\Controllers\FormConfigController;
use App\Http\Controllers\PengumumanPublikController;
use App\Http\Controllers\Panitia\AsalSekolahController as PanitiaAsalSekolahController;
use App\Http\Controllers\Panitia\DashboardController as PanitiaDashboardController;
use App\Http\Controllers\Panitia\JalurKuotaController;
use Illuminate\Support\Facades\Route;

// ── Publik ──────────────────────────────────────────────────────────────
Route::get('/', function () {
    if (\Illuminate\Support\Facades\Auth::check()) {
        return match (\Illuminate\Support\Facades\Auth::user()->role) {
            'admin'   => redirect()->route('admin.dashboard'),
            'panitia' => redirect()->route('panitia.dashboard'),
            default   => redirect()->route('peserta.dashboard'),
        };
    }

    $tahunAktif  = \App\Models\TahunPenerimaan::where('is_active', true)->first();
    $pengumuman  = \App\Models\Pengumuman::published()->latest('tanggal_publish')->take(5)->get();
    $jalur       = $tahunAktif
        ? \App\Models\JalurPendaftaran::where('tahun_penerimaan_id', $tahunAktif->id)
            ->where('is_active', true)
            ->orderBy('nama')
            ->get()
        : collect();
    $jurusan     = \App\Models\Jurusan::orderBy('nama')->get();

    return view('welcome', compact('pengumuman', 'jalur', 'jurusan', 'tahunAktif'));
})->name('home');

Route::get('/pengumuman', [PengumumanPublikController::class, 'index'])->name('pengumuman.index');
Route::get('/pengumuman/{pengumuman}', [PengumumanPublikController::class, 'show'])->name('pengumuman.show');

// ── Auth ─────────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// ── Register Peserta ─────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/daftar', [RegisterController::class, 'showForm'])->name('register');
    Route::post('/daftar', [RegisterController::class, 'register']);
});

// ── Dashboard Admin ──────────────────────────────────────────────────────
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/statistik', [AdminStatistikController::class, 'index'])->name('statistik.index');
    Route::get('/activity-log', [ActivityLogController::class, 'index'])->name('activity-log.index');
    Route::get('/settings', [SettingController::class, 'edit'])->name('settings.edit');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
    Route::resource('pengumuman', PengumumanController::class)->except(['show']);
    Route::resource('jurusan', JurusanController::class);
    Route::resource('tahun-penerimaan', TahunPenerimaanController::class)->except(['show']);
    Route::patch('tahun-penerimaan/{tahunPenerimaan}/activate', [TahunPenerimaanController::class, 'activate'])
        ->name('tahun-penerimaan.activate');
    Route::resource('jalur-pendaftaran', JalurPendaftaranController::class)->except(['show']);
    Route::patch('jalur-pendaftaran/{jalurPendaftaran}/toggle-aktif', [JalurPendaftaranController::class, 'toggleAktif'])
        ->name('jalur-pendaftaran.toggle-aktif');
    Route::resource('mata-pelajaran', MataPelajaranController::class)->except(['show']);
    Route::patch('mata-pelajaran/{mataPelajaran}/toggle-aktif', [MataPelajaranController::class, 'toggleAktif'])
        ->name('mata-pelajaran.toggle-aktif');
    Route::get('asal-sekolah/export', [AsalSekolahController::class, 'export'])->name('asal-sekolah.export');
    Route::post('asal-sekolah/import', [AsalSekolahController::class, 'import'])->name('asal-sekolah.import');
    Route::resource('asal-sekolah', AsalSekolahController::class)->except(['show']);
    Route::get('tahun-penerimaan/{tahunPenerimaan}/form-config', [FormConfigController::class, 'edit'])
        ->name('tahun-penerimaan.form-config.edit');
    Route::put('tahun-penerimaan/{tahunPenerimaan}/form-config', [FormConfigController::class, 'update'])
        ->name('tahun-penerimaan.form-config.update');
    Route::get('peserta/export', [AdminPesertaController::class, 'export'])->name('peserta.export');
    Route::get('peserta/create', [AdminPesertaController::class, 'create'])->name('peserta.create');
    Route::post('peserta', [AdminPesertaController::class, 'store'])->name('peserta.store');
    Route::patch('peserta/{peserta}/verifikasi', [AdminPesertaController::class, 'updateVerifikasi'])->name('peserta.verifikasi');
    Route::patch('peserta/{peserta}/hasil', [AdminPesertaController::class, 'updateHasil'])->name('peserta.hasil');
    Route::patch('peserta/{peserta}/daftar-ulang', [AdminPesertaController::class, 'updateDaftarUlang'])->name('peserta.daftar-ulang');
    Route::patch('peserta/{peserta}/reset-password', [AdminPesertaController::class, 'resetPassword'])->name('peserta.reset-password');
    Route::delete('peserta/{peserta}', [AdminPesertaController::class, 'destroy'])->name('peserta.destroy');
    Route::get('peserta/{peserta}/edit', [AdminPesertaController::class, 'edit'])->name('peserta.edit');
    Route::put('peserta/{peserta}', [AdminPesertaController::class, 'update'])->name('peserta.update');
    Route::get('peserta/{peserta}/pdf', [FormulirPdfController::class, 'download'])->name('peserta.pdf');
    Route::get('peserta/{peserta}', [AdminPesertaController::class, 'show'])->name('peserta.show');
    Route::get('peserta', [AdminPesertaController::class, 'index'])->name('peserta.index');
    Route::get('nilai-rekap/export', [NilaiRekapController::class, 'export'])->name('nilai-rekap.export');
    Route::get('nilai-rekap', [NilaiRekapController::class, 'index'])->name('nilai-rekap.index');
    Route::get('berkas/{berkas}/download', [BerkasViewController::class, 'download'])->name('berkas.download');

    Route::get('seleksi', [SeleksiController::class, 'index'])->name('seleksi.index');
    Route::post('seleksi/hitung-semua', [SeleksiController::class, 'hitungSemua'])->name('seleksi.hitung-semua');
    Route::post('seleksi/terapkan-hasil', [SeleksiController::class, 'terapkanHasil'])->name('seleksi.terapkan-hasil');

    Route::get('pengguna', [UserController::class, 'index'])->name('pengguna.index');
    Route::get('pengguna/create', [UserController::class, 'create'])->name('pengguna.create');
    Route::post('pengguna', [UserController::class, 'store'])->name('pengguna.store');
    Route::get('pengguna/{pengguna}/edit', [UserController::class, 'edit'])->name('pengguna.edit');
    Route::put('pengguna/{pengguna}', [UserController::class, 'update'])->name('pengguna.update');
    Route::patch('pengguna/{pengguna}/toggle-aktif', [UserController::class, 'toggleAktif'])->name('pengguna.toggle-aktif');
    Route::patch('pengguna/{pengguna}/reset-password', [UserController::class, 'resetPassword'])->name('pengguna.reset-password');
    Route::delete('pengguna/{pengguna}', [UserController::class, 'destroy'])->name('pengguna.destroy');
});

// ── Panitia: Manajemen Kuota Jalur ───────────────────────────────────────
Route::middleware(['auth', 'role:panitia'])->prefix('panitia')->name('panitia.')->group(function () {
    Route::patch('jalur-pendaftaran/{jalurPendaftaran}/kuota', [JalurKuotaController::class, 'update'])
        ->name('jalur-pendaftaran.kuota');
});

// ── Dashboard Panitia ────────────────────────────────────────────────────
Route::middleware(['auth', 'role:panitia'])->prefix('panitia')->name('panitia.')->group(function () {
    Route::get('/dashboard', [PanitiaDashboardController::class, 'index'])->name('dashboard');
    Route::get('/statistik', [AdminStatistikController::class, 'index'])->name('statistik.index');
    Route::get('/settings', [SettingController::class, 'edit'])->name('settings.edit');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
    Route::get('asal-sekolah/export', [PanitiaAsalSekolahController::class, 'export'])->name('asal-sekolah.export');
    Route::post('asal-sekolah/import', [PanitiaAsalSekolahController::class, 'import'])->name('asal-sekolah.import');
    Route::resource('asal-sekolah', PanitiaAsalSekolahController::class)->except(['show']);
    Route::get('tahun-penerimaan/{tahunPenerimaan}/form-config', [FormConfigController::class, 'edit'])
        ->name('tahun-penerimaan.form-config.edit');
    Route::put('tahun-penerimaan/{tahunPenerimaan}/form-config', [FormConfigController::class, 'update'])
        ->name('tahun-penerimaan.form-config.update');
    Route::get('peserta/export', [AdminPesertaController::class, 'export'])->name('peserta.export');
    Route::get('peserta/create', [AdminPesertaController::class, 'create'])->name('peserta.create');
    Route::post('peserta', [AdminPesertaController::class, 'store'])->name('peserta.store');
    Route::patch('peserta/{peserta}/verifikasi', [AdminPesertaController::class, 'updateVerifikasi'])->name('peserta.verifikasi');
    Route::patch('peserta/{peserta}/hasil', [AdminPesertaController::class, 'updateHasil'])->name('peserta.hasil');
    Route::patch('peserta/{peserta}/daftar-ulang', [AdminPesertaController::class, 'updateDaftarUlang'])->name('peserta.daftar-ulang');
    Route::patch('peserta/{peserta}/reset-password', [AdminPesertaController::class, 'resetPassword'])->name('peserta.reset-password');
    Route::delete('peserta/{peserta}', [AdminPesertaController::class, 'destroy'])->name('peserta.destroy');
    Route::get('peserta/{peserta}/edit', [AdminPesertaController::class, 'edit'])->name('peserta.edit');
    Route::put('peserta/{peserta}', [AdminPesertaController::class, 'update'])->name('peserta.update');
    Route::get('peserta/{peserta}/pdf', [FormulirPdfController::class, 'download'])->name('peserta.pdf');
    Route::get('peserta/{peserta}', [AdminPesertaController::class, 'show'])->name('peserta.show');
    Route::get('peserta', [AdminPesertaController::class, 'index'])->name('peserta.index');
    Route::get('nilai-rekap/export', [NilaiRekapController::class, 'export'])->name('nilai-rekap.export');
    Route::get('nilai-rekap', [NilaiRekapController::class, 'index'])->name('nilai-rekap.index');
    Route::get('berkas/{berkas}/download', [BerkasViewController::class, 'download'])->name('berkas.download');
});

// ── Dashboard Peserta ────────────────────────────────────────────────────
Route::middleware(['auth', 'role:peserta'])->prefix('peserta')->name('peserta.')->group(function () {
    Route::get('/dashboard', [PesertaDashboardController::class, 'index'])->name('dashboard');
    Route::get('/formulir', [FormulirController::class, 'index'])->name('formulir.index');
    Route::post('/formulir', [FormulirController::class, 'store'])->name('formulir.store');
    Route::get('/keluarga', [KeluargaController::class, 'index'])->name('keluarga.index');
    Route::post('/keluarga', [KeluargaController::class, 'store'])->name('keluarga.store');
    Route::get('/berkas', [BerkasController::class, 'index'])->name('berkas.index');
    Route::post('/berkas', [BerkasController::class, 'store'])->name('berkas.store');
    Route::get('/nilai', [NilaiController::class, 'index'])->name('nilai.index');
    Route::post('/nilai', [NilaiController::class, 'store'])->name('nilai.store');
    Route::get('/formulir/pdf', [FormulirPdfController::class, 'downloadMine'])->name('formulir.pdf');
});

// ── Test routes (hapus sebelum production) ───────────────────────────────
Route::get('/test-guest', fn () => view('test.guest'));
Route::get('/test-auth-layout', fn () => view('test.auth'));
Route::get('/test-403', fn () => abort(403));
Route::get('/test-500', fn () => abort(500));
