<?php

namespace Tests\Unit\Models;

use App\Models\SpmbSchedule;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SpmbScheduleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test can create SPMB schedule
     */
    public function test_can_create_spmb_schedule()
    {
        $schedule = SpmbSchedule::create([
            'title' => 'SPMB Tahun 2024',
            'start_date' => now()->addDays(1),
            'end_date' => now()->addDays(30),
            'description' => 'Pendaftaran siswa baru tahun 2024',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('spmb_schedules', [
            'title' => 'SPMB Tahun 2024',
        ]);
    }

    /**
     * Test SPMB schedule has many students
     */
    public function test_spmb_schedule_has_many_students()
    {
        $schedule = SpmbSchedule::factory()->create();
        
        Student::factory(5)->create(['spmb_schedule_id' => $schedule->id]);

        $this->assertCount(5, $schedule->students ?? []);
    }

    /**
     * Test SPMB schedule is_active cast
     */
    public function test_spmb_schedule_is_active_cast()
    {
        $schedule = SpmbSchedule::factory()->create(['is_active' => true]);
        $this->assertTrue($schedule->is_active);

        $schedule->update(['is_active' => false]);
        $this->assertFalse($schedule->fresh()->is_active);
    }

    /**
     * Test SPMB schedule dates
     */
    public function test_spmb_schedule_dates()
    {
        $startDate = now()->addDays(1);
        $endDate = now()->addDays(30);

        $schedule = SpmbSchedule::factory()->create([
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);

        $this->assertEquals($startDate->format('Y-m-d'), $schedule->fresh()->start_date->format('Y-m-d'));
    }

    /**
     * Test soft delete on SPMB schedule
     */
    public function test_spmb_schedule_can_be_soft_deleted()
    {
        $schedule = SpmbSchedule::factory()->create();

        $schedule->delete();

        $this->assertSoftDeleted('spmb_schedules', ['id' => $schedule->id]);
    }
}
