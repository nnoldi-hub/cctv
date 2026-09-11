<?php

namespace Tests\Feature\Technical;

use App\Models\Equipment;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
    }
}
