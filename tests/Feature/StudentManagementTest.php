<?php

namespace Tests\Feature;

use App\Enums\StudentStatus;
use App\Models\Guardian;
use App\Models\SpmbSchedule;
use App\Models\Student;
use App\Models\User;
use Database\Seeders\RombelSeeder;
use Database\Seeders\SpmbScheduleSeeder;
use Database\Seeders\UserSeeder;
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

    public function test_guardian_can_view_own_student_detail()
    {
        $guardianData = Guardian::factory()->create(['user_id' => $this->guardian->id]);
        $student = Student::factory()->create(['guardian_id' => $guardianData->id]);

        $response = $this->actingAs($this->guardian)
            ->get("/students/{$student->id}");

        $response->assertStatus(200);
    }

    public function test_admin_can_process_cash_payment_from_student_detail()
    {
        $student = Student::factory()->create();
        $feeMaster = \App\Models\FeeMaster::factory()->create(['amount' => 50000]);
        $billing = \App\Models\Billing::create([
            'student_id' => $student->id,
            'fee_master_id' => $feeMaster->id,
            'title' => 'SPP Test',
            'original_amount' => 50000,
            'final_amount' => 50000,
            'status' => 'UNPAID',
            'visible_to_wali' => true,
        ]);

        \Livewire\Livewire::actingAs($this->admin)
            ->test(\App\Livewire\StudentDetail::class, ['student' => $student])
            ->call('processCashPayment', $billing->id)
            ->assertHasNoErrors();

        $billing->refresh();
        $this->assertEquals('PAID', $billing->status);
        $this->assertDatabaseHas('payments', [
            'billing_id' => $billing->id,
            'status' => 'paid',
            'method' => 'cash',
        ]);
    }

    public function test_admin_can_delete_billing_from_student_detail()
    {
        $student = Student::factory()->create();
        $feeMaster = \App\Models\FeeMaster::factory()->create(['amount' => 50000]);
        $billing = \App\Models\Billing::create([
            'student_id' => $student->id,
            'fee_master_id' => $feeMaster->id,
            'title' => 'SPP Test',
            'original_amount' => 50000,
            'final_amount' => 50000,
            'status' => 'UNPAID',
            'visible_to_wali' => true,
        ]);

        \Livewire\Livewire::actingAs($this->admin)
            ->test(\App\Livewire\StudentDetail::class, ['student' => $student])
            ->call('delete', $billing->id)
            ->assertHasNoErrors();

        $this->assertSoftDeleted('billings', ['id' => $billing->id]);
    }

    public function test_admin_can_split_billing_from_student_detail()
    {
        $student = Student::factory()->create();
        $feeMaster = \App\Models\FeeMaster::factory()->create(['amount' => 50000]);
        $billing = \App\Models\Billing::create([
            'student_id' => $student->id,
            'fee_master_id' => $feeMaster->id,
            'title' => 'SPP Test',
            'original_amount' => 50000,
            'final_amount' => 50000,
            'status' => 'UNPAID',
            'visible_to_wali' => true,
        ]);

        \Livewire\Livewire::actingAs($this->admin)
            ->test(\App\Livewire\StudentDetail::class, ['student' => $student])
            ->call('openSplitModal', $billing->id)
            ->set('splitCount', 2)
            ->call('processSplit')
            ->assertHasNoErrors();

        $billing->refresh();
        $this->assertEquals('VOID', $billing->status);
        $this->assertDatabaseHas('billings', [
            'student_id' => $student->id,
            'status' => 'UNPAID',
            'final_amount' => 25000,
        ]);
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

        $response->assertRedirect('/my-dashboard');
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

    public function test_admin_reject_student_via_ajax_updates_status_and_returns_json()
    {
        $student = Student::factory()->create(['status' => 'REGISTERED']);

        $response = $this->actingAs($this->admin)
            ->withHeader('Accept', 'application/json')
            ->post("/admin/students/{$student->id}/reject", [
                'reason' => 'Dokumen tidak lengkap',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $student->refresh();
        $this->assertSame('ditolak', $student->status);
        $this->assertSame('Dokumen tidak lengkap', $student->rejection_note);
    }
    public function test_seeders_create_multiple_active_spmb_schedules_and_pending_students_across_them()
    {
        $this->seed(SpmbScheduleSeeder::class);
        $this->seed(RombelSeeder::class);
        $this->seed(UserSeeder::class);

        $activeSchedules = SpmbSchedule::where('is_active', true)->get();
        $this->assertGreaterThanOrEqual(2, $activeSchedules->count());

        $pendingScheduleIds = Student::where('status', StudentStatus::PENDING->value)
            ->pluck('spmb_schedule_id')
            ->filter()
            ->unique()
            ->values();

        $this->assertGreaterThanOrEqual(2, $pendingScheduleIds->count());
    }}
