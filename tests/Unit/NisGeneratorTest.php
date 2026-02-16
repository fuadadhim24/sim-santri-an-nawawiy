<?php

namespace Tests\Unit;

use App\Models\Student;
use App\Models\Guardian;
use App\Models\User;
use App\Services\NisGeneratorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NisGeneratorTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_generates_correct_nis_format(): void
    {
        $service = new NisGeneratorService();
        $year = 2026;
        $unitCode = '01'; // SMP

        $nis = $service->generate($unitCode, $year);

        $this->assertEquals('2026.01.0001', $nis);
    }

    public function test_it_increments_nis_correctly(): void
    {
        $service = new NisGeneratorService();
        $year = 2026;
        $unitCode = '02'; // SMA

        $user = User::factory()->create();
        $guardian = Guardian::create([
            'user_id' => $user->id,
            'full_name' => 'Test Guardian',
            'whatsapp' => '08123',
        ]);

        Student::create([
            'guardian_id' => $guardian->id,
            'nis' => '2026.02.0005',
            'full_name' => 'Student 5',
            'unit_code' => '02',
            'residence_status' => 'NON_MONDOK',
            'special_status' => 'UMUM',
            'is_active' => true,
        ]);

        $newNis = $service->generate($unitCode, $year);

        $this->assertEquals('2026.02.0006', $newNis);
    }
}
