<?php

namespace App\Http\Controllers\Technical;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Installation;
use App\Models\Invoice;
use App\Models\Offer;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Inertia\Inertia;
use Inertia\Response;

class InstallationController extends Controller
{
    public function index(Request $request): Response
    {
        $installations = Installation::query()
            ->with(['client:id,name', 'technician:id,name'])
            ->when($request->string('status')->toString(), fn ($query, $status) => $query->where('status', $status))
            ->when($request->string('type')->toString(), fn ($query, $type) => $query->where('type', $type))
            ->when($request->integer('technician_id'), fn ($query, $id) => $query->where('technician_id', $id))
            ->orderBy('scheduled_at')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Technical/Installations/Index', [
            'installations' => $installations,
            'filters' => $request->only('status', 'type', 'technician_id'),
            'technicians' => User::role('tehnic')->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function create(Request $request): Response
    {
        return Inertia::render('Technical/Installations/Create', [
            'clients' => Client::orderBy('name')->get(['id', 'name', 'address', 'city']),
            'offers' => Offer::with('client:id,name')->where('status', 'accepted')->get(['id', 'client_id', 'title']),
            'technicians' => User::role('tehnic')->orderBy('name')->get(['id', 'name']),
            'preselectedClientId' => $request->integer('client_id') ?: null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);
        $data['checklist'] = Installation::defaultChecklist();

        $installation = Installation::create($data);

        return redirect()->route('technical.installations.show', $installation)->with('success', 'Programare creata.');
    }

    public function show(Installation $installation): Response
    {
        $installation->load(['client', 'offer', 'technician:id,name', 'tickets']);

        return Inertia::render('Technical/Installations/Show', [
            'installation' => $installation,
        ]);
    }

    public function edit(Installation $installation): Response
    {
        return Inertia::render('Technical/Installations/Edit', [
            'installation' => $installation,
            'clients' => Client::orderBy('name')->get(['id', 'name', 'address', 'city']),
            'offers' => Offer::with('client:id,name')->where('status', 'accepted')->get(['id', 'client_id', 'title']),
            'technicians' => User::role('tehnic')->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, Installation $installation): RedirectResponse
    {
        $installation->update($this->validateData($request));

        return redirect()->route('technical.installations.show', $installation)->with('success', 'Programare actualizata.');
    }

    public function updateStatus(Request $request, Installation $installation): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:scheduled,in_progress,completed,cancelled'],
        ]);

        $installation->update($data);

        if ($data['status'] === 'completed') {
            $this->createInvoiceFromCompletedInstallation($installation);
        }

        return back()->with('success', 'Status actualizat.');
    }

    private function createInvoiceFromCompletedInstallation(Installation $installation): void
    {
        if (! $installation->offer_id) {
            return;
        }

        if (Invoice::where('offer_id', $installation->offer_id)->exists()) {
            return;
        }

        Invoice::create([
            'client_id' => $installation->client_id,
            'offer_id' => $installation->offer_id,
            'invoice_number' => Invoice::nextInvoiceNumber(),
            'amount' => $installation->offer->total_amount ?? 0,
            'status' => 'unpaid',
            'issued_at' => now(),
            'due_at' => now()->addDays(14),
        ]);
    }

    public function updateChecklist(Request $request, Installation $installation): RedirectResponse
    {
        $data = $request->validate([
            'index' => ['required', 'integer', 'min:0'],
            'done' => ['required', 'boolean'],
        ]);

        $checklist = $installation->checklist ?? [];

        abort_unless(array_key_exists($data['index'], $checklist), 422, 'Element checklist invalid.');

        $checklist[$data['index']]['done'] = $data['done'];

        $installation->update(['checklist' => $checklist]);

        return back()->with('success', 'Checklist actualizat.');
    }

    public function destroy(Installation $installation): RedirectResponse
    {
        $installation->delete();

        return redirect()->route('technical.installations.index')->with('success', 'Programare stearsa.');
    }

    public function pdf(Installation $installation): HttpResponse
    {
        $installation->load(['client', 'technician:id,name']);

        return Pdf::loadView('pdfs.installation-report', ['installation' => $installation])
            ->stream("raport-instalare-{$installation->id}.pdf");
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'client_id' => ['required', 'exists:clients,id'],
            'offer_id' => ['nullable', 'exists:offers,id'],
            'technician_id' => ['nullable', 'exists:users,id'],
            'type' => ['required', 'in:instalare,interventie'],
            'address' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'scheduled_at' => ['nullable', 'date'],
            'status' => ['required', 'in:scheduled,in_progress,completed,cancelled'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);
    }
}
