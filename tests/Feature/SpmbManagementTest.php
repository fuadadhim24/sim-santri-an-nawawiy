<?php

namespace Tests\Feature;

use App\Models\SpmbSchedule;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SpmbManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $guardian;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::factory()->create(['role' => 'SUPER_ADMIN']);
        $this->guardian = User::factory()->create(['role' => 'WALI_SANTRI']);
    }

    /**
     * Test admin can view SPMB schedules
     */
    public function test_admin_can_view_spmb_schedules()
    {
        SpmbSchedule::factory(3)->create();

        $response = $this->actingAs($this->admin)
            ->get('/admin/spmb-schedules');

        $response->assertStatus(200);
    }

    /**
     * Test admin can create SPMB schedule
     */
    public function test_admin_can_view_create_spmb_schedule_form()
    {
        $response = $this->actingAs($this->admin)
            ->get('/admin/spmb-schedules/create');

        $response->assertStatus(200);
    }

    /**
     * Test admin can edit SPMB schedule
     */
    public function test_admin_can_view_edit_spmb_schedule_form()
    {
        $schedule = SpmbSchedule::factory()->create();

        $response = $this->actingAs($this->admin)
            ->get("/admin/spmb-schedules/{$schedule->id}/edit");

        $response->assertStatus(200);
    }

    /**
     * Test guardian can view available SPMB schedules
     */
    public function test_guardian_can_view_available_schedules()
    {
        SpmbSchedule::factory(3)->create();

        $response = $this->actingAs($this->guardian)
            ->get('/spmb-schedules');

        $response->assertStatus(200);
    }

    /**
     * Test guardian can access SPMB registration
     */
    public function test_guardian_can_access_spmb_registration()
    {
        $response = $this->actingAs($this->guardian)
            ->get('/spmb/register');

        $response->assertStatus(200);
    }

    /**
     * Test only super admin can manage SPMB
     */
    public function test_only_super_admin_can_manage_spmb()
    {
        $adminTu = User::factory()->create(['role' => 'ADMIN_TU']);

        $response = $this->actingAs($adminTu)
            ->get('/admin/spmb-schedules');

        $response->assertStatus(403);
    }

    /**
     * Test unauthenticated user cannot access SPMB
     */
    public function test_unauthenticated_cannot_access_spmb()
    {
        $response = $this->get('/spmb-schedules');
        $response->assertRedirect('/login');
    }
}
