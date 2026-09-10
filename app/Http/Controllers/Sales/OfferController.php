<?php

namespace App\Http\Controllers\Sales;

use App\Exports\OffersExport;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Equipment;
use App\Models\Installation;
use App\Models\Offer;
use App\Models\Service;
use App\Models\User;
use App\Notifications\OfferSent;
use App\Notifications\OfferAvailable;
use App\Services\SmsService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;

class OfferController extends Controller
{
    public function export(Request $request)
    {
        return Excel::download(
            new OffersExport($request->only('search', 'status')),
            'oferte-'.now()->format('Y-m-d').'.xlsx'
        );
    }

    public function index(Request $request): Response
    {
        $offers = Offer::query()
            ->with('client:id,name,company_name')
            ->when($request->string('search')->toString(), function ($query, $search) {
                $query->whereHas('client', fn ($q) => $q->where('name', 'like', "%{$search}%"));
            })
            ->when($request->string('status')->toString(), fn ($query, $status) => $query->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $pipeline = Offer::query()
            ->selectRaw('status, count(*) as total, sum(total_amount) as amount')
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        return Inertia::render('Sales/Offers/Index', [
            'offers' => $offers,
            'filters' => $request->only('search', 'status'),
            'pipeline' => $pipeline,
        ]);
    }

    public function create(Request $request): Response
    {
        return Inertia::render('Sales/Offers/Create', [
            'clients' => Client::orderBy('name')->get(['id', 'name', 'company_name']),
            'equipment' => Equipment::orderBy('name')->get(['id', 'name', 'unit_price', 'unit']),
            'services' => Service::where('is_active', true)->orderBy('name')->get(['id', 'name', 'sale_price', 'unit']),
            'preselectedClientId' => $request->integer('client_id') ?: null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);

        $offer = DB::transaction(function () use ($data, $request) {
            $offer = Offer::create([
                'client_id' => $data['client_id'],
                'user_id' => $request->user()->id,
                'title' => $data['title'],
                'status' => 'draft',
                'valid_until' => $data['valid_until'] ?? null,
                'notes' => $data['notes'] ?? null,
                'total_amount' => collect($data['items'])->sum(fn ($item) => $item['quantity'] * $item['unit_price']),
            ]);

            $offer->items()->createMany($data['items']);

            return $offer;
        });

        if ($offer->status === 'sent') {
            $this->notifySentOffer($offer, $request->user());
        }

        return redirect()->route('sales.offers.show', $offer)->with('success', 'Oferta creata cu succes.');
    }

    public function show(Offer $offer): Response
    {
        $offer->load(['client', 'user:id,name', 'items.equipment:id,name']);

        return Inertia::render('Sales/Offers/Show', [
            'offer' => $offer,
        ]);
    }

    public function edit(Offer $offer): Response
    {
        $offer->load('items');

        return Inertia::render('Sales/Offers/Edit', [
            'offer' => $offer,
            'clients' => Client::orderBy('name')->get(['id', 'name', 'company_name']),
            'equipment' => Equipment::orderBy('name')->get(['id', 'name', 'unit_price', 'unit']),
            'services' => Service::where('is_active', true)->orderBy('name')->get(['id', 'name', 'sale_price', 'unit']),
        ]);
    }

    public function update(Request $request, Offer $offer): RedirectResponse
    {
        $data = $this->validateData($request);

        DB::transaction(function () use ($data, $offer) {
            $offer->update([
                'client_id' => $data['client_id'],
                'title' => $data['title'],
                'status' => in_array($offer->status, ['sent', 'accepted', 'rejected'], true) ? 'draft' : $data['status'],
                'valid_until' => $data['valid_until'] ?? null,
                'notes' => $data['notes'] ?? null,
                'total_amount' => collect($data['items'])->sum(fn ($item) => $item['quantity'] * $item['unit_price']),
            ]);

            $offer->items()->delete();
            $offer->items()->createMany($data['items']);
        });

        return redirect()->route('sales.offers.show', $offer)->with('success', 'Oferta actualizata cu succes.');
    }

    public function updateStatus(Request $request, Offer $offer, SmsService $sms): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:draft,sent,accepted,rejected,expired'],
        ]);

        $offer->update($data);

        if ($data['status'] === 'accepted') {
            $offer->client->update(['status' => 'client']);
            $offer->load(['items.equipment', 'items.service']);
            $this->createInstallationFromAcceptedOffer($offer);
        }

        if ($data['status'] === 'sent') {
            $this->notifySentOffer($offer, $request->user());
            if ($offer->client->phone) {
                $sms->send($offer->client->phone, "Ti-am trimis oferta \"{$offer->title}\". Te asteptam cu intrebari!");
            }
        }

        return back()->with('success', 'Status oferta actualizat.');
    }

    private function notifySentOffer(Offer $offer, User $sender): void
    {
        $offer->loadMissing('client.user');

        if ($offer->client->user) {
            $offer->client->user->notify(new OfferSent($offer));
        } elseif ($offer->client->email && config('notifications.mail_enabled')) {
            Notification::route('mail', $offer->client->email)->notify(new OfferSent($offer));
        }

        Notification::send(
            User::role(['admin', 'vanzari'])->where('users.id', '!=', $sender->id)->get(),
            new OfferAvailable($offer),
        );
    }

    private function createInstallationFromAcceptedOffer(Offer $offer): void
    {
        if (Installation::where('offer_id', $offer->id)->exists()) {
            return;
        }

        Installation::create([
            'client_id' => $offer->client_id,
            'offer_id' => $offer->id,
            'type' => 'instalare',
            'address' => trim(($offer->client->address ?? '').' '.($offer->client->city ?? '')),
            'status' => 'scheduled',
            'checklist' => Installation::defaultChecklist(),
            'material_items' => $offer->items->whereNotNull('equipment_id')->map(fn ($item) => [
                'equipment_id' => $item->equipment_id,
                'name' => $item->equipment?->name ?? $item->description,
                'unit' => $item->equipment?->unit ?? 'buc',
                'quantity' => (int) $item->quantity,
            ])->values()->all(),
            'service_items' => $offer->items->whereNotNull('service_id')->map(fn ($item) => [
                'service_id' => $item->service_id,
                'name' => $item->service?->name ?? $item->description,
                'unit' => $item->service?->unit ?? 'serviciu',
                'quantity' => (int) $item->quantity,
            ])->values()->all(),
            'notes' => "Generata automat la acceptarea ofertei #{$offer->id}.",
        ]);
    }

    public function destroy(Offer $offer): RedirectResponse
    {
        $offer->delete();

        return redirect()->route('sales.offers.index')->with('success', 'Oferta stearsa.');
    }

    public function pdf(Offer $offer): HttpResponse
    {
        $offer->load(['client', 'items.equipment:id,name', 'items.service:id,name']);

        return Pdf::loadView('pdfs.offer', ['offer' => $offer])->stream("oferta-{$offer->id}.pdf");
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'client_id' => ['required', 'exists:clients,id'],
            'title' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:draft,sent,accepted,rejected,expired'],
            'valid_until' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.equipment_id' => ['nullable', 'exists:equipment,id'],
            'items.*.service_id' => ['nullable', 'exists:services,id'],
            'items.*.description' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ]);
    }
}
