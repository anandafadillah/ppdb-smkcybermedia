<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peserta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('tahun_penerimaan_id')->constrained('tahun_penerimaan')->cascadeOnDelete();
            $table->foreignId('jalur_id')->nullable()->constrained('jalur_pendaftaran')->nullOnDelete();
            $table->foreignId('jurusan_id')->nullable()->constrained('jurusan')->nullOnDelete();
            $table->string('no_pendaftaran', 20)->nullable()->unique();
            $table->enum('status_formulir', ['draft', 'submitted'])->default('draft');
            $table->enum('status_verifikasi', ['belum_diverifikasi', 'terverifikasi', 'ditolak'])->default('belum_diverifikasi');
            $table->enum('status_hasil', ['belum', 'lolos', 'tidak_lolos', 'cadangan'])->default('belum');
            $table->enum('status_daftar_ulang', ['belum', 'sudah'])->default('belum');
            $table->timestamps();

            $table->unique(['user_id', 'tahun_penerimaan_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peserta');
    }
};
