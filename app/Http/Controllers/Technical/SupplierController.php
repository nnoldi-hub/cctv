<?php

namespace App\Http\Controllers\Technical;

use App\Http\Controllers\Controller;
use App\Imports\EquipmentImport;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;

class SupplierController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Technical/Suppliers/Index', [
            'suppliers' => Supplier::withCount('equipment')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Supplier::create($request->validate([
            'name' => ['required', 'string', 'max:255'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'tax_id' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]));

        return back()->with('success', 'Furnizor adaugat.');
    }

    public function import(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'file' => ['required', 'file', 'mimes:csv,txt,xlsx,xls', 'max:10240'],
            'markup_percent' => ['required', 'numeric', 'min:0', 'max:1000'],
            'name_column' => ['required', 'string', 'max:100'],
            'cost_column' => ['required', 'string', 'max:100'],
            'sku_column' => ['nullable', 'string', 'max:100'],
            'category_column' => ['nullable', 'string', 'max:100'],
            'unit_column' => ['nullable', 'string', 'max:100'],
            'stock_column' => ['nullable', 'string', 'max:100'],
            'description_column' => ['nullable', 'string', 'max:100'],
        ]);

        Excel::import(new EquipmentImport(
            $data['supplier_id'],
            (float) $data['markup_percent'],
            [
                'name' => $data['name_column'],
                'cost_price' => $data['cost_column'],
                'sku' => $data['sku_column'] ?? '',
                'category' => $data['category_column'] ?? '',
                'unit' => $data['unit_column'] ?? '',
                'stock' => $data['stock_column'] ?? '',
                'description' => $data['description_column'] ?? '',
            ],
        ), $data['file']);

        return back()->with('success', 'Materialele au fost importate si preturile au fost calculate.');
    }
}
