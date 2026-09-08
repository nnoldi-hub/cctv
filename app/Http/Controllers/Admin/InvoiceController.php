<?php

namespace App\Http\Controllers\Admin;

use App\Exports\InvoicesExport;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Offer;
use App\Models\Setting;
use App\Services\FgoClient;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use RuntimeException;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;

class InvoiceController extends Controller
{
    public function export(Request $request)
    {
        return Excel::download(
            new InvoicesExport($request->only('search', 'status')),
            'facturi-'.now()->format('Y-m-d').'.xlsx'
        );
    }

    public function index(Request $request): Response
    {
        $invoices = Invoice::query()
            ->with('client:id,name')
            ->when($request->string('search')->toString(), function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('invoice_number', 'like', "%{$search}%")
                        ->orWhereHas('client', fn ($c) => $c->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($request->string('status')->toString(), fn ($query, $status) => $query->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Invoices/Index', [
            'invoices' => $invoices,
            'filters' => $request->only('search', 'status'),
            'summary' => [
                'unpaid' => (float) Invoice::where('status', 'unpaid')->sum('amount'),
                'paid' => (float) Invoice::where('status', 'paid')->sum('amount'),
                'overdue' => Invoice::where('status', 'unpaid')->where('due_at', '<', now())->count(),
            ],
        ]);
    }

    public function create(Request $request): Response
    {
        return Inertia::render('Admin/Invoices/Create', [
            'clients' => Client::orderBy('name')->get(['id', 'name']),
            'offers' => Offer::with('client:id,name')->where('status', 'accepted')->get(['id', 'client_id', 'title', 'total_amount']),
            'nextInvoiceNumber' => Invoice::nextInvoiceNumber(),
            'preselectedClientId' => $request->integer('client_id') ?: null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);
        $data['invoice_number'] = $data['invoice_number'] ?? '' ?: Invoice::nextInvoiceNumber();
        $data['offer_id'] = $data['offer_id'] ?? null;
        $data['issued_at'] = $data['issued_at'] ?? null;
        $data['due_at'] = $data['due_at'] ?? null;

        $invoice = Invoice::create($data);

        return redirect()->route('admin.invoices.show', $invoice)->with('success', 'Factura creata.');
    }

    public function show(Invoice $invoice): Response
    {
        $invoice->load(['client', 'offer']);

        return Inertia::render('Admin/Invoices/Show', [
            'invoice' => $invoice,
        ]);
    }

    public function edit(Invoice $invoice): Response
    {
        return Inertia::render('Admin/Invoices/Edit', [
            'invoice' => $invoice,
            'clients' => Client::orderBy('name')->get(['id', 'name']),
            'offers' => Offer::with('client:id,name')->where('status', 'accepted')->get(['id', 'client_id', 'title', 'total_amount']),
        ]);
    }

    public function update(Request $request, Invoice $invoice): RedirectResponse
    {
        $invoice->update($this->validateData($request));

        return redirect()->route('admin.invoices.show', $invoice)->with('success', 'Factura actualizata.');
    }

    public function markPaid(Invoice $invoice): RedirectResponse
    {
        $invoice->update(['status' => 'paid', 'paid_at' => now()]);

        return back()->with('success', 'Factura marcata ca platita.');
    }

    public function syncFgo(Invoice $invoice, FgoClient $fgo): RedirectResponse
    {
        if (! $fgo->isConfigured()) {
            return back()->with('error', $fgo->configurationMessage());
        }

        try {
            $fgo->syncInvoice($invoice);
        } catch (RuntimeException $exception) {
            $invoice->update([
                'fgo_status' => 'error',
                'fgo_error' => $exception->getMessage(),
            ]);

            return back()->with('error', $exception->getMessage());
        }

        return back()->with('success', 'Factura sincronizata cu FGO.');
    }

    public function destroy(Invoice $invoice): RedirectResponse
    {
        $invoice->delete();

        return redirect()->route('admin.invoices.index')->with('success', 'Factura stearsa.');
    }

    public function pdf(Invoice $invoice): HttpResponse
    {
        $invoice->load('client');

        return Pdf::loadView('pdfs.invoice', [
            'invoice' => $invoice,
            'settings' => Setting::allSettings(),
        ])->stream("factura-{$invoice->invoice_number}.pdf");
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'client_id' => ['required', 'exists:clients,id'],
            'offer_id' => ['nullable', 'exists:offers,id'],
            'invoice_number' => ['nullable', 'string', 'max:100'],
            'amount' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:unpaid,paid,overdue,cancelled'],
            'issued_at' => ['nullable', 'date'],
            'due_at' => ['nullable', 'date'],
        ]);
    }
}
