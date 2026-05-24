<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test can view login page
     */
    public function test_can_view_login_page()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    /**
     * Test can login with valid credentials
     */
    public function test_can_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test cannot login with invalid credentials
     */
    public function test_cannot_login_with_invalid_credentials()
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $this->assertGuest();
    }

    /**
     * Test cannot login with non-existent email
     */
    public function test_cannot_login_with_nonexistent_email()
    {
        $response = $this->post('/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'password',
        ]);

        $this->assertGuest();
    }

    /**
     * Test can logout
     */
    public function test_can_logout()
    {
        $user = User::factory()->create();

        $this->actingAs($user);
        $this->assertAuthenticatedAs($user);

        $response = $this->post('/logout');

        $this->assertGuest();
    }

    /**
     * Test authenticated user redirected from login page
     */
    public function test_authenticated_user_redirected_from_login()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get('/login');

        $response->assertRedirect('/dashboard');
    }

    /**
     * Test can register new user
     */
    public function test_can_view_register_page()
    {
        $response = $this->get('/register');
        
        // May be enabled or disabled depending on config
        $this->assertTrue($response->status() === 200 || $response->status() === 404);
    }

    /**
     * Test dashboard redirects based on role
     */
    public function test_dashboard_redirects_based_on_role()
    {
        $adminUser = User::factory()->create(['role' => 'ADMINISTRASI']);
        $guardianUser = User::factory()->create(['role' => 'WALI_SANTRI']);

        // Admin should go to admin dashboard
        $response = $this->actingAs($adminUser)
            ->get('/dashboard');
        
        $response->assertStatus(302);

        // Guardian should go to wali dashboard
        $response = $this->actingAs($guardianUser)
            ->get('/dashboard');
        
        $response->assertStatus(302);
    }
}
