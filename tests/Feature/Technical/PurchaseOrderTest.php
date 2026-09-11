<?php

namespace Tests\Feature\Technical;

use App\Models\Equipment;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class PurchaseOrderTest extends TestCase
{
    use RefreshDatabase;

    private User $technician;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
        $this->technician = User::factory()->create();
        $this->technician->assignRole('tehnic');
    }

    public function test_technician_can_create_and_receive_purchase_order(): void
    {
        $supplier = Supplier::create(['name' => 'Furnizor PO']);
        $equipment = Equipment::factory()->create(['stock_quantity' => 2, 'cost_price' => 100]);

        $this->actingAs($this->technician)
            ->post(route('technical.purchase-orders.store'), [
                'supplier_id' => $supplier->id,
                'ordered_at' => '2026-09-11',
                'items' => [['equipment_id' => $equipment->id, 'quantity' => 3, 'unit_cost' => 110]],
            ])
            ->assertRedirect(route('technical.purchase-orders.index'));

        $order = PurchaseOrder::first();
        $this->actingAs($this->technician)
            ->patch(route('technical.purchase-orders.receive', $order))
            ->assertRedirect();

        $this->assertDatabaseHas('purchase_orders', ['id' => $order->id, 'status' => 'received']);
        $this->assertDatabaseHas('equipment', ['id' => $equipment->id, 'stock_quantity' => 5, 'cost_price' => 110]);
        $this->assertDatabaseHas('expenses', ['document_number' => $order->order_number, 'amount' => 330]);
        $this->assertDatabaseHas('audit_logs', ['action' => 'purchase_order.created', 'auditable_id' => $order->id]);
        $this->assertDatabaseHas('audit_logs', ['action' => 'purchase_order.received', 'auditable_id' => $order->id]);
    }

    public function test_technician_can_generate_replenishment_orders_from_low_stock(): void
    {
        $supplier = Supplier::create(['name' => 'Furnizor reaprovizionare']);
        Equipment::factory()->create([
            'supplier_id' => $supplier->id,
            'stock_quantity' => 2,
            'minimum_stock' => 5,
            'cost_price' => 80,
            'is_active' => true,
        ]);
        Equipment::factory()->create(['stock_quantity' => 1, 'minimum_stock' => 5, 'supplier_id' => null]);

        $this->actingAs($this->technician)
            ->post(route('technical.purchase-orders.replenish'))
            ->assertRedirect();

        $order = PurchaseOrder::with('items')->first();
        $this->assertSame('draft', $order->status);
        $this->assertCount(1, $order->items);
        $this->assertSame(8.0, (float) $order->items->first()->quantity);
        $this->assertDatabaseHas('audit_logs', ['action' => 'purchase_order.replenishment_created']);
    }

    public function test_draft_order_can_be_edited_with_supplier_document_and_cancelled(): void
    {
        $supplier = Supplier::create(['name' => 'Furnizor editare']);
        $equipment = Equipment::factory()->create();
        $order = PurchaseOrder::create([
            'supplier_id' => $supplier->id,
            'order_number' => 'PO-EDIT-1',
            'status' => 'draft',
            'total_amount' => 100,
        ]);
        $order->items()->create(['equipment_id' => $equipment->id, 'quantity' => 2, 'unit_cost' => 50]);

        $this->actingAs($this->technician)
            ->put(route('technical.purchase-orders.update', $order), [
                'supplier_id' => $supplier->id,
                'ordered_at' => '2026-09-11',
                'supplier_invoice_number' => 'FACT-99',
                'items' => [['equipment_id' => $equipment->id, 'quantity' => 3, 'unit_cost' => 55]],
                'document' => UploadedFile::fake()->create('factura.pdf', 20, 'application/pdf'),
            ])
            ->assertRedirect(route('technical.purchase-orders.show', $order));

        $order->refresh();
        $this->assertSame('FACT-99', $order->supplier_invoice_number);
        $this->assertSame(165.0, (float) $order->total_amount);
        $this->assertNotNull($order->document_path);

        $this->actingAs($this->technician)
            ->patch(route('technical.purchase-orders.cancel', $order))
            ->assertRedirect();

        $this->assertDatabaseHas('purchase_orders', ['id' => $order->id, 'status' => 'cancelled']);
    }
}
