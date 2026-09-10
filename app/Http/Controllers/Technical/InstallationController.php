<?php

namespace App\Http\Controllers\Technical;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Equipment;
use App\Models\Installation;
use App\Models\Invoice;
use App\Models\Offer;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
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
            'equipment' => Equipment::where('is_active', true)->where('stock_quantity', '>', 0)->orderBy('name')->get(['id', 'name', 'sku', 'unit', 'stock_quantity']),
        ]);
    }

    public function create(Request $request): Response
    {
        return Inertia::render('Technical/Installations/Create', [
            'clients' => Client::orderBy('name')->get(['id', 'name', 'address', 'city']),
            'offers' => Offer::with('client:id,name')->where('status', 'accepted')->get(['id', 'client_id', 'title']),
            'technicians' => User::role('tehnic')->orderBy('name')->get(['id', 'name']),
            'equipment' => Equipment::where('is_active', true)->where('stock_quantity', '>', 0)->orderBy('name')->get(['id', 'name', 'sku', 'unit', 'stock_quantity']),
            'preselectedClientId' => $request->integer('client_id') ?: null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);
        $data['checklist'] = Installation::defaultChecklist();
        $data = $this->processExecutionDetails($request, $data);

        $installation = DB::transaction(function () use ($data) {
            $installation = Installation::create($data);
            if ($installation->status === 'completed') {
                $this->markCompleted($installation);
                $this->consumeMaterialsFromStock($installation);
            }
            return $installation;
        });

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
            'equipment' => Equipment::orderBy('name')->get(['id', 'name', 'sku', 'unit', 'stock_quantity']),
        ]);
    }

    public function update(Request $request, Installation $installation): RedirectResponse
    {
        $data = $this->processExecutionDetails($request, $this->validateData($request), $installation);
        DB::transaction(function () use ($data, $installation) {
            $installation->update($data);
            if ($installation->status === 'completed') {
                $this->markCompleted($installation);
                $this->consumeMaterialsFromStock($installation);
            }
        });

        return redirect()->route('technical.installations.show', $installation)->with('success', 'Programare actualizata.');
    }

    public function updateStatus(Request $request, Installation $installation): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:scheduled,in_progress,completed,cancelled'],
        ]);

        DB::transaction(function () use ($data, $installation) {
            $installation->update($data);
            if ($data['status'] === 'completed') {
                $this->markCompleted($installation);
                $this->consumeMaterialsFromStock($installation);
                $this->createInvoiceFromCompletedInstallation($installation);
            }
        });

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
            'labor_hours' => ['nullable', 'numeric', 'min:0', 'max:999.99'],
            'status' => ['required', 'in:scheduled,in_progress,completed,cancelled'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'materials' => ['nullable', 'string', 'max:5000'],
            'material_items' => ['nullable', 'array'],
            'material_items.*.equipment_id' => ['required', 'integer', 'exists:equipment,id'],
            'material_items.*.quantity' => ['required', 'integer', 'min:1', 'max:9999'],
            'customer_name' => ['nullable', 'string', 'max:255'],
            'customer_notes' => ['nullable', 'string', 'max:2000'],
            'photos.*' => ['nullable', 'image', 'max:5120'],
            'handover_at' => ['nullable', 'date'],
            'technician_signature' => ['nullable', 'image', 'max:5120'],
            'customer_signature' => ['nullable', 'image', 'max:5120'],
        ]);
    }

    private function processExecutionDetails(Request $request, array $data, ?Installation $installation = null): array
    {
        $data['materials'] = array_values(array_filter(array_map(
            'trim',
            preg_split('/\r\n|\r|\n/', $data['materials'] ?? '')
        )));
        $data['material_items'] = collect($data['material_items'] ?? [])
            ->map(function (array $item): array {
                $equipment = Equipment::findOrFail($item['equipment_id']);

                return [
                    'equipment_id' => $equipment->id,
                    'name' => $equipment->name,
                    'unit' => $equipment->unit,
                    'quantity' => (int) $item['quantity'],
                ];
            })
            ->values()
            ->all();
        unset($data['photos']);

        $photos = [];
        foreach ($request->file('photos', []) as $photo) {
            $photos[] = Storage::disk('public')->url($photo->store('installations', 'public'));
        }

        if ($photos) {
            $data['photos'] = array_values(array_merge($installation?->photos ?? [], $photos));
        }

        foreach (['technician_signature', 'customer_signature'] as $signatureField) {
            if ($request->hasFile($signatureField)) {
                $data[$signatureField] = Storage::disk('public')->url(
                    $request->file($signatureField)->store('installations/signatures', 'public')
                );
            } elseif ($installation) {
                $data[$signatureField] = $installation->{$signatureField};
            }
        }

        return $data;
    }

    private function markCompleted(Installation $installation): void
    {
        $installation->update([
            'completed_at' => $installation->completed_at ?? now(),
            'handover_at' => $installation->handover_at ?? now(),
            'report_number' => $installation->report_number ?? 'PV-'.now()->format('Y').'-'.str_pad((string) $installation->id, 5, '0', STR_PAD_LEFT),
        ]);
    }

    private function consumeMaterialsFromStock(Installation $installation): void
    {
        if ($installation->stock_consumed_at || empty($installation->material_items)) {
            return;
        }

        $quantities = collect($installation->material_items)
            ->groupBy('equipment_id')
            ->map(fn ($items) => $items->sum('quantity'));

        foreach ($quantities as $equipmentId => $quantity) {
            $equipment = Equipment::query()->lockForUpdate()->findOrFail($equipmentId);
            if ($equipment->stock_quantity < $quantity) {
                throw ValidationException::withMessages([
                    'material_items' => "Stoc insuficient pentru {$equipment->name}. Disponibil: {$equipment->stock_quantity}.",
                ]);
            }
        }

        foreach ($quantities as $equipmentId => $quantity) {
            Equipment::query()->whereKey($equipmentId)->decrement('stock_quantity', $quantity);
        }

        $installation->update(['stock_consumed_at' => now()]);
    }
}
