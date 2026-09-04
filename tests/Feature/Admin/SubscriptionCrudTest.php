<?php

namespace Tests\Feature\Admin;

use App\Models\Client;
use App\Models\Subscription;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionCrudTest extends TestCase
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

    public function test_admin_can_create_a_subscription(): void
    {
        $client = Client::factory()->create();

        $response = $this->actingAs($this->adminUser)->post(route('admin.subscriptions.store'), [
            'client_id' => $client->id,
            'plan' => 'Mentenanta premium',
            'price' => 150,
            'billing_cycle' => 'monthly',
            'status' => 'active',
            'started_at' => now()->format('Y-m-d'),
        ]);

        $response->assertRedirect(route('admin.subscriptions.index'));
        $this->assertDatabaseHas('subscriptions', ['plan' => 'Mentenanta premium', 'price' => 150]);
    }

    public function test_monthly_recurring_revenue_normalizes_yearly_subscriptions(): void
    {
        $client = Client::factory()->create();
        Subscription::factory()->create(['client_id' => $client->id, 'billing_cycle' => 'monthly', 'price' => 100, 'status' => 'active']);
        Subscription::factory()->create(['client_id' => $client->id, 'billing_cycle' => 'yearly', 'price' => 1200, 'status' => 'active']);

        $response = $this->actingAs($this->adminUser)->get(route('admin.subscriptions.index'));

        $response->assertInertia(fn ($page) => $page
            ->component('Admin/Subscriptions/Index')
            ->where('monthlyRecurringRevenue', 200)
        );
    }

    public function test_subscriptions_export_downloads_a_file(): void
    {
        Subscription::factory()->count(2)->create();

        $this->actingAs($this->adminUser)
            ->get(route('admin.subscriptions.export'))
            ->assertOk();
    }

    public function test_admin_can_update_subscription_status(): void
    {
        $subscription = Subscription::factory()->create(['status' => 'active']);

        $this->actingAs($this->adminUser)->put(route('admin.subscriptions.update', $subscription), [
            'client_id' => $subscription->client_id,
            'plan' => $subscription->plan,
            'price' => $subscription->price,
            'billing_cycle' => $subscription->billing_cycle,
            'status' => 'cancelled',
            'started_at' => $subscription->started_at->format('Y-m-d'),
        ])->assertRedirect(route('admin.subscriptions.index'));

        $this->assertDatabaseHas('subscriptions', ['id' => $subscription->id, 'status' => 'cancelled']);
    }
}
