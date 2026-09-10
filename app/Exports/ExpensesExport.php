<?php

namespace App\Exports;

use App\Models\Expense;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ExpensesExport implements FromQuery, WithHeadings, WithMapping
{
    public function __construct(private array $filters = []) {}

    public function query(): Builder
    {
        return Expense::query()
            ->with(['supplier:id,name', 'installation:id,report_number'])
            ->when($this->filters['category'] ?? null, fn ($query, $category) => $query->where('category', $category))
            ->latest('expense_date');
    }

    public function headings(): array
    {
        return ['Data', 'Descriere', 'Categorie', 'Furnizor', 'Lucrare', 'Suma (lei)', 'Nr. document', 'Note'];
    }

    public function map($expense): array
    {
        return [
            $expense->expense_date?->format('d.m.Y'),
            $expense->description,
            $expense->category,
            $expense->supplier?->name,
            $expense->installation?->report_number,
            number_format((float) $expense->amount, 2, '.', ''),
            $expense->document_number,
            $expense->notes,
        ];
    }
}
