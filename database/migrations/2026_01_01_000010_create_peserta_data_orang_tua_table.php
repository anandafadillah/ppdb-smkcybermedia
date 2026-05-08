<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peserta_data_ayah', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peserta_id')->constrained('peserta')->cascadeOnDelete();
            $table->string('nama')->nullable();
            $table->string('nik', 16)->nullable();
            $table->year('tahun_lahir')->nullable();
            $table->string('pendidikan')->nullable();
            $table->string('pekerjaan')->nullable();
            $table->string('penghasilan')->nullable();
            $table->string('ketidakmampuan_khusus')->nullable();
            $table->timestamps();
        });

        Schema::create('peserta_data_ibu', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peserta_id')->constrained('peserta')->cascadeOnDelete();
            $table->string('nama')->nullable();
            $table->string('nik', 16)->nullable();
            $table->year('tahun_lahir')->nullable();
            $table->string('pendidikan')->nullable();
            $table->string('pekerjaan')->nullable();
            $table->string('penghasilan')->nullable();
            $table->string('ketidakmampuan_khusus')->nullable();
            $table->timestamps();
        });

        Schema::create('peserta_data_wali', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peserta_id')->constrained('peserta')->cascadeOnDelete();
            $table->string('nama')->nullable();
            $table->string('nik', 16)->nullable();
            $table->year('tahun_lahir')->nullable();
            $table->string('pekerjaan')->nullable();
            $table->string('penghasilan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peserta_data_wali');
        Schema::dropIfExists('peserta_data_ibu');
        Schema::dropIfExists('peserta_data_ayah');
    }
};
