<?php

namespace Tests\Feature;

use App\Models\Guardian;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuardianManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'ADMIN_TU']);
    }

    /**
     * Test admin can view guardian list
     */
    public function test_admin_can_view_guardian_list()
    {
        Guardian::factory(5)->create();

        $response = $this->actingAs($this->admin)
            ->get('/admin/guardians');

        $response->assertStatus(200);
    }

    /**
     * Test admin can create guardian
     */
    public function test_admin_can_view_create_guardian_form()
    {
        $response = $this->actingAs($this->admin)
            ->get('/admin/guardians/create');

        $response->assertStatus(200);
    }

    /**
     * Test admin can edit guardian
     */
    public function test_admin_can_view_edit_guardian_form()
    {
        $guardian = Guardian::factory()->create();

        $response = $this->actingAs($this->admin)
            ->get("/admin/guardians/{$guardian->id}/edit");

        $response->assertStatus(200);
    }

    /**
     * Test unauthenticated user cannot access guardian pages
     */
    public function test_unauthenticated_cannot_access_guardians()
    {
        $response = $this->get('/admin/guardians');
        $response->assertRedirect('/login');
    }

    /**
     * Test guardian user cannot access guardian management
     */
    public function test_guardian_cannot_access_management()
    {
        $guardianUser = User::factory()->create(['role' => 'WALI_SANTRI']);

        $response = $this->actingAs($guardianUser)
            ->get('/admin/guardians');

        $response->assertStatus(403);
    }
}
