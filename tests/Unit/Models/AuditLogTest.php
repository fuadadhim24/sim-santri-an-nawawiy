<?php

namespace Tests\Unit\Models;

use App\Models\AuditLog;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test can create audit log
     */
    public function test_can_create_audit_log()
    {
        $user = User::factory()->create();
        $student = Student::factory()->create();

        $auditLog = AuditLog::create([
            'log_type' => 'CREATE',
            'subject_type' => Student::class,
            'subject_id' => $student->id,
            'performed_by' => $user->id,
            'ip_address' => '127.0.0.1',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'log_type' => 'CREATE',
            'subject_id' => $student->id,
        ]);
    }

    /**
     * Test audit log stores old and new values
     */
    public function test_audit_log_stores_old_and_new_values()
    {
        $auditLog = AuditLog::create([
            'log_type' => 'UPDATE',
            'subject_type' => Student::class,
            'subject_id' => 1,
            'old_values' => ['name' => 'Old Name'],
            'new_values' => ['name' => 'New Name'],
        ]);

        $this->assertEquals('Old Name', $auditLog->fresh()->old_values['name']);
        $this->assertEquals('New Name', $auditLog->fresh()->new_values['name']);
    }

    /**
     * Test audit log with description
     */
    public function test_audit_log_with_description()
    {
        $auditLog = AuditLog::create([
            'log_type' => 'UPDATE',
            'subject_type' => Student::class,
            'subject_id' => 1,
            'description' => 'Status diubah menjadi ACTIVE',
        ]);

        $this->assertEquals('Status diubah menjadi ACTIVE', $auditLog->description);
    }

    /**
     * Test audit log without user authentication
     */
    public function test_audit_log_without_performed_by()
    {
        $auditLog = AuditLog::create([
            'log_type' => 'DELETE',
            'subject_type' => Student::class,
            'subject_id' => 1,
            'performed_by' => null,
        ]);

        $this->assertNull($auditLog->performed_by);
    }

    /**
     * Test audit log tracks different log types
     */
    public function test_audit_log_tracks_different_log_types()
    {
        $logTypes = ['CREATE', 'READ', 'UPDATE', 'DELETE', 'BULK_ACTION'];

        foreach ($logTypes as $type) {
            $auditLog = AuditLog::create([
                'log_type' => $type,
                'subject_type' => Student::class,
                'subject_id' => 1,
            ]);

            $this->assertEquals($type, $auditLog->log_type);
        }
    }
}
