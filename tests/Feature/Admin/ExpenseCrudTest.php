<?php

namespace Tests\Feature\Admin;

use App\Models\Expense;
use App\Models\Installation;
use App\Models\Supplier;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ExpenseCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
    }

    public function test_admin_can_record_supplier_expense_for_installation(): void
    {
        $supplier = Supplier::create(['name' => 'Furnizor test']);
        $installation = Installation::factory()->create();

        $this->actingAs($this->admin)
            ->post(route('admin.expenses.store'), [
                'supplier_id' => $supplier->id,
                'installation_id' => $installation->id,
                'description' => 'Material suplimentar',
                'category' => 'material',
                'amount' => 275.50,
                'expense_date' => '2026-09-10',
                'document_number' => 'F-100',
            ])
            ->assertRedirect(route('admin.expenses.index'));

        $this->assertDatabaseHas('expenses', [
            'supplier_id' => $supplier->id,
            'installation_id' => $installation->id,
            'amount' => 275.50,
        ]);
        $this->assertSame(275.50, $installation->fresh()->actual_expense_total);
    }

    public function test_sales_user_cannot_access_expenses(): void
    {
        $sales = User::factory()->create();
        $sales->assignRole('vanzari');

        $this->actingAs($sales)->get(route('admin.expenses.index'))->assertForbidden();
    }

    public function test_admin_can_edit_expense(): void
    {
        $expense = Expense::create([
            'description' => 'Cost initial',
            'category' => 'other',
            'amount' => 100,
            'expense_date' => '2026-09-10',
        ]);

        $this->actingAs($this->admin)
            ->put(route('admin.expenses.update', $expense), [
                'description' => 'Transport actualizat',
                'category' => 'transport',
                'amount' => 150,
                'expense_date' => '2026-09-11',
            ])
            ->assertRedirect(route('admin.expenses.index'));

        $this->assertDatabaseHas('expenses', ['id' => $expense->id, 'description' => 'Transport actualizat', 'amount' => 150]);
    }

    public function test_admin_can_upload_expense_document(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin)
            ->post(route('admin.expenses.store'), [
                'description' => 'Factura furnizor',
                'category' => 'material',
                'amount' => 450,
                'expense_date' => '2026-09-10',
                'document' => UploadedFile::fake()->create('factura.pdf', 100, 'application/pdf'),
            ])
            ->assertRedirect(route('admin.expenses.index'));

        $expense = Expense::first();
        $this->assertNotNull($expense->document_path);
        Storage::disk('public')->assertExists($expense->document_path);
    }
}
