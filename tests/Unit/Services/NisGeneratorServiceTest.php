<?php

namespace Tests\Unit\Services;

use App\Models\Student;
use App\Services\NisGeneratorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NisGeneratorServiceTest extends TestCase
{
    use RefreshDatabase;

    private NisGeneratorService $nisGeneratorService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->nisGeneratorService = new NisGeneratorService();
    }

    /**
     * Test generate NIS with first student
     */
    public function test_generate_nis_for_first_student()
    {
        $nis = $this->nisGeneratorService->generate('MTK', 2024);

        $this->assertEquals('2425.MTK.0001', $nis);
    }

    /**
     * Test generate NIS increments sequence
     */
    public function test_generate_nis_increments_sequence()
    {
        Student::factory()->create(['nis' => '2425.MTK.0001']);

        $nis = $this->nisGeneratorService->generate('MTK', 2024);

        $this->assertEquals('2425.MTK.0002', $nis);
    }

    /**
     * Test generate NIS with different unit codes
     */
    public function test_generate_nis_with_different_unit_codes()
    {
        Student::factory()->create(['nis' => '2425.MTK.0001']);
        Student::factory()->create(['nis' => '2425.IBTIDAIYAH.0001']);

        $nisMtk = $this->nisGeneratorService->generate('MTK', 2024);
        $nisIbtidaiyah = $this->nisGeneratorService->generate('IBTIDAIYAH', 2024);

        $this->assertEquals('2425.MTK.0002', $nisMtk);
        $this->assertEquals('2425.IBTIDAIYAH.0002', $nisIbtidaiyah);
    }

    /**
     * Test generate NIS with different years
     */
    public function test_generate_nis_with_different_years()
    {
        Student::factory()->create(['nis' => '2324.MTK.0001']);

        $nis2024 = $this->nisGeneratorService->generate('MTK', 2024);
        $nis2025 = $this->nisGeneratorService->generate('MTK', 2025);

        $this->assertEquals('2425.MTK.0001', $nis2024);
        $this->assertEquals('2526.MTK.0001', $nis2025);
    }

    /**
     * Test generate NIS format consistency
     */
    public function test_generate_nis_format_consistency()
    {
        for ($i = 0; $i < 5; $i++) {
            $nis = $this->nisGeneratorService->generate('TEST', 2024);
            
            // Check format: YYyy.UNIT.XXXX
            $this->assertMatchesRegularExpression('/^2425\.TEST\.\d{4}$/', $nis);
        }
    }

    /**
     * Test generate NIS pads sequence with zeros
     */
    public function test_generate_nis_pads_sequence_with_zeros()
    {
        for ($i = 0; $i < 15; $i++) {
            Student::factory()->create(['nis' => sprintf('2425.MTK.%04d', $i + 1)]);
        }

        $nis = $this->nisGeneratorService->generate('MTK', 2024);

        $this->assertEquals('2425.MTK.0016', $nis);
        // Ensure 4-digit padding
        $this->assertStringContainsString('0016', $nis);
    }

    /**
     * Test generate NIS gets highest sequence
     */
    public function test_generate_nis_uses_highest_sequence()
    {
        Student::factory()->create(['nis' => '2425.MTK.0001']);
        Student::factory()->create(['nis' => '2425.MTK.0003']);
        Student::factory()->create(['nis' => '2425.MTK.0002']);

        $nis = $this->nisGeneratorService->generate('MTK', 2024);

        // Should use the highest (0003) and increment
        $this->assertEquals('2425.MTK.0004', $nis);
    }
}
