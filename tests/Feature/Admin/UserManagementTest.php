<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
        $this->adminUser = User::factory()->create();
        $this->adminUser->assignRole('admin');
    }

    public function test_admin_can_create_a_user_with_a_role(): void
    {
        $response = $this->actingAs($this->adminUser)->post(route('admin.users.store'), [
            'name' => 'Noul Tehnician',
            'email' => 'noul.tehnician@example.com',
            'password' => 'password123',
            'role' => 'tehnic',
        ]);

        $response->assertRedirect(route('admin.users.index'));

        $user = User::firstWhere('email', 'noul.tehnician@example.com');
        $this->assertNotNull($user);
        $this->assertTrue($user->hasRole('tehnic'));
    }

    public function test_admin_created_user_is_pre_verified_and_can_access_their_module_immediately(): void
    {
        $this->actingAs($this->adminUser)->post(route('admin.users.store'), [
            'name' => 'Noul Tehnician',
            'email' => 'noul.tehnician@example.com',
            'password' => 'password123',
            'role' => 'tehnic',
        ]);

        $user = User::firstWhere('email', 'noul.tehnician@example.com');
        $this->assertNotNull($user->email_verified_at);

        $this->actingAs($user)
            ->get(route('technical.dashboard'))
            ->assertOk();
    }

    public function test_admin_can_change_a_users_role(): void
    {
        $user = User::factory()->create();
        $user->assignRole('suport');

        $this->actingAs($this->adminUser)->put(route('admin.users.update', $user), [
            'name' => $user->name,
            'email' => $user->email,
            'role' => 'vanzari',
        ])->assertRedirect(route('admin.users.index'));

        $user->refresh();
        $this->assertTrue($user->hasRole('vanzari'));
        $this->assertFalse($user->hasRole('suport'));
    }

    public function test_admin_cannot_delete_their_own_account(): void
    {
        $this->actingAs($this->adminUser)
            ->delete(route('admin.users.destroy', $this->adminUser))
            ->assertStatus(422);

        $this->assertDatabaseHas('users', ['id' => $this->adminUser->id]);
    }

    public function test_admin_can_delete_another_user(): void
    {
        $user = User::factory()->create();
        $user->assignRole('suport');

        $this->actingAs($this->adminUser)
            ->delete(route('admin.users.destroy', $user))
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }
}
