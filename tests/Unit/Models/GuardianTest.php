<?php

namespace Tests\Unit\Models;

use App\Models\Guardian;
use App\Models\Student;
use App\Models\User;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuardianTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test can create guardian
     */
    public function test_can_create_guardian()
    {
        $userData = [
            'name' => 'Bapak Wali',
            'email' => 'wali@example.com',
            'password' => bcrypt('password'),
            'role' => 'WALI_SANTRI',
        ];
        $user = User::create($userData);

        $guardian = Guardian::create([
            'user_id' => $user->id,
            'full_name' => 'Bapak Wali',
            'phone' => '081234567890',
            'address' => 'Jl. Guardian',
            'occupation' => 'Entrepreneur',
        ]);

        $this->assertDatabaseHas('guardians', [
            'user_id' => $user->id,
            'full_name' => 'Bapak Wali',
        ]);
    }

    /**
     * Test guardian has many students
     */
    public function test_guardian_has_many_students()
    {
        $guardian = Guardian::factory()->create();
        
        Student::factory(3)->create(['guardian_id' => $guardian->id]);

        $this->assertCount(3, $guardian->students);
    }

    /**
     * Test guardian belongs to user
     */
    public function test_guardian_belongs_to_user()
    {
        $user = User::factory()->create();
        $guardian = Guardian::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($guardian->user()->exists());
        $this->assertEquals($user->id, $guardian->user->id);
    }

    /**
     * Test cannot force delete guardian with students
     */
    public function test_cannot_force_delete_guardian_with_students()
    {
        $guardian = Guardian::factory()->create();
        Student::factory()->create(['guardian_id' => $guardian->id]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Tidak dapat menghapus wali yang memiliki santri terdaftar');

        $guardian->forceDelete();
    }

    /**
     * Test can force delete guardian without students
     */
    public function test_can_force_delete_guardian_without_students()
    {
        $guardian = Guardian::factory()->create();
        $guardianId = $guardian->id;

        $guardian->forceDelete();

        $this->assertNull(Guardian::withTrashed()->find($guardianId));
    }

    /**
     * Test guardian can be soft deleted
     */
    public function test_guardian_can_be_soft_deleted()
    {
        $guardian = Guardian::factory()->create();

        $guardian->delete();

        $this->assertSoftDeleted('guardians', ['id' => $guardian->id]);
    }
}
