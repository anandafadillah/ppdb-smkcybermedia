<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peserta_skor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peserta_id')->unique()->constrained('peserta')->cascadeOnDelete();
            $table->foreignId('jurusan_id')->nullable()->constrained('jurusan')->nullOnDelete();
            $table->foreignId('jalur_id')->nullable()->constrained('jalur_pendaftaran')->nullOnDelete();
            $table->decimal('skor_nilai', 5, 2)->nullable();
            $table->decimal('skor_umur', 5, 2)->nullable();
            $table->decimal('skor_total', 5, 2)->nullable();
            $table->unsignedTinyInteger('bobot_nilai_snapshot')->default(70);
            $table->unsignedTinyInteger('bobot_umur_snapshot')->default(30);
            $table->unsignedTinyInteger('umur_saat_dihitung')->nullable();
            $table->unsignedInteger('ranking')->nullable();
            $table->timestamp('calculated_at')->nullable();
            $table->timestamps();
        });

        DB::table('settings')->insertOrIgnore([
            ['key' => 'seleksi_bobot_nilai', 'value' => '70', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'seleksi_bobot_umur',  'value' => '30', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'seleksi_umur_min',    'value' => '15', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'seleksi_umur_max',    'value' => '21', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('peserta_skor');

        DB::table('settings')->whereIn('key', [
            'seleksi_bobot_nilai', 'seleksi_bobot_umur',
            'seleksi_umur_min', 'seleksi_umur_max',
        ])->delete();
    }
};
