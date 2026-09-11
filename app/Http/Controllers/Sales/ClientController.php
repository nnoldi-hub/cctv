<?php

namespace App\Http\Controllers\Sales;

use App\Exports\ClientsExport;
use App\Exports\ClientStatementExport;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Setting;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;

class ClientController extends Controller
{
    public function export(Request $request)
    {
        return Excel::download(
            new ClientsExport($request->only('search', 'status', 'source')),
            'clienti-'.now()->format('Y-m-d').'.xlsx'
        );
    }

    public function index(Request $request): Response
    {
        $query = Client::query()
            ->with('assignedTo:id,name')
            ->when($request->string('search')->toString(), function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('company_name', 'like', "%{$search}%");
                });
            })
            ->when($request->string('status')->toString(), fn ($query, $status) => $query->where('status', $status))
            ->when($request->string('source')->toString(), fn ($query, $source) => $query->where('source', $source))
            ->latest();

        $clients = (clone $query)->paginate(15)->withQueryString();
        $pipeline = $query->get();

        return Inertia::render('Sales/Clients/Index', [
            'clients' => $clients,
            'filters' => $request->only('search', 'status', 'source'),
            'pipeline' => $pipeline,
        ]);
    }

    public function updatePipeline(Request $request, Client $client): RedirectResponse
    {
        $data = $request->validate([
            'pipeline_stage' => ['required', 'in:new,contacted,visit,proposal,won,lost'],
            'lost_reason' => ['nullable', 'string', 'max:255'],
        ]);

        if ($data['pipeline_stage'] !== 'lost') {
            $data['lost_reason'] = null;
        }

        if ($data['pipeline_stage'] === 'won') {
            $data['status'] = 'client';
        } elseif ($data['pipeline_stage'] === 'lost') {
            $data['status'] = 'inactive';
        }

        $client->update($data);

        return back()->with('success', 'Etapa pipeline actualizata.');
    }

    public function create(): Response
    {
        return Inertia::render('Sales/Clients/Create', [
            'users' => User::orderBy('name')->get(['id', 'name']),
            'portalUsers' => auth()->user()->hasRole('admin')
                ? User::role(['client', 'client-manager'])->orderBy('name')->get(['id', 'name', 'email'])
                : [],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);
        $portalData = $this->portalData($request);

        $client = Client::create($data);
        $this->syncPortalAccount($client, $portalData);

        return redirect()->route('sales.clients.show', $client)->with('success', 'Client adaugat cu succes.');
    }

    public function show(Client $client): Response
    {
        $client->load([
            'assignedTo:id,name',
            'user:id,name,email',
            'user.roles:id,name',
            'offers' => fn ($query) => $query->latest(),
            'installations' => fn ($query) => $query->latest('scheduled_at'),
            'invoices' => fn ($query) => $query->with('payments')->latest(),
            'subscriptions' => fn ($query) => $query->latest('started_at'),
            'tickets' => fn ($query) => $query->with('assignedTo:id,name')->latest(),
            'activities' => fn ($query) => $query->with('assignedTo:id,name')->latest('due_at'),
        ]);

        return Inertia::render('Sales/Clients/Show', [
            'client' => $client,
            'summary' => array_merge($this->financialSummary($client), [
                'offers' => $client->offers->count(),
                'installations' => $client->installations->count(),
                'invoices' => $client->invoices->count(),
                'openTickets' => $client->tickets->whereNotIn('status', ['resolved', 'closed'])->count(),
                'activeSubscriptions' => $client->subscriptions->where('status', 'active')->count(),
                'pendingActivities' => $client->activities->where('status', 'pending')->count(),
            ]),
        ]);
    }

    public function statementPdf(Client $client)
    {
        $invoices = $client->invoices()->with('payments')->latest()->get();
        $payments = $invoices->flatMap->payments->sortByDesc('paid_at')->values();

        return Pdf::loadView('pdfs.client-statement', [
            'client' => $client,
            'invoices' => $invoices,
            'payments' => $payments,
            'summary' => $this->financialSummary($client, $invoices),
            'settings' => Setting::allSettings(),
        ])->stream('situatie-financiara-'.str($client->name)->slug().'.pdf');
    }

    public function statementExcel(Client $client)
    {
        return Excel::download(
            new ClientStatementExport($client),
            'situatie-financiara-'.str($client->name)->slug().'.xlsx'
        );
    }

    private function financialSummary(Client $client, $invoices = null): array
    {
        $invoices ??= $client->invoices;

        return [
            'invoiceTotal' => (float) $invoices->sum('amount'),
            'invoicePaid' => (float) $invoices->sum('paid_amount'),
            'invoiceBalance' => (float) $invoices->whereIn('status', ['unpaid', 'overdue'])->sum(fn ($invoice) => max((float) $invoice->amount - (float) $invoice->paid_amount, 0)),
            'overdueInvoices' => $invoices->where('status', 'overdue')->count(),
        ];
    }

    public function edit(Client $client): Response
    {
        $client->load('user.roles:id,name');

        return Inertia::render('Sales/Clients/Edit', [
            'client' => $client,
            'users' => User::orderBy('name')->get(['id', 'name']),
            'portalUsers' => auth()->user()->hasRole('admin')
                ? User::role(['client', 'client-manager'])->orderBy('name')->get(['id', 'name', 'email'])
                : [],
        ]);
    }

    public function update(Request $request, Client $client): RedirectResponse
    {
        $data = $this->validateData($request, $client);
        $portalData = $this->portalData($request, $client);

        $client->update($data);
        $this->syncPortalAccount($client, $portalData);

        return redirect()->route('sales.clients.show', $client)->with('success', 'Client actualizat cu succes.');
    }

    public function destroy(Client $client): RedirectResponse
    {
        $client->delete();

        return redirect()->route('sales.clients.index')->with('success', 'Client sters.');
    }

    private function validateData(Request $request, ?Client $client = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'county' => ['nullable', 'string', 'max:255'],
            'source' => ['required', 'in:web,phone,referral,manual'],
            'status' => ['required', 'in:lead,client,inactive'],
            'pipeline_stage' => ['required', 'in:new,contacted,visit,proposal,won,lost'],
            'lost_reason' => ['nullable', 'string', 'max:255'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);
    }

    private function portalData(Request $request, ?Client $client = null): array
    {
        if (! $request->hasAny(['portal_user_id', 'portal_role'])) {
            return [];
        }

        abort_unless($request->user()->hasRole('admin'), 403);

        return $request->validate([
            'portal_user_id' => ['nullable', 'exists:users,id', Rule::unique('clients', 'user_id')->ignore($client?->id)],
            'portal_role' => ['required_with:portal_user_id', 'nullable', 'in:client,client-manager'],
        ]);
    }

    private function syncPortalAccount(Client $client, array $data): void
    {
        if (! array_key_exists('portal_user_id', $data)) {
            return;
        }

        $client->update(['user_id' => $data['portal_user_id'] ?: null]);

        if (! empty($data['portal_user_id'])) {
            $user = User::findOrFail($data['portal_user_id']);
            $user->removeRole('client');
            $user->removeRole('client-manager');
            $user->assignRole($data['portal_role']);
        }
    }
}
