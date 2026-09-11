<?php

namespace Tests\Feature\Client;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortalFinancialsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    private function actingClient(): array
    {
        $clientUser = User::factory()->create();
        $clientUser->assignRole('client');
        $client = Client::factory()->create(['user_id' => $clientUser->id]);

        return [$clientUser, $client];
    }

    public function test_dashboard_reports_correct_unpaid_invoice_count_and_balance(): void
    {
        [$clientUser, $client] = $this->actingClient();

        Invoice::factory()->create(['client_id' => $client->id, 'status' => 'unpaid', 'amount' => 1000, 'paid_amount' => 200]);
        Invoice::factory()->create(['client_id' => $client->id, 'status' => 'overdue', 'amount' => 500, 'paid_amount' => 0]);
        Invoice::factory()->create(['client_id' => $client->id, 'status' => 'paid', 'amount' => 300, 'paid_amount' => 300]);

        $response = $this->actingAs($clientUser)->get(route('client.dashboard'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->where('unpaidInvoices', 2)
            ->where('balance', 1300)
        );
    }

    public function test_invoices_index_returns_financial_summary(): void
    {
        [$clientUser, $client] = $this->actingClient();

        Invoice::factory()->create(['client_id' => $client->id, 'status' => 'unpaid', 'amount' => 1000, 'paid_amount' => 400]);
        Invoice::factory()->create(['client_id' => $client->id, 'status' => 'paid', 'amount' => 300, 'paid_amount' => 300]);

        $response = $this->actingAs($clientUser)->get(route('client.invoices.index'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->where('summary.invoiceTotal', 1300)
            ->where('summary.invoicePaid', 700)
            ->where('summary.invoiceBalance', 600)
        );
    }

    public function test_client_can_download_own_financial_statement_pdf(): void
    {
        [$clientUser, $client] = $this->actingClient();

        Invoice::factory()->create(['client_id' => $client->id, 'status' => 'unpaid', 'amount' => 1000, 'paid_amount' => 0]);

        $response = $this->actingAs($clientUser)->get(route('client.invoices.statement'));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_client_cannot_download_other_clients_invoice(): void
    {
        [$clientUser, $client] = $this->actingClient();
        $otherClient = Client::factory()->create();
        $otherInvoice = Invoice::factory()->create(['client_id' => $otherClient->id]);

        $this->actingAs($clientUser)
            ->get(route('client.invoices.pdf', $otherInvoice))
            ->assertNotFound();
    }
}
