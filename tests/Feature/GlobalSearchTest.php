<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Offer;
use App\Models\Ticket;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GlobalSearchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('search', ['q' => 'test']))->assertRedirect(route('login'));
    }

    public function test_short_queries_return_empty_results(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get(route('search', ['q' => 'a']));

        $response->assertJson(['clients' => [], 'offers' => [], 'tickets' => []]);
    }

    public function test_admin_sees_results_from_all_modules(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $client = Client::factory()->create(['name' => 'Zebra Client']);
        Offer::factory()->create(['client_id' => $client->id, 'title' => 'Zebra Offer']);
        Ticket::factory()->create(['client_id' => $client->id, 'subject' => 'Zebra Ticket']);

        $response = $this->actingAs($admin)->get(route('search', ['q' => 'Zebra']));

        $response->assertJsonCount(1, 'clients');
        $response->assertJsonCount(1, 'offers');
        $response->assertJsonCount(1, 'tickets');
    }

    public function test_sales_user_does_not_see_tickets_in_search_results(): void
    {
        $sales = User::factory()->create();
        $sales->assignRole('vanzari');

        $client = Client::factory()->create(['name' => 'Zebra Client']);
        Ticket::factory()->create(['client_id' => $client->id, 'subject' => 'Zebra Ticket']);

        $response = $this->actingAs($sales)->get(route('search', ['q' => 'Zebra']));

        $response->assertJsonCount(0, 'tickets');
    }

    public function test_technician_does_not_see_offers_in_search_results(): void
    {
        $tech = User::factory()->create();
        $tech->assignRole('tehnic');

        $client = Client::factory()->create(['name' => 'Zebra Client']);
        Offer::factory()->create(['client_id' => $client->id, 'title' => 'Zebra Offer']);

        $response = $this->actingAs($tech)->get(route('search', ['q' => 'Zebra']));

        $response->assertJsonCount(0, 'offers');
    }
}
