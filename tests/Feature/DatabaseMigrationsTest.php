<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DatabaseMigrationsTest extends TestCase
{
    use RefreshDatabase;

    // Cycle 1 — All required tables exist
    public function test_all_required_tables_exist(): void
    {
        $tables = [
            'users', 'tahun_penerimaan', 'jalur_pendaftaran', 'jurusan',
            'asal_sekolah', 'mata_pelajaran', 'form_configs',
            'nomor_pendaftaran_sequence', 'peserta', 'peserta_data_diri',
            'peserta_data_ayah', 'peserta_data_ibu', 'peserta_data_wali',
            'peserta_data_alamat', 'peserta_data_kip', 'peserta_nilai',
            'peserta_berkas', 'pengumuman', 'activity_logs', 'settings',
        ];

        foreach ($tables as $table) {
            $this->assertTrue(Schema::hasTable($table), "Tabel '{$table}' tidak ditemukan");
        }
    }

    // Cycle 2 — users table has nisn, email (nullable), role
    public function test_users_table_has_nisn_email_role_columns(): void
    {
        $this->assertTrue(Schema::hasColumn('users', 'nisn'));
        $this->assertTrue(Schema::hasColumn('users', 'email'));
        $this->assertTrue(Schema::hasColumn('users', 'role'));
        $this->assertTrue(Schema::hasColumn('users', 'is_active'));
    }

    // Cycle 3 — users nisn and email accept null
    public function test_users_nisn_and_email_are_nullable(): void
    {
        \DB::table('users')->insert([
            'name'       => 'Admin Test',
            'nisn'       => null,
            'email'      => 'admin@test.com',
            'password'   => bcrypt('password'),
            'role'       => 'admin',
            'is_active'  => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        \DB::table('users')->insert([
            'name'       => 'Peserta Test',
            'nisn'       => '1234567890',
            'email'      => null,
            'password'   => bcrypt('password'),
            'role'       => 'peserta',
            'is_active'  => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->assertEquals(2, \DB::table('users')->count());
    }

    // Cycle 4 — users nisn unique constraint
    public function test_users_nisn_unique_constraint(): void
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        \DB::table('users')->insert([
            'name' => 'Peserta A', 'nisn' => '1234567890', 'email' => null,
            'password' => bcrypt('p'), 'role' => 'peserta', 'is_active' => true,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        \DB::table('users')->insert([
            'name' => 'Peserta B', 'nisn' => '1234567890', 'email' => null,
            'password' => bcrypt('p'), 'role' => 'peserta', 'is_active' => true,
            'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    // Cycle 5 — peserta table has all status columns
    public function test_peserta_table_has_status_columns(): void
    {
        $columns = ['status_formulir', 'status_verifikasi', 'status_hasil', 'status_daftar_ulang'];
        foreach ($columns as $col) {
            $this->assertTrue(Schema::hasColumn('peserta', $col), "Kolom '{$col}' tidak ada di tabel peserta");
        }
        $this->assertTrue(Schema::hasColumn('peserta', 'no_pendaftaran'));
        $this->assertTrue(Schema::hasColumn('peserta', 'user_id'));
        $this->assertTrue(Schema::hasColumn('peserta', 'tahun_penerimaan_id'));
        $this->assertTrue(Schema::hasColumn('peserta', 'jalur_id'));
        $this->assertTrue(Schema::hasColumn('peserta', 'jurusan_id'));
    }

    // Cycle 6 — peserta valid status values are insertable
    public function test_peserta_valid_status_values(): void
    {
        $userId = \DB::table('users')->insertGetId([
            'name' => 'Peserta', 'nisn' => '1234567890', 'email' => null,
            'password' => bcrypt('p'), 'role' => 'peserta', 'is_active' => true,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $tahunId = \DB::table('tahun_penerimaan')->insertGetId([
            'tahun' => '2025/2026', 'label' => 'TA 2025/2026', 'is_active' => true,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $jurusanId = \DB::table('jurusan')->insertGetId([
            'kode' => 'TJKT', 'nama' => 'Teknik Jaringan Komputer',
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $jalurId = \DB::table('jalur_pendaftaran')->insertGetId([
            'tahun_penerimaan_id' => $tahunId, 'nama' => 'Afirmasi',
            'is_active' => true, 'persentase_kuota' => 30,
            'kode_awal_daring' => 'AFI-D', 'kode_awal_luring' => 'AFI-L',
            'created_at' => now(), 'updated_at' => now(),
        ]);

        \DB::table('peserta')->insert([
            'user_id'            => $userId,
            'tahun_penerimaan_id'=> $tahunId,
            'jalur_id'           => $jalurId,
            'jurusan_id'         => $jurusanId,
            'no_pendaftaran'     => null,
            'status_formulir'    => 'draft',
            'status_verifikasi'  => 'belum_diverifikasi',
            'status_hasil'       => 'belum',
            'status_daftar_ulang'=> 'belum',
            'created_at'         => now(),
            'updated_at'         => now(),
        ]);

        $this->assertEquals(1, \DB::table('peserta')->count());
    }

    // Cycle 7 — peserta_nilai and peserta_berkas structure
    public function test_peserta_nilai_and_berkas_structure(): void
    {
        $this->assertTrue(Schema::hasColumn('peserta_nilai', 'peserta_id'));
        $this->assertTrue(Schema::hasColumn('peserta_nilai', 'mata_pelajaran_id'));
        $this->assertTrue(Schema::hasColumn('peserta_nilai', 'semester'));
        $this->assertTrue(Schema::hasColumn('peserta_nilai', 'nilai'));

        $this->assertTrue(Schema::hasColumn('peserta_berkas', 'peserta_id'));
        $this->assertTrue(Schema::hasColumn('peserta_berkas', 'tipe_berkas'));
        $this->assertTrue(Schema::hasColumn('peserta_berkas', 'file_path'));
    }

    // Cycle 8 — Admin seeder creates at least one admin account
    public function test_database_seeder_creates_admin(): void
    {
        $this->seed(\Database\Seeders\DatabaseSeeder::class);

        $admin = \DB::table('users')->where('role', 'admin')->first();
        $this->assertNotNull($admin, 'Seeder harus membuat minimal satu akun admin');
        $this->assertEquals('admin', $admin->role);
        $this->assertTrue((bool) $admin->is_active);
    }
}
