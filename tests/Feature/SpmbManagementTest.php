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

    public function test_guardian_can_access_spmb_registration()
    {
        $schedule = SpmbSchedule::factory()->create([
            'registration_start' => now()->subDays(2),
            'registration_end' => now()->addDays(2),
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->guardian)
            ->withSession(['selected_spmb_schedule_id' => $schedule->id])
            ->get('/spmb/register');

        $response->assertStatus(200);
    }

    /**
     * Test only authorized roles can manage SPMB
     */
    public function test_only_authorized_roles_can_manage_spmb()
    {
        $bendahara = User::factory()->create(['role' => 'BENDAHARA']);

        $response = $this->actingAs($bendahara)
            ->get('/admin/spmb-schedules')
            ->assertRedirect(route('admin.dashboard'));
    }

    /**
     * Test unauthenticated user cannot access SPMB
     */
    public function test_unauthenticated_cannot_access_spmb()
    {
        $response = $this->get('/spmb-schedules');
        $response->assertRedirect('/login');
    }

    public function test_guardian_can_register_student_with_class_level()
    {
        $schedule = SpmbSchedule::factory()->create([
            'registration_start' => now()->subDays(2),
            'registration_end' => now()->addDays(2),
            'is_active' => true,
        ]);

        $guardianModel = \App\Models\Guardian::factory()->create(['user_id' => $this->guardian->id]);
        $classLevel = \App\Models\ClassLevel::create(['name' => 'Kelas 7 SMP', 'level_order' => 1]);

        \Illuminate\Support\Facades\Storage::fake('public');
        $this->withSession(['selected_spmb_schedule_id' => $schedule->id]);

        \Livewire\Livewire::actingAs($this->guardian)
            ->test(\App\Livewire\SpmbStudentRegistration::class)
            ->set('full_name', 'Santri Baru')
            ->set('unit_code', '01')
            ->set('residence_status', 'MONDOK')
            ->set('special_status', 'UMUM')
            ->set('class_level_id', $classLevel->id)
            ->set('address', 'Alamat Santri')
            ->set('kk', \Illuminate\Http\UploadedFile::fake()->create('kk.pdf', 500))
            ->set('foto', \Illuminate\Http\UploadedFile::fake()->image('foto.jpg'))
            ->set('akta', \Illuminate\Http\UploadedFile::fake()->create('akta.pdf', 500))
            ->set('ijazah', \Illuminate\Http\UploadedFile::fake()->create('ijazah.pdf', 500))
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('wali.dashboard'));

        $this->assertDatabaseHas('students', [
            'full_name' => 'Santri Baru',
            'class_level_id' => $classLevel->id,
            'guardian_id' => $guardianModel->id,
        ]);

        $newStudent = \App\Models\Student::where('full_name', 'Santri Baru')->first();
        $this->assertNull($newStudent->nis);
        $this->assertNotNull($newStudent->registration_number);
        $this->assertMatchesRegularExpression('/^\d{4}\.\d{4}$/', $newStudent->registration_number);
    }
}
