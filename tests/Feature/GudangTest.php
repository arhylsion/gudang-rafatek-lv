<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GudangTest extends TestCase
{
    public function test_login_page_ok(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Gudang Rafatek');
    }

    public function test_login_with_valid_credentials(): void
    {
        $response = $this->post('/login', [
            'username' => 'administrator',
            'password' => 'administrator',
        ]);
        $response->assertRedirect('/dashboard');
    }

    public function test_protected_routes_require_auth(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
        $this->get('/barang')->assertRedirect('/login');
    }

    public function test_dashboard_and_modules_ok_when_logged_in(): void
    {
        $user = User::where('username', 'administrator')->first();
        $this->actingAs($user);

        $urls = [
            '/dashboard',
            '/barang',
            '/jenis',
            '/satuan',
            '/rak',
            '/keranjang',
            '/lokasi',
            '/barang-masuk',
            '/barang-keluar',
            '/opname',
            '/laporan/stok',
            '/laporan/masuk',
            '/laporan/keluar',
            '/user',
            '/profil',
            '/password',
        ];
        foreach ($urls as $url) {
            $res = $this->get($url);
            $this->assertTrue(
                in_array($res->status(), [200, 302]),
                "URL $url returned {$res->status()}"
            );
        }
    }

    public function test_role_protection(): void
    {
        $kepala = User::where('hak_akses', 'Kepala Gudang')->first();
        $this->actingAs($kepala);
        // Kepala Gudang tidak boleh akses barang
        $this->get('/barang')->assertStatus(403);
        // Tapi boleh akses laporan
        $this->get('/laporan/stok')->assertStatus(200);
    }
}
