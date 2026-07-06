<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('login'));
    }

    public function test_login_page_returns_a_successful_response(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_active_user_can_login(): void
    {
        User::create([
            'name' => 'Admin TNA',
            'email' => 'admin@example.test',
            'password' => Hash::make('password'),
            'role' => User::ROLE_ADMIN,
            'permissions' => User::ROLE_PERMISSIONS[User::ROLE_ADMIN],
            'is_active' => true,
        ]);

        $response = $this->post('/login', [
            'email' => 'admin@example.test',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
    }
}
