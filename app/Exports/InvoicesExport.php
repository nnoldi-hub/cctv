<?php

namespace App\Exports;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class InvoicesExport implements FromQuery, WithHeadings, WithMapping
{
    public function __construct(private array $filters = []) {}

    public function query(): Builder
    {
        return Invoice::query()
            ->with('client:id,name')
            ->when($this->filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('invoice_number', 'like', "%{$search}%")
                        ->orWhereHas('client', fn ($c) => $c->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($this->filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->latest();
    }

    public function headings(): array
    {
        return ['Numar factura', 'Client', 'Suma (lei)', 'Status', 'Data emitere', 'Scadenta', 'Data plata'];
    }

    public function map($invoice): array
    {
        return [
            $invoice->invoice_number,
            $invoice->client->name,
            number_format((float) $invoice->amount, 2, '.', ''),
            $invoice->status,
            $invoice->issued_at?->format('d.m.Y'),
            $invoice->due_at?->format('d.m.Y'),
            $invoice->paid_at?->format('d.m.Y'),
        ];
    }
}
