<?php

namespace Tests\Feature\Admin;

use App\Models\Pengumuman;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PengumumanTest extends TestCase
{
    use RefreshDatabase;

    // ──────────────────────────────────────────────
    // Siklus 1: Admin CRUD pengumuman
    // ──────────────────────────────────────────────

    public function test_admin_dapat_melihat_list_pengumuman(): void
    {
        $admin = User::factory()->admin()->create();
        Pengumuman::factory()->create(['judul' => 'Pengumuman PPDB 2025', 'user_id' => $admin->id]);

        $response = $this->actingAs($admin)->get('/admin/pengumuman');

        $response->assertOk();
        $response->assertSee('Pengumuman PPDB 2025');
    }

    public function test_admin_dapat_membuat_pengumuman(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post('/admin/pengumuman', [
            'judul'           => 'Informasi Seleksi',
            'isi'             => '<p>Seleksi dimulai 1 Agustus</p>',
            'status'          => 'draft',
            'tanggal_publish' => '2025-08-01',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('pengumuman', ['judul' => 'Informasi Seleksi', 'status' => 'draft']);
    }

    public function test_admin_dapat_mengedit_pengumuman(): void
    {
        $admin = User::factory()->admin()->create();
        $p     = Pengumuman::factory()->create(['user_id' => $admin->id, 'judul' => 'Judul Lama']);

        $response = $this->actingAs($admin)->put("/admin/pengumuman/{$p->id}", [
            'judul'  => 'Judul Baru',
            'isi'    => '<p>Isi baru</p>',
            'status' => 'published',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('pengumuman', ['id' => $p->id, 'judul' => 'Judul Baru', 'status' => 'published']);
    }

    public function test_admin_dapat_menghapus_pengumuman(): void
    {
        $admin = User::factory()->admin()->create();
        $p     = Pengumuman::factory()->create(['user_id' => $admin->id]);

        $this->actingAs($admin)
            ->delete("/admin/pengumuman/{$p->id}")
            ->assertRedirect();

        $this->assertDatabaseMissing('pengumuman', ['id' => $p->id]);
    }

    // ──────────────────────────────────────────────
    // Siklus 2: Status draft vs published
    // ──────────────────────────────────────────────

    public function test_pengumuman_published_tampil_di_dashboard_admin(): void
    {
        $admin = User::factory()->admin()->create();
        Pengumuman::factory()->published()->create(['judul' => 'Info Published', 'user_id' => $admin->id]);
        Pengumuman::factory()->create(['judul' => 'Info Draft', 'status' => 'draft', 'user_id' => $admin->id]);

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertOk();
        $response->assertSee('Info Published');
        $response->assertDontSee('Info Draft');
    }

    public function test_pengumuman_published_tampil_di_dashboard_panitia(): void
    {
        $panitia = User::factory()->panitia()->create();
        $admin   = User::factory()->admin()->create();
        Pengumuman::factory()->published()->create(['judul' => 'Info Panitia', 'user_id' => $admin->id]);

        $response = $this->actingAs($panitia)->get('/panitia/dashboard');

        $response->assertOk();
        $response->assertSee('Info Panitia');
    }

    public function test_pengumuman_published_tampil_di_dashboard_peserta(): void
    {
        $user    = User::factory()->peserta()->create();
        $admin   = User::factory()->admin()->create();
        Pengumuman::factory()->published()->create(['judul' => 'Info Peserta', 'user_id' => $admin->id]);

        $response = $this->actingAs($user)->get('/peserta/dashboard');

        $response->assertOk();
        $response->assertSee('Info Peserta');
    }

    public function test_pengumuman_published_tampil_di_landing_page(): void
    {
        $admin = User::factory()->admin()->create();
        Pengumuman::factory()->published()->create(['judul' => 'Selamat Datang', 'user_id' => $admin->id]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Selamat Datang');
    }

    public function test_draft_tidak_tampil_di_landing_page(): void
    {
        $admin = User::factory()->admin()->create();
        Pengumuman::factory()->create(['judul' => 'Rahasia Draft', 'status' => 'draft', 'user_id' => $admin->id]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertDontSee('Rahasia Draft');
    }
}
