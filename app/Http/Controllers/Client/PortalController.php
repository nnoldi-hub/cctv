<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\Ticket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Setting;
use Inertia\Inertia;
use Inertia\Response;

class PortalController extends Controller
{
    public function dashboard(Request $request): Response
    {
        $client = $request->user()->clientProfile;

        return Inertia::render('Client/Dashboard', [
            'client' => $client,
            'openTickets' => $client->tickets()->whereIn('status', ['open', 'in_progress'])->count(),
            'scheduledWorks' => $client->installations()->where('status', 'scheduled')->whereNotNull('scheduled_at')->count(),
            'unpaidInvoices' => $client->invoices()->whereIn('status', ['draft', 'issued', 'overdue'])->count(),
            'unreadNotifications' => $request->user()->unreadNotifications()->count(),
            'recentTickets' => $client->tickets()->with('assignedTo:id,name')->latest()->take(5)->get(),
        ]);
    }

    public function tickets(Request $request): Response
    {
        $client = $request->user()->clientProfile;

        return Inertia::render('Client/Tickets/Index', ['tickets' => $client->tickets()->with('assignedTo:id,name')->latest()->paginate(10)]);
    }

    public function storeTicket(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'priority' => ['required', 'in:low,medium,high'],
        ]);
        $data['client_id'] = $request->user()->clientProfile->id;
        Ticket::create($data);

        return back()->with('success', 'Cererea a fost trimisa.');
    }

    public function works(Request $request): Response
    {
        $client = $request->user()->clientProfile;

        return Inertia::render('Client/Works/Index', ['works' => $client->installations()->with('technician:id,name')->latest('scheduled_at')->paginate(10)]);
    }

    public function invoices(Request $request): Response
    {
        return Inertia::render('Client/Invoices/Index', ['invoices' => $request->user()->clientProfile->invoices()->latest('issued_at')->paginate(10)]);
    }

    public function invoicePdf(Request $request, int $invoice): SymfonyResponse
    {
        $record = $request->user()->clientProfile->invoices()->findOrFail($invoice);
        $record->load('client');

        return Pdf::loadView('pdfs.invoice', [
            'invoice' => $record,
            'settings' => Setting::allSettings(),
        ])->stream("factura-{$record->invoice_number}.pdf");
    }

    public function subscriptions(Request $request): Response
    {
        return Inertia::render('Client/Subscriptions/Index', ['subscriptions' => $request->user()->clientProfile->subscriptions()->latest()->get()]);
    }

    public function equipment(Request $request): Response
    {
        return Inertia::render('Client/Equipment/Index', ['equipment' => Equipment::where('client_id', $request->user()->clientProfile->id)->latest()->get()]);
    }

    public function notifications(Request $request): Response
    {
        return Inertia::render('Client/Notifications/Index', ['notifications' => $request->user()->notifications()->latest()->paginate(20)]);
    }

    public function readNotification(Request $request, string $notification): RedirectResponse
    {
        $request->user()->notifications()->whereKey($notification)->update(['read_at' => now()]);

        return back();
    }
}
