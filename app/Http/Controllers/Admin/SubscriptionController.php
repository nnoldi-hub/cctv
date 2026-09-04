<?php

namespace App\Http\Controllers\Admin;

use App\Exports\SubscriptionsExport;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Subscription;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;

class SubscriptionController extends Controller
{
    public function export(Request $request)
    {
        return Excel::download(
            new SubscriptionsExport($request->only('status')),
            'abonamente-'.now()->format('Y-m-d').'.xlsx'
        );
    }

    public function index(Request $request): Response
    {
        $subscriptions = Subscription::query()
            ->with('client:id,name')
            ->when($request->string('status')->toString(), fn ($query, $status) => $query->where('status', $status))
            ->orderBy('next_billing_at')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Subscriptions/Index', [
            'subscriptions' => $subscriptions,
            'filters' => $request->only('status'),
            'monthlyRecurringRevenue' => (float) Subscription::where('status', 'active')
                ->get()
                ->sum(fn ($s) => $s->billing_cycle === 'yearly' ? $s->price / 12 : $s->price),
        ]);
    }

    public function create(Request $request): Response
    {
        return Inertia::render('Admin/Subscriptions/Create', [
            'clients' => Client::orderBy('name')->get(['id', 'name']),
            'preselectedClientId' => $request->integer('client_id') ?: null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Subscription::create($this->validateData($request));

        return redirect()->route('admin.subscriptions.index')->with('success', 'Abonament creat.');
    }

    public function edit(Subscription $subscription): Response
    {
        return Inertia::render('Admin/Subscriptions/Edit', [
            'subscription' => $subscription,
            'clients' => Client::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, Subscription $subscription): RedirectResponse
    {
        $subscription->update($this->validateData($request));

        return redirect()->route('admin.subscriptions.index')->with('success', 'Abonament actualizat.');
    }

    public function destroy(Subscription $subscription): RedirectResponse
    {
        $subscription->delete();

        return redirect()->route('admin.subscriptions.index')->with('success', 'Abonament sters.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'client_id' => ['required', 'exists:clients,id'],
            'plan' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'billing_cycle' => ['required', 'in:monthly,yearly'],
            'status' => ['required', 'in:active,paused,cancelled'],
            'started_at' => ['required', 'date'],
            'next_billing_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);
    }
}
