<?php

namespace Tests\Feature;

use App\Models\Billing;
use App\Models\Payment;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FinancialReportTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $bendahara;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'SUPER_ADMIN']);
        $this->bendahara = User::factory()->create(['role' => 'BENDAHARA']);
    }

    /**
     * Test admin and bendahara can view financial report
     */
    public function test_authorized_users_can_access_financial_report()
    {
        $this->actingAs($this->admin)
            ->get('/admin/reports/financial')
            ->assertStatus(200);

        $this->actingAs($this->bendahara)
            ->get('/admin/reports/financial')
            ->assertStatus(200);
    }

    /**
     * Test financial report calculates cash and cashless incomes separately
     */
    public function test_financial_report_splits_cash_and_cashless_income()
    {
        $student = Student::factory()->create();
        
        $billing1 = Billing::create([
            'student_id' => $student->id,
            'title' => 'SPP',
            'original_amount' => 500000,
            'discount_applied' => 0,
            'final_amount' => 500000,
            'status' => 'PAID',
        ]);

        $billing2 = Billing::create([
            'student_id' => $student->id,
            'title' => 'Uang Buku',
            'original_amount' => 300000,
            'discount_applied' => 0,
            'final_amount' => 300000,
            'status' => 'PAID',
        ]);

        Payment::create([
            'billing_id' => $billing1->id,
            'amount' => 500000,
            'method' => 'cash',
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        Payment::create([
            'billing_id' => $billing2->id,
            'amount' => 300000,
            'method' => 'duitku',
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        Livewire::actingAs($this->admin)
            ->test(\App\Livewire\FinancialReport::class)
            ->assertViewHas('cashIncome', 500000)
            ->assertViewHas('cashlessIncome', 300000)
            ->assertViewHas('totalIncome', 800000)
            ->assertViewHas('totalTransactions', 2);
    }

    /**
     * Test print view can be generated with date filters
     */
    public function test_financial_report_print_view()
    {
        $student = Student::factory()->create();
        $billing = Billing::create([
            'student_id' => $student->id,
            'title' => 'SPP',
            'original_amount' => 500000,
            'discount_applied' => 0,
            'final_amount' => 500000,
            'status' => 'PAID',
        ]);

        Payment::create([
            'billing_id' => $billing->id,
            'amount' => 500000,
            'method' => 'cash',
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.reports.financial.print', [
                'startDate' => date('Y-m-d'),
                'endDate' => date('Y-m-d')
            ]));

        $response->assertStatus(200);
        $response->assertSee('Bagian 1: Pembayaran Tunai (Cash)');
        $response->assertSee('Bagian 2: Pembayaran Cashless (Duitku)');
        $response->assertSee('Rp 500.000');
    }

    /**
     * Test excel export returns CSV stream
     */
    public function test_financial_report_excel_export()
    {
        $student = Student::factory()->create();
        $billing = Billing::create([
            'student_id' => $student->id,
            'title' => 'SPP',
            'original_amount' => 500000,
            'discount_applied' => 0,
            'final_amount' => 500000,
            'status' => 'PAID',
        ]);

        Payment::create([
            'billing_id' => $billing->id,
            'amount' => 500000,
            'method' => 'cash',
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        Livewire::actingAs($this->admin)
            ->test(\App\Livewire\FinancialReport::class)
            ->call('exportExcel')
            ->assertFileDownloaded('laporan-keuangan-' . now()->format('Y-m-d') . '.xlsx');
    }
}
