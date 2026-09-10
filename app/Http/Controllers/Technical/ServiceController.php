<?php

namespace App\Http\Controllers\Technical;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ServiceController extends Controller
{
    public function index(Request $request): Response
    {
        $services = Service::query()
            ->when($request->string('search')->toString(), fn ($query, $search) => $query->where('name', 'like', "%{$search}%"))
            ->when($request->filled('active'), fn ($query) => $query->where('is_active', $request->boolean('active')))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Technical/Services/Index', [
            'services' => $services,
            'filters' => $request->only('search', 'active'),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Technical/Services/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        Service::create($this->validateData($request));

        return redirect()->route('technical.services.index')->with('success', 'Serviciu adaugat.');
    }

    public function edit(Service $service): Response
    {
        return Inertia::render('Technical/Services/Edit', ['service' => $service]);
    }

    public function update(Request $request, Service $service): RedirectResponse
    {
        $service->update($this->validateData($request));

        return redirect()->route('technical.services.index')->with('success', 'Serviciu actualizat.');
    }

    public function destroy(Service $service): RedirectResponse
    {
        $service->update(['is_active' => false]);

        return redirect()->route('technical.services.index')->with('success', 'Serviciu dezactivat.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'unit' => ['required', 'string', 'max:50'],
            'cost_price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['required', 'boolean'],
        ]);
    }
}
