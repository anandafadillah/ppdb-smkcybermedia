<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    // ──────────────────────────────────────────────
    // Siklus 1: List pengguna
    // ──────────────────────────────────────────────

    public function test_admin_dapat_melihat_list_pengguna(): void
    {
        $admin   = User::factory()->admin()->create();
        $panitia = User::factory()->panitia()->create(['name' => 'Budi Santoso']);
        $peserta = User::factory()->peserta()->create(['name' => 'Ani Wijaya']);

        $response = $this->actingAs($admin)->get('/admin/pengguna');

        $response->assertOk();
        $response->assertSee('Budi Santoso');
        $response->assertSee('Ani Wijaya');
    }

    public function test_filter_role_hanya_tampilkan_role_tersebut(): void
    {
        $admin   = User::factory()->admin()->create();
        $panitia = User::factory()->panitia()->create(['name' => 'Panitia Satu']);
        $peserta = User::factory()->peserta()->create(['name' => 'Peserta Satu']);

        $response = $this->actingAs($admin)->get('/admin/pengguna?role=panitia');

        $response->assertOk();
        $response->assertSee('Panitia Satu');
        $response->assertDontSee('Peserta Satu');
    }

    public function test_peserta_tidak_bisa_akses_list_pengguna(): void
    {
        $peserta = User::factory()->peserta()->create();

        $this->actingAs($peserta)
            ->get('/admin/pengguna')
            ->assertForbidden();
    }

    // ──────────────────────────────────────────────
    // Siklus 2: Buat akun baru
    // ──────────────────────────────────────────────

    public function test_admin_dapat_membuat_akun_panitia(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post('/admin/pengguna', [
            'name'                  => 'Panitia Baru',
            'email'                 => 'panitia@example.com',
            'role'                  => 'panitia',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'name'  => 'Panitia Baru',
            'email' => 'panitia@example.com',
            'role'  => 'panitia',
        ]);
    }

    public function test_admin_dapat_membuat_akun_peserta(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post('/admin/pengguna', [
            'name'                  => 'Peserta Baru',
            'nisn'                  => '1234567890',
            'role'                  => 'peserta',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'name' => 'Peserta Baru',
            'nisn' => '1234567890',
            'role' => 'peserta',
        ]);
    }

    public function test_buat_akun_gagal_jika_email_duplikat(): void
    {
        $admin = User::factory()->admin()->create(['email' => 'admin@example.com']);

        $this->actingAs($admin)->post('/admin/pengguna', [
            'name'                  => 'Duplikat',
            'email'                 => 'admin@example.com',
            'role'                  => 'panitia',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ])->assertSessionHasErrors('email');
    }

    // ──────────────────────────────────────────────
    // Siklus 3: Edit pengguna
    // ──────────────────────────────────────────────

    public function test_admin_dapat_edit_nama_dan_email(): void
    {
        $admin  = User::factory()->admin()->create();
        $target = User::factory()->panitia()->create(['name' => 'Lama', 'email' => 'lama@example.com']);

        $response = $this->actingAs($admin)->put("/admin/pengguna/{$target->id}", [
            'name'  => 'Baru',
            'email' => 'baru@example.com',
            'role'  => 'panitia',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['id' => $target->id, 'name' => 'Baru', 'email' => 'baru@example.com']);
    }

    // ──────────────────────────────────────────────
    // Siklus 4: Toggle aktif
    // ──────────────────────────────────────────────

    public function test_admin_dapat_nonaktifkan_pengguna(): void
    {
        $admin  = User::factory()->admin()->create();
        $target = User::factory()->panitia()->create(['is_active' => true]);

        $this->actingAs($admin)
            ->patch("/admin/pengguna/{$target->id}/toggle-aktif")
            ->assertRedirect();

        $this->assertDatabaseHas('users', ['id' => $target->id, 'is_active' => false]);
    }

    public function test_admin_dapat_aktifkan_kembali_pengguna(): void
    {
        $admin  = User::factory()->admin()->create();
        $target = User::factory()->panitia()->create(['is_active' => false]);

        $this->actingAs($admin)
            ->patch("/admin/pengguna/{$target->id}/toggle-aktif")
            ->assertRedirect();

        $this->assertDatabaseHas('users', ['id' => $target->id, 'is_active' => true]);
    }

    // ──────────────────────────────────────────────
    // Siklus 5: Reset password
    // ──────────────────────────────────────────────

    public function test_admin_dapat_reset_password_peserta(): void
    {
        $admin  = User::factory()->admin()->create();
        $target = User::factory()->peserta()->create();

        $response = $this->actingAs($admin)->patch("/admin/pengguna/{$target->id}/reset-password", [
            'password'              => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect();
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('newpassword123', $target->fresh()->password));
    }

    // ──────────────────────────────────────────────
    // Siklus 6: Tidak bisa hapus diri sendiri
    // ──────────────────────────────────────────────

    public function test_admin_tidak_bisa_hapus_akun_sendiri(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->delete("/admin/pengguna/{$admin->id}")
            ->assertForbidden();

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_admin_dapat_hapus_akun_pengguna_lain(): void
    {
        $admin  = User::factory()->admin()->create();
        $target = User::factory()->panitia()->create();

        $this->actingAs($admin)
            ->delete("/admin/pengguna/{$target->id}")
            ->assertRedirect();

        $this->assertDatabaseMissing('users', ['id' => $target->id]);
    }
}
