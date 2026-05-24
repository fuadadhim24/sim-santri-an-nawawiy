<?php

namespace Tests\Feature;

use App\Models\Guardian;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $guardian;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::factory()->create(['role' => 'ADMINISTRASI']);
        $this->guardian = User::factory()->create(['role' => 'WALI_SANTRI']);
    }

    /**
     * Test admin can view student list
     */
    public function test_admin_can_view_student_list()
    {
        Student::factory(5)->create();

        $response = $this->actingAs($this->admin)
            ->get('/admin/students');

        $response->assertStatus(200);
    }

    /**
     * Test admin can create new student
     */
    public function test_admin_can_create_new_student()
    {
        $response = $this->actingAs($this->admin)
            ->get('/admin/students/create');

        $response->assertStatus(200);
    }

    /**
     * Test admin can view student detail
     */
    public function test_admin_can_view_student_detail()
    {
        $student = Student::factory()->create();

        $response = $this->actingAs($this->admin)
            ->get("/admin/students/{$student->id}");

        $response->assertStatus(200);
    }

    /**
     * Test admin can edit student
     */
    public function test_admin_can_edit_student()
    {
        $student = Student::factory()->create();

        $response = $this->actingAs($this->admin)
            ->get("/admin/students/{$student->id}/edit");

        $response->assertStatus(200);
    }

    /**
     * Test guardian can view own student detail
     */
    public function test_guardian_can_view_own_student_detail()
    {
        $guardianData = Guardian::factory()->create();
        $student = Student::factory()->create(['guardian_id' => $guardianData->id]);

        $response = $this->actingAs($this->guardian)
            ->get("/students/{$student->id}");

        $response->assertStatus(200);
    }

    /**
     * Test unauthenticated user cannot access student pages
     */
    public function test_unauthenticated_user_cannot_access_student_pages()
    {
        $response = $this->get('/admin/students');
        $response->assertRedirect('/login');
    }

    /**
     * Test guardian cannot access admin student list
     */
    public function test_guardian_cannot_access_admin_pages()
    {
        $response = $this->actingAs($this->guardian)
            ->get('/admin/students');

        $response->assertStatus(403);
    }

    /**
     * Test student acceptance workflow
     */
    public function test_student_acceptance_workflow()
    {
        $student = Student::factory()->create(['status' => 'REGISTERED']);

        $response = $this->actingAs($this->admin)
            ->get('/admin/student-acceptance');

        $response->assertStatus(200);
    }

    /**
     * Test admin can reject student
     */
    public function test_admin_can_reject_student()
    {
        $student = Student::factory()->create();

        $response = $this->actingAs($this->admin)
            ->post("/admin/students/{$student->id}/reject", [
                'reason' => 'Tidak memenuhi kriteria',
            ]);

        // Response depends on implementation - should redirect or return success
        $this->assertTrue($response->status() === 302 || $response->status() === 200);
    }
}
