<?php

namespace Tests\Unit\Models;

use App\Models\Billing;
use App\Models\Guardian;
use App\Models\Student;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test can create student with valid data
     */
    public function test_can_create_student_with_valid_data()
    {
        $guardian = Guardian::factory()->create();
        $studentData = [
            'guardian_id' => $guardian->id,
            'full_name' => 'Ahmad Santri',
            'nis' => '20241001',
            'unit_code' => '01',
            'residence_status' => 'MONDOK',
            'special_status' => 'UMUM',
            'address' => 'Jl. Test No. 1',
            'class_name' => 'Kelas X',
            'is_active' => true,
        ];

        $student = Student::create($studentData);

        $this->assertDatabaseHas('students', [
            'full_name' => 'Ahmad Santri',
            'nis' => '20241001',
        ]);
        $this->assertEquals($studentData['full_name'], $student->full_name);
    }

    /**
     * Test student has many billings
     */
    public function test_student_has_many_billings()
    {
        $student = Student::factory()->create();
        
        Billing::factory(3)->create(['student_id' => $student->id]);

        $this->assertCount(3, $student->billings);
    }

    /**
     * Test student can be soft deleted
     */
    public function test_student_can_be_soft_deleted()
    {
        $student = Student::factory()->create();

        $student->delete();

        $this->assertSoftDeleted('students', ['id' => $student->id]);
    }

    /**
     * Test cannot force delete student with active billings
     */
    public function test_cannot_force_delete_student_with_active_billings()
    {
        $student = Student::factory()->create();
        Billing::factory()->create([
            'student_id' => $student->id,
            'status' => 'UNPAID',
        ]);

        $this->expectException(Exception::class);
        $student->forceDelete();
    }

    /**
     * Test can force delete student without active billings
     */
    public function test_can_force_delete_student_without_active_billings()
    {
        $student = Student::factory()->create();
        $studentId = $student->id;

        $student->forceDelete();

        $this->assertNull(Student::withTrashed()->find($studentId));
    }

    /**
     * Test student unit codes
     */
    public function test_student_unit_codes()
    {
        $units = ['01', '02', '03'];

        foreach ($units as $unit) {
            $student = Student::factory()->create(['unit_code' => $unit]);
            $this->assertEquals($unit, $student->unit_code);
        }
    }

    /**
     * Test student with guardian relationship
     */
    public function test_student_can_belong_to_guardian()
    {
        $guardian = Guardian::factory()->create();
        $student = Student::factory()->create(['guardian_id' => $guardian->id]);
        
        $this->assertEquals($guardian->id, $student->guardian_id);
    }

    /**
     * Test student is_active cast to boolean
     */
    public function test_student_is_active_is_boolean()
    {
        $student = Student::factory()->create(['is_active' => true]);
        $this->assertTrue($student->is_active);

        $student->update(['is_active' => false]);
        $this->assertFalse($student->fresh()->is_active);
    }
}
