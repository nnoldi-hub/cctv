<?php

namespace Tests\Feature\Technical;

use App\Models\Client;
use App\Models\Installation;
use App\Models\Invoice;
use App\Models\Offer;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InstallationCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $techUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
        $this->techUser = User::factory()->create();
        $this->techUser->assignRole('tehnic');
    }

    public function test_technician_can_create_installation_with_default_checklist(): void
    {
        $client = Client::factory()->create();

        $response = $this->actingAs($this->techUser)->post(route('technical.installations.store'), [
            'client_id' => $client->id,
            'type' => 'instalare',
            'address' => 'Str. Exemplu nr. 1',
            'status' => 'scheduled',
        ]);

        $installation = Installation::firstWhere('client_id', $client->id);
        $response->assertRedirect(route('technical.installations.show', $installation));

        $this->assertNotEmpty($installation->checklist);
        $this->assertFalse($installation->checklist[0]['done']);
    }

    public function test_technician_can_toggle_checklist_item(): void
    {
        $installation = Installation::factory()->create();

        $this->actingAs($this->techUser)
            ->patch(route('technical.installations.checklist', $installation), ['index' => 0, 'done' => true])
            ->assertRedirect();

        $installation->refresh();
        $this->assertTrue($installation->checklist[0]['done']);
    }

    public function test_toggling_one_checklist_item_does_not_affect_others(): void
    {
        $installation = Installation::factory()->create();

        $this->actingAs($this->techUser)
            ->patch(route('technical.installations.checklist', $installation), ['index' => 0, 'done' => true]);
        $this->actingAs($this->techUser)
            ->patch(route('technical.installations.checklist', $installation), ['index' => 2, 'done' => true]);

        $installation->refresh();
        $this->assertTrue($installation->checklist[0]['done']);
        $this->assertFalse($installation->checklist[1]['done']);
        $this->assertTrue($installation->checklist[2]['done']);
    }

    public function test_technician_can_update_installation_status(): void
    {
        $installation = Installation::factory()->create(['status' => 'scheduled']);

        $this->actingAs($this->techUser)
            ->patch(route('technical.installations.status', $installation), ['status' => 'completed'])
            ->assertRedirect();

        $this->assertDatabaseHas('installations', ['id' => $installation->id, 'status' => 'completed']);
    }

    public function test_completing_an_installation_linked_to_an_offer_creates_an_invoice(): void
    {
        $client = Client::factory()->create();
        $offer = Offer::factory()->create(['client_id' => $client->id, 'total_amount' => 2500]);
        $installation = Installation::factory()->create(['client_id' => $client->id, 'offer_id' => $offer->id, 'status' => 'in_progress']);

        $this->actingAs($this->techUser)
            ->patch(route('technical.installations.status', $installation), ['status' => 'completed']);

        $invoice = Invoice::firstWhere('offer_id', $offer->id);
        $this->assertNotNull($invoice);
        $this->assertEquals(2500, $invoice->amount);
        $this->assertEquals('unpaid', $invoice->status);
    }

    public function test_completing_an_installation_without_an_offer_does_not_create_an_invoice(): void
    {
        $installation = Installation::factory()->create(['offer_id' => null, 'status' => 'in_progress']);

        $this->actingAs($this->techUser)
            ->patch(route('technical.installations.status', $installation), ['status' => 'completed']);

        $this->assertDatabaseCount('invoices', 0);
    }

    public function test_completing_an_installation_twice_does_not_duplicate_the_invoice(): void
    {
        $offer = Offer::factory()->create(['total_amount' => 1000]);
        $installation = Installation::factory()->create(['offer_id' => $offer->id, 'status' => 'in_progress']);

        $this->actingAs($this->techUser)->patch(route('technical.installations.status', $installation), ['status' => 'completed']);
        $this->actingAs($this->techUser)->patch(route('technical.installations.status', $installation), ['status' => 'in_progress']);
        $this->actingAs($this->techUser)->patch(route('technical.installations.status', $installation), ['status' => 'completed']);

        $this->assertEquals(1, Invoice::where('offer_id', $offer->id)->count());
    }

    public function test_installation_report_pdf_can_be_downloaded(): void
    {
        $installation = Installation::factory()->create();

        $this->actingAs($this->techUser)
            ->get(route('technical.installations.pdf', $installation))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_installations_can_be_filtered_by_type(): void
    {
        Installation::factory()->create(['type' => 'instalare']);
        Installation::factory()->create(['type' => 'interventie']);

        $response = $this->actingAs($this->techUser)
            ->get(route('technical.installations.index', ['type' => 'interventie']));

        $response->assertInertia(fn ($page) => $page
            ->component('Technical/Installations/Index')
            ->has('installations.data', 1)
        );
    }
}
