<?php

namespace App\Http\Controllers\Technical;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EquipmentController extends Controller
{
    public function index(Request $request): Response
    {
        $equipment = Equipment::query()
            ->when($request->string('search')->toString(), function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")->orWhere('sku', 'like', "%{$search}%");
                });
            })
            ->when($request->string('category')->toString(), fn ($query, $category) => $query->where('category', $category))
            ->when($request->boolean('low_stock'), fn ($query) => $query->where('stock_quantity', '<', 5))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Technical/Equipment/Index', [
            'equipment' => $equipment,
            'filters' => $request->only('search', 'category', 'low_stock'),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Technical/Equipment/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        Equipment::create($this->validateData($request));

        return redirect()->route('technical.equipment.index')->with('success', 'Echipament adaugat.');
    }

    public function edit(Equipment $equipment): Response
    {
        return Inertia::render('Technical/Equipment/Edit', ['equipment' => $equipment]);
    }

    public function update(Request $request, Equipment $equipment): RedirectResponse
    {
        $equipment->update($this->validateData($request));

        return redirect()->route('technical.equipment.index')->with('success', 'Echipament actualizat.');
    }

    public function adjustStock(Request $request, Equipment $equipment): RedirectResponse
    {
        $data = $request->validate([
            'delta' => ['required', 'integer'],
        ]);

        $equipment->update([
            'stock_quantity' => max(0, $equipment->stock_quantity + $data['delta']),
        ]);

        return back()->with('success', 'Stoc actualizat.');
    }

    public function destroy(Equipment $equipment): RedirectResponse
    {
        $equipment->delete();

        return redirect()->route('technical.equipment.index')->with('success', 'Echipament sters.');
    }

    private function validateData(Request $request): array
    {
        $request->merge([
            'cost_price' => $request->input('cost_price', 0),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'in:camera,dvr,nvr,cable,accessory,other'],
            'sku' => ['nullable', 'string', 'max:100'],
            'unit' => ['required', 'string', 'max:50'],
            'unit_price' => ['required', 'numeric', 'min:0'],
            'cost_price' => ['required', 'numeric', 'min:0'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['required', 'boolean'],
        ]);
    }
}
