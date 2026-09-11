<?php

namespace Tests\Feature\Admin;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceCrudTest extends TestCase
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

    public function test_sales_user_cannot_access_admin_module(): void
    {
        $sales = User::factory()->create();
        $sales->assignRole('vanzari');

        $this->actingAs($sales)->get(route('admin.invoices.index'))->assertForbidden();
    }

    public function test_admin_can_create_invoice_with_auto_generated_number(): void
    {
        $client = Client::factory()->create();

        $response = $this->actingAs($this->adminUser)->post(route('admin.invoices.store'), [
            'client_id' => $client->id,
            'amount' => 1500,
            'status' => 'unpaid',
        ]);

        $invoice = Invoice::firstWhere('client_id', $client->id);
        $response->assertRedirect(route('admin.invoices.show', $invoice));
        $this->assertNotEmpty($invoice->invoice_number);
        $this->assertEquals(1500, $invoice->amount);
    }

    public function test_admin_can_mark_invoice_as_paid(): void
    {
        $invoice = Invoice::factory()->create(['status' => 'unpaid']);

        $this->actingAs($this->adminUser)
            ->patch(route('admin.invoices.pay', $invoice))
            ->assertRedirect();

        $invoice->refresh();
        $this->assertEquals('paid', $invoice->status);
        $this->assertNotNull($invoice->paid_at);
        $this->assertEquals((float) $invoice->amount, (float) $invoice->paid_amount);
    }

    public function test_admin_can_record_payment_details(): void
    {
        $invoice = Invoice::factory()->create(['amount' => 1200, 'status' => 'unpaid']);

        $this->actingAs($this->adminUser)
            ->patch(route('admin.invoices.pay', $invoice), [
                'paid_amount' => 1200,
                'payment_method' => 'transfer',
                'payment_reference' => 'OP-123',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'status' => 'paid',
            'paid_amount' => 1200,
            'payment_method' => 'transfer',
            'payment_reference' => 'OP-123',
        ]);
    }

    public function test_admin_can_record_partial_payments_and_remaining_balance(): void
    {
        $invoice = Invoice::factory()->create(['amount' => 1000, 'status' => 'unpaid']);

        $this->actingAs($this->adminUser)
            ->patch(route('admin.invoices.pay', $invoice), ['paid_amount' => 300, 'payment_method' => 'transfer'])
            ->assertRedirect();

        $invoice->refresh();
        $this->assertSame('unpaid', $invoice->status);
        $this->assertSame(700.0, $invoice->remaining_amount);
        $this->assertDatabaseHas('invoice_payments', ['invoice_id' => $invoice->id, 'amount' => 300]);
        $this->assertDatabaseHas('audit_logs', ['action' => 'invoice.payment_recorded', 'auditable_id' => $invoice->id]);

        $this->actingAs($this->adminUser)
            ->patch(route('admin.invoices.pay', $invoice), ['paid_amount' => 700, 'payment_method' => 'cash'])
            ->assertRedirect();

        $this->assertDatabaseHas('invoices', ['id' => $invoice->id, 'status' => 'paid', 'paid_amount' => 1000]);
        $this->assertDatabaseCount('invoice_payments', 2);
    }

    public function test_overdue_unpaid_invoices_are_marked_overdue_when_listed(): void
    {
        $invoice = Invoice::factory()->create(['status' => 'unpaid', 'due_at' => now()->subDay()]);

        $this->actingAs($this->adminUser)->get(route('admin.invoices.index'))->assertOk();

        $this->assertDatabaseHas('invoices', ['id' => $invoice->id, 'status' => 'overdue']);
    }

    public function test_invoice_pdf_can_be_downloaded(): void
    {
        $invoice = Invoice::factory()->create();

        $this->actingAs($this->adminUser)
            ->get(route('admin.invoices.pdf', $invoice))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_invoices_export_downloads_a_file(): void
    {
        Invoice::factory()->count(2)->create();

        $this->actingAs($this->adminUser)
            ->get(route('admin.invoices.export'))
            ->assertOk();
    }

    public function test_invoice_summary_counts_overdue_correctly(): void
    {
        Invoice::factory()->create(['status' => 'unpaid', 'due_at' => now()->subDays(5)]);
        Invoice::factory()->create(['status' => 'unpaid', 'due_at' => now()->addDays(5)]);

        $response = $this->actingAs($this->adminUser)->get(route('admin.invoices.index'));

        $response->assertInertia(fn ($page) => $page
            ->component('Admin/Invoices/Index')
            ->where('summary.overdue', 1)
        );
    }
}
