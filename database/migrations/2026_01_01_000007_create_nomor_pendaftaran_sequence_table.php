<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nomor_pendaftaran_sequence', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jalur_id')->constrained('jalur_pendaftaran')->cascadeOnDelete();
            $table->foreignId('tahun_penerimaan_id')->constrained('tahun_penerimaan')->cascadeOnDelete();
            $table->enum('mode', ['daring', 'luring'])->default('daring');
            $table->unsignedSmallInteger('last_sequence')->default(0);
            $table->timestamps();

            $table->unique(['jalur_id', 'tahun_penerimaan_id', 'mode'], 'nps_jalur_tahun_mode_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nomor_pendaftaran_sequence');
    }
};
