<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_register_ditutup(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(404);
    }

    public function test_pengguna_baru_tidak_bisa_register_mandiri(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(404);
        $this->assertGuest();
        $this->assertSame(0, User::count());
    }
}
