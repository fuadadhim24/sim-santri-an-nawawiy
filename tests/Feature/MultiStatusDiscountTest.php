<?php

namespace Tests\Feature;

use App\Models\Billing;
use App\Models\Discount;
use App\Models\FeeCategory;
use App\Models\FeeMaster;
use App\Models\SpecialStatus;
use App\Models\Student;
use App\Models\User;
use App\Services\BillingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MultiStatusDiscountTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private SpecialStatus $statusKurangMampu;
    private SpecialStatus $statusPrestasi1;
    private FeeMaster $feeMaster;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'SUPER_ADMIN']);

        // Buat special statuses
        $this->statusKurangMampu = SpecialStatus::create([
            'code'      => 'KURANGMAMPU',
            'name'      => 'Kurang Mampu',
            'is_system' => false,
        ]);

        $this->statusPrestasi1 = SpecialStatus::create([
            'code'      => 'PRESTASI1',
            'name'      => 'Prestasi Golongan 1',
            'is_system' => false,
        ]);

        // Buat fee category & master
        $category = FeeCategory::factory()->create(['is_active' => true]);
        $this->feeMaster = FeeMaster::factory()->create([
            'fee_category_id'       => $category->id,
            'amount'                => 150000,
            'is_active'             => true,
            'unit_target'           => null,
            'residence_target'      => null,
            'class_level_target_id' => null,
        ]);
    }

    // =========================================================
    // 1. MODEL — RELASI MANY-TO-MANY
    // =========================================================

    /** @test */
    public function student_dapat_memiliki_banyak_special_status()
    {
        $student = Student::factory()->create(['special_status' => 'UMUM']);

        $student->specialStatuses()->sync([
            'KURANGMAMPU' => [],
            'PRESTASI1'   => [],
        ]);

        $this->assertCount(2, $student->fresh()->specialStatuses);
        $this->assertTrue($student->fresh()->hasAnySpecialStatus());
    }

    /** @test */
    public function student_tanpa_status_khusus_hasAnySpecialStatus_returns_false()
    {
        $student = Student::factory()->create(['special_status' => 'UMUM']);

        $this->assertFalse($student->hasAnySpecialStatus());
    }

    /** @test */
    public function getSpecialStatusCodes_mengembalikan_semua_kode_status()
    {
        $student = Student::factory()->create(['special_status' => 'UMUM']);
        $student->specialStatuses()->sync([
            'KURANGMAMPU' => [],
            'PRESTASI1'   => [],
        ]);

        $codes = $student->fresh()->getSpecialStatusCodes();

        $this->assertCount(2, $codes);
        $this->assertTrue($codes->contains('KURANGMAMPU'));
        $this->assertTrue($codes->contains('PRESTASI1'));
    }

    // =========================================================
    // 2. BILLING SERVICE — AKUMULASI DISKON
    // =========================================================

    /** @test */
    public function billing_service_menjumlah_diskon_dari_semua_status()
    {
        $student = Student::factory()->create(['special_status' => 'UMUM']);
        $student->specialStatuses()->sync([
            'KURANGMAMPU' => [],
            'PRESTASI1'   => [],
        ]);

        // Buat diskon untuk masing-masing status
        Discount::create([
            'fee_master_id'   => $this->feeMaster->id,
            'target_status'   => 'KURANGMAMPU',
            'discount_amount' => 50000,
        ]);
        Discount::create([
            'fee_master_id'   => $this->feeMaster->id,
            'target_status'   => 'PRESTASI1',
            'discount_amount' => 30000,
        ]);

        $billingService = app(BillingService::class);
        $student->load('specialStatuses');

        // Generate billing
        $billingService->generateBillingsForStudentWithCategories(
            $student,
            [$this->feeMaster->category->id]
        );

        // Cek billing yang terbuat
        $billing = Billing::where('student_id', $student->id)->first();
        $this->assertNotNull($billing);

        // Diskon harus dijumlah: 50000 + 30000 = 80000
        $this->assertEquals(80000, (float) $billing->discount_applied);
        $this->assertEquals(70000, (float) $billing->final_amount); // 150000 - 80000
    }

    /** @test */
    public function diskon_tidak_melebihi_100_persen_tagihan()
    {
        $student = Student::factory()->create(['special_status' => 'UMUM']);
        $student->specialStatuses()->sync([
            'KURANGMAMPU' => [],
            'PRESTASI1'   => [],
        ]);

        // Total diskon sengaja dibuat melebihi tagihan (150000)
        Discount::create([
            'fee_master_id'   => $this->feeMaster->id,
            'target_status'   => 'KURANGMAMPU',
            'discount_amount' => 100000,
        ]);
        Discount::create([
            'fee_master_id'   => $this->feeMaster->id,
            'target_status'   => 'PRESTASI1',
            'discount_amount' => 100000,
        ]);

        $billingService = app(BillingService::class);
        $student->load('specialStatuses');

        $billingService->generateBillingsForStudentWithCategories(
            $student,
            [$this->feeMaster->category->id]
        );

        $billing = Billing::where('student_id', $student->id)->first();
        $this->assertNotNull($billing);

        // final_amount tidak boleh negatif
        $this->assertGreaterThanOrEqual(0, (float) $billing->final_amount);
        // discount_applied tidak boleh melebihi original_amount
        $this->assertLessThanOrEqual(
            (float) $billing->original_amount,
            (float) $billing->discount_applied
        );
    }

    /** @test */
    public function santri_umum_tidak_mendapat_diskon()
    {
        $student = Student::factory()->create(['special_status' => 'UMUM']);
        // Tidak ada status khusus

        Discount::create([
            'fee_master_id'   => $this->feeMaster->id,
            'target_status'   => 'KURANGMAMPU',
            'discount_amount' => 50000,
        ]);

        $billingService = app(BillingService::class);

        $billingService->generateBillingsForStudentWithCategories(
            $student,
            [$this->feeMaster->category->id]
        );

        $billing = Billing::where('student_id', $student->id)->first();
        $this->assertNotNull($billing);
        $this->assertEquals(0, (float) $billing->discount_applied);
        $this->assertEquals(150000, (float) $billing->final_amount);
    }

    /** @test */
    public function santri_satu_status_hanya_dapat_diskon_status_miliknya()
    {
        $student = Student::factory()->create(['special_status' => 'UMUM']);
        $student->specialStatuses()->sync(['KURANGMAMPU' => []]);

        Discount::create([
            'fee_master_id'   => $this->feeMaster->id,
            'target_status'   => 'KURANGMAMPU',
            'discount_amount' => 50000,
        ]);
        Discount::create([
            'fee_master_id'   => $this->feeMaster->id,
            'target_status'   => 'PRESTASI1',
            'discount_amount' => 30000,
        ]);

        $billingService = app(BillingService::class);
        $student->load('specialStatuses');

        $billingService->generateBillingsForStudentWithCategories(
            $student,
            [$this->feeMaster->category->id]
        );

        $billing = Billing::where('student_id', $student->id)->first();

        // Hanya dapat diskon KURANGMAMPU saja
        $this->assertEquals(50000, (float) $billing->discount_applied);
        $this->assertEquals(100000, (float) $billing->final_amount);
    }

    // =========================================================
    // 3. PIVOT TABLE — DATABASE
    // =========================================================

    /** @test */
    public function tabel_pivot_student_special_statuses_tersedia()
    {
        $this->assertTrue(
            \Illuminate\Support\Facades\Schema::hasTable('student_special_statuses')
        );
    }

    /** @test */
    public function pivot_mencegah_duplikasi_status_pada_santri_yang_sama()
    {
        $student = Student::factory()->create(['special_status' => 'UMUM']);

        $student->specialStatuses()->sync(['KURANGMAMPU' => []]);
        // Sync lagi dengan status yang sama — tidak boleh error atau duplikat
        $student->specialStatuses()->sync(['KURANGMAMPU' => []]);

        $this->assertCount(1, $student->fresh()->specialStatuses);
    }

    // =========================================================
    // 4. LIVEWIRE — UI
    // =========================================================

    /** @test */
    public function student_form_menampilkan_checkbox_status_khusus()
    {
        $this->actingAs($this->admin);

        \Livewire\Livewire::test(\App\Livewire\StudentForm::class)
            ->assertSee('KURANGMAMPU')
            ->assertSee('PRESTASI1');
    }

    /** @test */
    public function student_form_load_status_lama_saat_edit()
    {
        $student = Student::factory()->create(['special_status' => 'UMUM']);
        $student->specialStatuses()->sync(['KURANGMAMPU' => []]);

        $this->actingAs($this->admin);

        \Livewire\Livewire::test(\App\Livewire\StudentForm::class, ['student' => $student])
            ->assertSet('special_statuses', ['KURANGMAMPU']);
    }

    /** @test */
    public function billing_index_filter_status_khusus_menggunakan_pivot()
    {
        $student = Student::factory()->create(['special_status' => 'UMUM']);
        $student->specialStatuses()->sync(['KURANGMAMPU' => []]);

        Billing::factory()->create([
            'student_id'     => $student->id,
            'visible_to_wali' => true,
        ]);

        $this->actingAs($this->admin);

        \Livewire\Livewire::test(\App\Livewire\BillingIndex::class)
            ->set('specialFilter', 'KURANGMAMPU')
            ->assertSee($student->full_name);
    }
}
