<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Installation;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ExpenseController extends Controller
{
    public function index(Request $request): Response
    {
        $expenses = Expense::query()
            ->with(['supplier:id,name', 'installation:id,report_number,client_id'])
            ->when($request->string('category')->toString(), fn ($query, $category) => $query->where('category', $category))
            ->latest('expense_date')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/Expenses/Index', [
            'expenses' => $expenses,
            'filters' => $request->only('category'),
            'summary' => [
                'total' => (float) Expense::sum('amount'),
                'this_month' => (float) Expense::whereBetween('expense_date', [now()->startOfMonth(), now()->endOfMonth()])->sum('amount'),
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Expenses/Create', [
            'suppliers' => Supplier::orderBy('name')->get(['id', 'name']),
            'installations' => Installation::with('client:id,name')->latest()->get(['id', 'client_id', 'report_number']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Expense::create($request->validate([
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'installation_id' => ['nullable', 'exists:installations,id'],
            'description' => ['required', 'string', 'max:255'],
            'category' => ['required', 'in:material,transport,manopera,other'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'expense_date' => ['required', 'date'],
            'document_number' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]));

        return redirect()->route('admin.expenses.index')->with('success', 'Cheltuiala a fost inregistrata.');
    }

    public function destroy(Expense $expense): RedirectResponse
    {
        $expense->delete();

        return back()->with('success', 'Cheltuiala a fost stearsa.');
    }
}
