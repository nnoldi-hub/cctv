<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Equipment;
use App\Models\Installation;
use App\Models\Invoice;
use App\Models\Offer;
use App\Models\Ticket;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'clients' => Client::count(),
                'offers' => Offer::count(),
                'users' => User::count(),
                'invoicesUnpaid' => Invoice::where('status', 'unpaid')->count(),
                'revenuePaid' => (float) Invoice::where('status', 'paid')->sum('amount'),
            ],
            'alerts' => $this->buildAlerts(),
            'recentLeads' => Client::where('status', 'lead')
                ->latest()
                ->take(5)
                ->get(['id', 'name', 'phone', 'email', 'source', 'notes', 'created_at']),
            'pendingOffers' => Offer::with('client:id,name')
                ->whereIn('status', ['draft', 'sent'])
                ->latest()
                ->take(5)
                ->get(['id', 'client_id', 'title', 'status', 'total_amount', 'created_at']),
            'activeInstallations' => Installation::with('client:id,name')
                ->whereIn('status', ['scheduled', 'in_progress'])
                ->orderBy('scheduled_at')
                ->take(5)
                ->get(['id', 'client_id', 'type', 'status', 'scheduled_at']),
            'openTickets' => Ticket::with('client:id,name')
                ->whereIn('status', ['open', 'in_progress'])
                ->latest()
                ->take(5)
                ->get(['id', 'client_id', 'subject', 'priority', 'status']),
        ]);
    }

    private function buildAlerts(): array
    {
        $alerts = [];

        $lowStockCount = Equipment::where('stock_quantity', '<', 5)->count();
        if ($lowStockCount > 0) {
            $alerts[] = [
                'type' => 'warning',
                'message' => "{$lowStockCount} echipamente cu stoc sub 5 bucati.",
                'href' => route('technical.equipment.index', ['low_stock' => true]),
            ];
        }

        $overdueInvoices = Invoice::where('status', 'unpaid')->where('due_at', '<', now())->count();
        if ($overdueInvoices > 0) {
            $alerts[] = [
                'type' => 'danger',
                'message' => "{$overdueInvoices} facturi restante (scadenta depasita).",
                'href' => route('admin.invoices.index', ['status' => 'unpaid']),
            ];
        }

        $highPriorityOpenTickets = Ticket::where('priority', 'high')->whereIn('status', ['open', 'in_progress'])->count();
        if ($highPriorityOpenTickets > 0) {
            $alerts[] = [
                'type' => 'danger',
                'message' => "{$highPriorityOpenTickets} tichete cu prioritate ridicata deschise.",
                'href' => route('technical.tickets.index', ['priority' => 'high']),
            ];
        }

        return $alerts;
    }
}
