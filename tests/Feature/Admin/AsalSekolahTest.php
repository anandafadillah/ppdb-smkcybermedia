<?php

namespace Tests\Feature\Admin;

use App\Models\AsalSekolah;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AsalSekolahTest extends TestCase
{
    use RefreshDatabase;

    // ──────────────────────────────────────────────
    // Siklus 2: Admin membuat asal sekolah baru
    // ──────────────────────────────────────────────

    public function test_admin_dapat_membuat_asal_sekolah_baru(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post('/admin/asal-sekolah', [
            'npsn'      => '20100001',
            'nama'      => 'SMP Negeri 1 Jakarta',
            'alamat'    => 'Jl. Contoh No. 1',
            'kelurahan' => 'Gambir',
            'kecamatan' => 'Gambir',
            'status'    => 'negeri',
        ]);

        $response->assertRedirect('/admin/asal-sekolah');
        $this->assertDatabaseHas('asal_sekolah', [
            'npsn'  => '20100001',
            'nama'  => 'SMP Negeri 1 Jakarta',
            'status' => 'negeri',
        ]);
    }

    public function test_npsn_yang_sudah_ada_ditolak_saat_membuat_baru(): void
    {
        $admin = User::factory()->admin()->create();
        AsalSekolah::factory()->create(['npsn' => '20100001']);

        $response = $this->actingAs($admin)->post('/admin/asal-sekolah', [
            'npsn'   => '20100001',
            'nama'   => 'Sekolah Lain',
            'status' => 'swasta',
        ]);

        $response->assertSessionHasErrors('npsn');
    }

    // ──────────────────────────────────────────────
    // Siklus 3: Admin mengedit asal sekolah
    // ──────────────────────────────────────────────

    public function test_admin_dapat_mengedit_asal_sekolah(): void
    {
        $admin   = User::factory()->admin()->create();
        $sekolah = AsalSekolah::factory()->create(['nama' => 'Nama Lama', 'npsn' => '20100001']);

        $response = $this->actingAs($admin)->put("/admin/asal-sekolah/{$sekolah->id}", [
            'npsn'      => '20100001',
            'nama'      => 'SMP Negeri 1 Jakarta (Updated)',
            'status'    => 'negeri',
            'kecamatan' => 'Gambir',
        ]);

        $response->assertRedirect('/admin/asal-sekolah');
        $this->assertDatabaseHas('asal_sekolah', [
            'id'   => $sekolah->id,
            'nama' => 'SMP Negeri 1 Jakarta (Updated)',
        ]);
    }

    // ──────────────────────────────────────────────
    // Siklus 4: Admin menghapus asal sekolah
    // ──────────────────────────────────────────────

    public function test_admin_dapat_menghapus_asal_sekolah(): void
    {
        $admin   = User::factory()->admin()->create();
        $sekolah = AsalSekolah::factory()->create();

        $response = $this->actingAs($admin)->delete("/admin/asal-sekolah/{$sekolah->id}");

        $response->assertRedirect('/admin/asal-sekolah');
        $this->assertDatabaseMissing('asal_sekolah', ['id' => $sekolah->id]);
    }

    // ──────────────────────────────────────────────
    // Siklus 5: Panitia dapat CRUD, peserta tidak
    // ──────────────────────────────────────────────

    public function test_panitia_dapat_melihat_dan_membuat_asal_sekolah(): void
    {
        $panitia = User::factory()->panitia()->create();
        AsalSekolah::factory()->create(['nama' => 'SMP Contoh', 'npsn' => '20100001']);

        $listResponse = $this->actingAs($panitia)->get('/panitia/asal-sekolah');
        $listResponse->assertOk();
        $listResponse->assertSee('SMP Contoh');

        $createResponse = $this->actingAs($panitia)->post('/panitia/asal-sekolah', [
            'npsn'   => '20100099',
            'nama'   => 'SMP Baru',
            'status' => 'swasta',
        ]);
        $createResponse->assertRedirect('/panitia/asal-sekolah');
        $this->assertDatabaseHas('asal_sekolah', ['npsn' => '20100099']);
    }

    public function test_peserta_tidak_bisa_mengakses_manajemen_asal_sekolah(): void
    {
        $peserta = User::factory()->peserta()->create();

        $this->actingAs($peserta)->get('/admin/asal-sekolah')->assertForbidden();
        $this->actingAs($peserta)->get('/panitia/asal-sekolah')->assertForbidden();
    }

    // ──────────────────────────────────────────────
    // Siklus 6: Search berdasarkan nama atau NPSN
    // ──────────────────────────────────────────────

    public function test_list_dapat_dicari_berdasarkan_nama(): void
    {
        $admin = User::factory()->admin()->create();
        AsalSekolah::factory()->create(['nama' => 'SMP Negeri Satu', 'npsn' => '20100001']);
        AsalSekolah::factory()->create(['nama' => 'SMA Swasta Dua',  'npsn' => '20100002']);

        $response = $this->actingAs($admin)->get('/admin/asal-sekolah?search=Negeri');

        $response->assertOk();
        $response->assertSee('SMP Negeri Satu');
        $response->assertDontSee('SMA Swasta Dua');
    }

    public function test_list_dapat_dicari_berdasarkan_npsn(): void
    {
        $admin = User::factory()->admin()->create();
        AsalSekolah::factory()->create(['nama' => 'SMP Negeri Satu', 'npsn' => '20100001']);
        AsalSekolah::factory()->create(['nama' => 'SMA Swasta Dua',  'npsn' => '20100002']);

        $response = $this->actingAs($admin)->get('/admin/asal-sekolah?search=20100002');

        $response->assertOk();
        $response->assertSee('SMA Swasta Dua');
        $response->assertDontSee('SMP Negeri Satu');
    }

    // ──────────────────────────────────────────────
    // Siklus 7: Export Excel
    // ──────────────────────────────────────────────

    public function test_admin_dapat_export_asal_sekolah_ke_excel(): void
    {
        $admin = User::factory()->admin()->create();
        AsalSekolah::factory()->create(['nama' => 'SMP Negeri Export', 'npsn' => '20100001']);

        $response = $this->actingAs($admin)->get('/admin/asal-sekolah/export');

        $response->assertOk();
        $response->assertHeader('Content-Disposition');
    }

    // ──────────────────────────────────────────────
    // Siklus 8: Import Excel
    // ──────────────────────────────────────────────

    public function test_admin_dapat_import_asal_sekolah_dari_excel(): void
    {
        $admin = User::factory()->admin()->create();

        $tempFile = tempnam(sys_get_temp_dir(), 'test_import') . '.xlsx';
        (new \Rap2hpoutre\FastExcel\FastExcel(collect([
            ['NPSN' => '20100001', 'Nama Sekolah' => 'SMP Import Test', 'Status' => 'negeri', 'Alamat' => 'Jl. A', 'Kelurahan' => 'Kel A', 'Kecamatan' => 'Kec A'],
            ['NPSN' => '20100002', 'Nama Sekolah' => 'SMA Import Swasta', 'Status' => 'swasta', 'Alamat' => '', 'Kelurahan' => '', 'Kecamatan' => ''],
        ])))->export($tempFile);

        $file = new \Illuminate\Http\UploadedFile(
            $tempFile, 'import.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null, true
        );

        $response = $this->actingAs($admin)->post('/admin/asal-sekolah/import', ['file' => $file]);

        $response->assertRedirect('/admin/asal-sekolah');
        $this->assertDatabaseHas('asal_sekolah', ['npsn' => '20100001', 'nama' => 'SMP Import Test']);
        $this->assertDatabaseHas('asal_sekolah', ['npsn' => '20100002', 'nama' => 'SMA Import Swasta']);

        @unlink($tempFile);
    }

    public function test_import_dengan_header_tidak_valid_ditolak(): void
    {
        $admin = User::factory()->admin()->create();

        $tempFile = tempnam(sys_get_temp_dir(), 'test_import_bad') . '.xlsx';
        (new \Rap2hpoutre\FastExcel\FastExcel(collect([
            ['KolonSalah' => '20100001', 'Nama' => 'SMP Test'],
        ])))->export($tempFile);

        $file = new \Illuminate\Http\UploadedFile(
            $tempFile, 'import_bad.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null, true
        );

        $response = $this->actingAs($admin)->post('/admin/asal-sekolah/import', ['file' => $file]);

        $response->assertSessionHasErrors('file');

        @unlink($tempFile);
    }

    // ──────────────────────────────────────────────
    // Siklus 1: Admin melihat list asal sekolah
    // ──────────────────────────────────────────────

    public function test_admin_dapat_melihat_list_asal_sekolah(): void
    {
        $admin = User::factory()->admin()->create();

        AsalSekolah::factory()->create(['nama' => 'SMP Negeri 1 Jakarta', 'npsn' => '20100001']);
        AsalSekolah::factory()->create(['nama' => 'SMP Swasta ABC', 'npsn' => '20100002']);

        $response = $this->actingAs($admin)->get('/admin/asal-sekolah');

        $response->assertOk();
        $response->assertSee('SMP Negeri 1 Jakarta');
        $response->assertSee('SMP Swasta ABC');
    }
}
