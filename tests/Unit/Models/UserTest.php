<?php

namespace Tests\Unit\Models;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test creating a user with valid data
     */
    public function test_can_create_user_with_valid_data()
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password123'),
            'role' => 'SUPER_ADMIN',
        ];

        $user = User::create($userData);

        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'role' => 'SUPER_ADMIN',
        ]);
        $this->assertEquals($userData['name'], $user->name);
    }

    /**
     * Test user with different roles
     */
    public function test_user_can_have_different_roles()
    {
        $roles = ['SUPER_ADMIN', 'ADMIN_TU', 'WALI_SANTRI'];

        foreach ($roles as $role) {
            $user = User::factory()->create(['role' => $role]);
            $this->assertEquals($role, $user->role);
        }
    }

    /**
     * Test password is hashed
     */
    public function test_password_is_hashed()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'whatsapp' => '081234567891',
            'password' => bcrypt('password123'),
            'role' => 'ADMIN_TU',
        ]);

        $this->assertTrue(password_verify('password123', $user->password));
    }

    /**
     * Test user email is unique
     */
    public function test_user_email_must_be_unique()
    {
        User::factory()->create(['email' => 'unique@example.com', 'whatsapp' => '081234567892']);

        $this->expectException(\Exception::class);
        User::create([
            'name' => 'Another User',
            'email' => 'unique@example.com',
            'whatsapp' => '081234567893',
            'password' => bcrypt('password'),
            'role' => 'ADMIN_TU',
        ]);
    }

    /**
     * Test updating user information
     */
    public function test_can_update_user_information()
    {
        $user = User::factory()->create();
        $newName = 'Updated Name';

        $user->update(['name' => $newName]);

        $this->assertEquals($newName, $user->fresh()->name);
    }

    /**
     * Test deleting a user
     */
    public function test_can_delete_user()
    {
        $user = User::factory()->create();
        $userId = $user->id;

        $user->delete();

        $this->assertNull(User::find($userId));
    }
}
