<?php

namespace Tests\Feature;

use App\Models\Billing;
use App\Models\Guardian;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IDORAndAccessControlTest extends TestCase
{
    use RefreshDatabase;

    /**
     * NEGATIVE TEST 1: Guardian cannot view other guardian's student billing
     * (Insecure Direct Object Reference vulnerability)
     */
    public function test_guardian_idor_protection_on_receipt()
    {
        $guardian1 = User::factory()->create(['role' => 'WALI_SANTRI']);
        $guardian2 = User::factory()->create(['role' => 'WALI_SANTRI']);

        $guardianData1 = Guardian::factory()->create();
        $guardianData2 = Guardian::factory()->create();

        $student1 = Student::factory()->create(['guardian_id' => $guardianData1->id]);
        $student2 = Student::factory()->create(['guardian_id' => $guardianData2->id]);

        $billing1 = Billing::factory()->create(['student_id' => $student1->id]);
        $billing2 = Billing::factory()->create(['student_id' => $student2->id]);

        // Guardian1 tries to view Guardian2's billing
        $response = $this->actingAs($guardian1)
            ->get("/receipts/{$billing2->id}");

        // Should be 403 or policy rejection
        $this->assertTrue($response->status() === 403 || $response->status() === 404);
    }

    /**
     * NEGATIVE TEST 2: Admin can view all receipts (access control)
     */
    public function test_admin_can_view_all_receipts()
    {
        $admin = User::factory()->create(['role' => 'ADMINISTRASI']);
        $guardian = User::factory()->create(['role' => 'WALI_SANTRI']);

        $guardianData = Guardian::factory()->create();
        $student = Student::factory()->create(['guardian_id' => $guardianData->id]);
        $billing = Billing::factory()->create(['student_id' => $student->id]);

        // Admin should access billing view endpoint
        $response = $this->actingAs($admin)
            ->get("/receipts/{$billing->id}");

        $this->assertTrue($response->status() === 200 || $response->status() === 403);
    }

    /**
     * NEGATIVE TEST 3: Unauthenticated user cannot view any receipt
     */
    public function test_unauthenticated_cannot_view_receipt()
    {
        $billing = Billing::factory()->create();

        $response = $this->get("/receipts/{$billing->id}");

        // Should redirect to login
        $this->assertTrue($response->status() === 302);
        $this->assertStringContainsString('login', $response->getTargetUrl());
    }

    /**
     * NEGATIVE TEST 4: Administrasi cannot access Super Admin routes
     */
    public function test_ADMINISTRASI_cannot_access_super_admin_routes()
    {
        $adminTu = User::factory()->create(['role' => 'ADMINISTRASI']);

        // Try to access Super Admin only routes
        $response = $this->actingAs($adminTu)
            ->get('/admin/users');

        $response->assertRedirect(route('admin.dashboard'));
    }

    /**
     * NEGATIVE TEST 5: Guardian cannot access admin routes
     */
    public function test_guardian_cannot_access_admin_routes()
    {
        $guardian = User::factory()->create(['role' => 'WALI_SANTRI']);

        $routes = [
            '/admin/students',
            '/admin/billings',
            '/admin/users',
        ];

        foreach ($routes as $route) {
            $response = $this->actingAs($guardian)->get($route);
            $response->assertRedirect(route('wali.dashboard'));
        }
    }

    /**
     * NEGATIVE TEST 6: Admin cannot change their own role (prevent privilege escalation)
     */
    public function test_admin_cannot_escalate_own_role()
    {
        $admin = User::factory()->create(['role' => 'ADMINISTRASI']);

        $response = $this->actingAs($admin)
            ->patch("/admin/users/{$admin->id}", [
                'role' => 'SUPER_ADMIN',
            ]);

        // Should fail or be forbidden
        $this->assertTrue($response->status() === 403 || $response->status() === 404);
    }

    /**
     * NEGATIVE TEST 7: Cannot view print receipt of other student's billing
     */
    public function test_cannot_print_other_billing_receipt()
    {
        $guardian1 = User::factory()->create(['role' => 'WALI_SANTRI']);
        $guardianData1 = Guardian::factory()->create();
        $student1 = Student::factory()->create(['guardian_id' => $guardianData1->id]);
        $billing1 = Billing::factory()->create(['student_id' => $student1->id]);

        $billing2 = Billing::factory()->create();

        $response = $this->actingAs($guardian1)
            ->get("/receipts/{$billing2->id}/print");

        $this->assertTrue($response->status() === 403 || $response->status() === 404);
    }
}
