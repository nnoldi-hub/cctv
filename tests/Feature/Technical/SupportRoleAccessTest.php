<?php

namespace Tests\Feature\Technical;

use App\Models\Ticket;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupportRoleAccessTest extends TestCase
{
    use RefreshDatabase;

    private User $supportUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
        $this->supportUser = User::factory()->create();
        $this->supportUser->assignRole('suport');
    }

    public function test_support_user_can_access_technical_dashboard(): void
    {
        $this->actingAs($this->supportUser)
            ->get(route('technical.dashboard'))
            ->assertOk();
    }

    public function test_support_user_can_manage_tickets(): void
    {
        $this->actingAs($this->supportUser)
            ->get(route('technical.tickets.index'))
            ->assertOk();

        $ticket = Ticket::factory()->create();

        $this->actingAs($this->supportUser)
            ->patch(route('technical.tickets.status', $ticket), ['status' => 'resolved'])
            ->assertRedirect();
    }

    public function test_support_user_cannot_manage_equipment_or_installations(): void
    {
        $this->actingAs($this->supportUser)
            ->get(route('technical.equipment.index'))
            ->assertForbidden();

        $this->actingAs($this->supportUser)
            ->get(route('technical.installations.index'))
            ->assertForbidden();
    }
}
