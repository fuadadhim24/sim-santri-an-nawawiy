<?php

namespace Tests\Unit\Services;

use App\Models\AuditLog;
use App\Models\Student;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test log audit entry for create action
     */
    public function test_log_audit_entry_for_create()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $student = Student::factory()->create();

        AuditService::log(
            'CREATE',
            $student,
            [],
            [
                'full_name' => $student->full_name,
                'email' => $student->email,
            ],
            'Student dibuat'
        );

        $this->assertDatabaseHas('audit_logs', [
            'log_type' => 'CREATE',
            'subject_type' => Student::class,
            'subject_id' => $student->id,
            'performed_by' => $user->id,
        ]);
    }

    /**
     * Test log audit entry for update action
     */
    public function test_log_audit_entry_for_update()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $student = Student::factory()->create();

        AuditService::log(
            'UPDATE',
            $student,
            ['full_name' => 'Old Name'],
            ['full_name' => 'New Name'],
            'Nama diperbarui'
        );

        $this->assertDatabaseHas('audit_logs', [
            'log_type' => 'UPDATE',
            'subject_type' => Student::class,
            'subject_id' => $student->id,
        ]);

        $auditLog = AuditLog::latest()->first();
        $this->assertArrayHasKey('full_name', $auditLog->old_values);
        $this->assertArrayHasKey('full_name', $auditLog->new_values);
    }

    /**
     * Test log audit entry for delete action
     */
    public function test_log_audit_entry_for_delete()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $student = Student::factory()->create();

        AuditService::log(
            'DELETE',
            $student,
            ['full_name' => $student->full_name],
            [],
            'Student dihapus'
        );

        $this->assertDatabaseHas('audit_logs', [
            'log_type' => 'DELETE',
            'subject_type' => Student::class,
            'performed_by' => $user->id,
        ]);
    }

    /**
     * Test audit log stores IP address
     */
    public function test_audit_log_stores_ip_address()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $student = Student::factory()->create();

        AuditService::log('CREATE', $student);

        $auditLog = AuditLog::latest()->first();
        $this->assertNotNull($auditLog->ip_address);
    }

    /**
     * Test audit log stores performed by user
     */
    public function test_audit_log_stores_performed_by_user()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $student = Student::factory()->create();

        AuditService::log('CREATE', $student);

        $this->assertDatabaseHas('audit_logs', [
            'performed_by' => $user->id,
        ]);
    }

    /**
     * Test audit log without authentication
     */
    public function test_audit_log_without_authentication()
    {
        $student = Student::factory()->create();

        AuditService::log('CREATE', $student, [], [], 'Test without auth');

        $this->assertDatabaseHas('audit_logs', [
            'performed_by' => null,
        ]);
    }

    /**
     * Test audit log with multiple changes
     */
    public function test_audit_log_with_multiple_changes()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $student = Student::factory()->create();

        AuditService::log(
            'UPDATE',
            $student,
            [
                'full_name' => 'Old Name',
                'email' => 'old@example.com',
                'phone' => '081111111111',
            ],
            [
                'full_name' => 'New Name',
                'email' => 'new@example.com',
                'phone' => '082222222222',
            ]
        );

        $auditLog = AuditLog::latest()->first();
        $this->assertCount(3, $auditLog->old_values);
        $this->assertCount(3, $auditLog->new_values);
    }

    /**
     * Test audit log with empty old/new values
     */
    public function test_audit_log_with_empty_values()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $student = Student::factory()->create();

        AuditService::log('CREATE', $student, [], []);

        $auditLog = AuditLog::latest()->first();
        $this->assertNull($auditLog->old_values);
        $this->assertNull($auditLog->new_values);
    }

    /**
     * Test audit log with description
     */
    public function test_audit_log_with_description()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $student = Student::factory()->create();
        $description = 'Student diterima karena lolos seleksi';

        AuditService::log('UPDATE', $student, [], [], $description);

        $this->assertDatabaseHas('audit_logs', [
            'description' => $description,
        ]);
    }
}
