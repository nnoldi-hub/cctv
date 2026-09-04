<?php

namespace App\Http\Controllers\Sales;

use App\Exports\ClientsExport;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
        $clients = Client::query()
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
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Sales/Clients/Index', [
            'clients' => $clients,
            'filters' => $request->only('search', 'status', 'source'),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Sales/Clients/Create', [
            'users' => User::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);

        $client = Client::create($data);

        return redirect()->route('sales.clients.show', $client)->with('success', 'Client adaugat cu succes.');
    }

    public function show(Client $client): Response
    {
        $client->load([
            'assignedTo:id,name',
            'offers' => fn ($query) => $query->latest(),
            'installations' => fn ($query) => $query->latest('scheduled_at'),
            'invoices' => fn ($query) => $query->latest(),
        ]);

        return Inertia::render('Sales/Clients/Show', [
            'client' => $client,
        ]);
    }

    public function edit(Client $client): Response
    {
        return Inertia::render('Sales/Clients/Edit', [
            'client' => $client,
            'users' => User::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, Client $client): RedirectResponse
    {
        $data = $this->validateData($request, $client);

        $client->update($data);

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
            'assigned_to' => ['nullable', 'exists:users,id'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);
    }
}
