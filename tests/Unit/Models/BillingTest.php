<?php

namespace Tests\Unit\Models;

use App\Models\Billing;
use App\Models\Student;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BillingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test creating a billing
     */
    public function test_can_create_billing()
    {
        $student = Student::factory()->create();

        $billing = Billing::create([
            'student_id' => $student->id,
            'title' => 'Test Billing',
            'original_amount' => 1000000,
            'discount_applied' => 0,
            'final_amount' => 1000000,
            'status' => 'UNPAID',
        ]);

        $this->assertDatabaseHas('billings', [
            'student_id' => $student->id,
            'title' => 'Test Billing',
            'status' => 'UNPAID',
        ]);
    }

    /**
     * Test billing status transitions
     */
    public function test_billing_status_can_change_from_unpaid_to_paid()
    {
        $billing = Billing::factory()->create(['status' => 'UNPAID']);

        $billing->update(['status' => 'PAID']);

        $this->assertEquals('PAID', $billing->fresh()->status);
    }

    /**
     * Test cannot modify paid billing
     */
    public function test_cannot_update_paid_billing()
    {
        $billing = Billing::factory()->create(['status' => 'PAID']);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Tagihan yang sudah dibayar tidak dapat diubah');

        $billing->update(['title' => 'New Title']);
    }

    /**
     * Test cannot delete paid billing
     */
    public function test_cannot_delete_paid_billing()
    {
        $billing = Billing::factory()->create(['status' => 'PAID']);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Tidak dapat menghapus tagihan yang sudah dibayar');

        $billing->delete();
    }

    /**
     * Test billing discount calculation
     */
    public function test_billing_calculates_final_amount_correctly()
    {
        $billing = Billing::create([
            'student_id' => Student::factory()->create()->id,
            'title' => 'Test with Discount',
            'original_amount' => 1000000,
            'discount_applied' => 250000,
            'final_amount' => 750000,
            'status' => 'UNPAID',
        ]);

        $expectedFinalAmount = 1000000 - 250000;
        $this->assertEquals($expectedFinalAmount, $billing->final_amount);
    }

    /**
     * Test billing belongs to student
     */
    public function test_billing_belongs_to_student()
    {
        $student = Student::factory()->create();
        $billing = Billing::factory()->create(['student_id' => $student->id]);

        $this->assertTrue($billing->student()->exists());
        $this->assertEquals($student->id, $billing->student->id);
    }

    /**
     * Test billing can have multiple versions
     */
    public function test_billing_can_have_versions()
    {
        $student = Student::factory()->create();
        
        $billing1 = Billing::create([
            'student_id' => $student->id,
            'title' => 'Original',
            'original_amount' => 1000000,
            'discount_applied' => 0,
            'final_amount' => 1000000,
            'status' => 'UNPAID',
            'version' => 1,
        ]);

        $billing2 = Billing::create([
            'student_id' => $student->id,
            'title' => 'Updated',
            'original_amount' => 1200000,
            'discount_applied' => 0,
            'final_amount' => 1200000,
            'status' => 'UNPAID',
            'version' => 2,
            'version_of' => $billing1->id,
        ]);

        $this->assertEquals(2, $billing2->version);
        $this->assertEquals($billing1->id, $billing2->version_of);
    }

    /**
     * Test soft delete on billing
     */
    public function test_billing_can_be_soft_deleted()
    {
        $billing = Billing::factory()->create(['status' => 'UNPAID']);

        $billing->delete();

        $this->assertSoftDeleted('billings', ['id' => $billing->id]);
    }
}
