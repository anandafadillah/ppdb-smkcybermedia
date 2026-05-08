<?php

namespace Tests\Feature;

use Tests\TestCase;

class ProjectBootstrapTest extends TestCase
{
    // Cycle 1 — Tracer bullet: Laravel running
    public function test_homepage_returns_200(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    // Cycle 2 — Homepage has valid HTML structure
    public function test_homepage_has_html_structure(): void
    {
        $response = $this->get('/');
        $response->assertSee('<html', false);
        $response->assertSee('</body>', false);
    }

    // Cycle 3 — Guest layout renders with CSS loaded (Vite compiled or Tailwind CDN)
    public function test_guest_layout_includes_tailwind(): void
    {
        $response = $this->get('/test-guest');
        $response->assertStatus(200);
        // Verify CSS is loaded: either via Vite compiled file or Tailwind CDN fallback
        $content = $response->getContent();
        $hasCss = str_contains($content, 'app-') && str_contains($content, '.css')
            || str_contains($content, 'tailwindcss')
            || str_contains($content, 'cdn.tailwindcss.com');
        $this->assertTrue($hasCss, 'Layout harus memuat Tailwind CSS (Vite atau CDN)');
        // Verify Tailwind utility classes are applied in the layout
        $response->assertSee('min-h-screen', false);
    }

    // Cycle 4 — Custom 404 error page
    public function test_not_found_returns_custom_404(): void
    {
        $response = $this->get('/halaman-yang-tidak-ada');
        $response->assertStatus(404);
        $response->assertSee('404', false);
        $response->assertSee('Halaman Tidak Ditemukan', false);
    }

    // Cycle 5 — Custom 403 error page
    public function test_403_error_page_renders(): void
    {
        $response = $this->get('/test-403');
        $response->assertStatus(403);
        $response->assertSee('403', false);
        $response->assertSee('Akses Ditolak', false);
    }

    // Cycle 6 — Custom 500 error page
    public function test_500_error_page_renders(): void
    {
        $response = $this->get('/test-500');
        $response->assertStatus(500);
        $response->assertSee('500', false);
        $response->assertSee('Kesalahan Server', false);
    }

    // Cycle 7 — Authenticated layout has sidebar
    public function test_authenticated_layout_has_sidebar(): void
    {
        $response = $this->get('/test-auth-layout');
        $response->assertStatus(200);
        $response->assertSee('id="sidebar"', false);
        $response->assertSee('id="header"', false);
    }
}
