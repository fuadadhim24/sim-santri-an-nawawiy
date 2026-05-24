<?php

namespace Tests\Feature;

use App\Models\Guardian;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $superAdmin;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->superAdmin = User::factory()->create(['role' => 'SUPER_ADMIN']);
        $this->admin = User::factory()->create(['role' => 'ADMINISTRASI']);
    }

    /**
     * Test super admin can view user list
     */
    public function test_super_admin_can_view_user_list()
    {
        User::factory(5)->create();

        $response = $this->actingAs($this->superAdmin)
            ->get('/admin/users');

        $response->assertStatus(200);
    }

    /**
     * Test admin cannot access user management
     */
    public function test_admin_cannot_access_user_management()
    {
        $response = $this->actingAs($this->admin)
            ->get('/admin/users');

        $response->assertStatus(403);
    }

    /**
     * Test super admin can create new user
     */
    public function test_super_admin_can_view_create_user_form()
    {
        $response = $this->actingAs($this->superAdmin)
            ->get('/admin/users/create');

        $response->assertStatus(200);
    }

    /**
     * Test super admin can edit user
     */
    public function test_super_admin_can_view_edit_user_form()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($this->superAdmin)
            ->get("/admin/users/{$user->id}/edit");

        $response->assertStatus(200);
    }

    /**
     * Test guardian user can access their profile
     */
    public function test_guardian_can_access_own_profile()
    {
        $guardian = User::factory()->create(['role' => 'WALI_SANTRI']);

        $response = $this->actingAs($guardian)
            ->get('/profile');

        $response->assertStatus(200);
    }

    /**
     * Test user can update their profile
     */
    public function test_user_can_update_own_profile()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->patch('/profile', [
                'name' => 'Updated Name',
            ]);

        // Should redirect on success
        $this->assertTrue($response->status() === 302 || $response->status() === 200);
    }
}
