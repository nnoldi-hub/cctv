<?php

namespace Tests\Feature\Admin;

use App\Models\Client;
use App\Models\Equipment;
use App\Models\Installation;
use App\Models\Invoice;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class OperationalRemindersTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_sends_reminders_for_overdue_invoices_installations_and_low_stock(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $technician = User::factory()->create();
        $technician->assignRole('tehnic');
        $client = Client::factory()->create();

        Invoice::factory()->create(['status' => 'overdue', 'amount' => 1000, 'paid_amount' => 200]);
        Installation::factory()->create([
            'client_id' => $client->id,
            'technician_id' => $technician->id,
            'status' => 'scheduled',
            'scheduled_at' => now()->addDay()->setTime(10, 0),
        ]);
        Equipment::factory()->create(['stock_quantity' => 1, 'minimum_stock' => 5]);

        Artisan::call('operations:send-reminders');

        $this->assertSame(1, $admin->notifications()->where('type', 'App\\Notifications\\OperationalReminder')->whereJsonContains('data->kind', 'overdue_invoices')->count());
        $this->assertSame(1, $technician->notifications()->whereJsonContains('data->kind', 'tomorrow_installation')->count());
        $this->assertSame(1, $admin->notifications()->whereJsonContains('data->kind', 'low_stock')->count());
    }
}
