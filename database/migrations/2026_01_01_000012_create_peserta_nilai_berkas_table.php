<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peserta_nilai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peserta_id')->constrained('peserta')->cascadeOnDelete();
            $table->foreignId('mata_pelajaran_id')->constrained('mata_pelajaran')->cascadeOnDelete();
            $table->unsignedTinyInteger('semester');
            $table->decimal('nilai', 5, 2)->nullable();
            $table->timestamps();

            $table->unique(['peserta_id', 'mata_pelajaran_id', 'semester']);
        });

        Schema::create('peserta_berkas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peserta_id')->constrained('peserta')->cascadeOnDelete();
            $table->string('tipe_berkas', 50);
            $table->string('file_path')->nullable();
            $table->string('mime_type', 50)->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peserta_berkas');
        Schema::dropIfExists('peserta_nilai');
    }
};
