<?php

namespace App\Exports;

use App\Models\Client;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ClientStatementExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private Client $client) {}

    public function collection(): Collection
    {
        return $this->client->invoices()->with('payments')->latest()->get();
    }

    public function headings(): array
    {
        return [
            'Numar factura',
            'Data emitere',
            'Scadenta',
            'Suma factura (lei)',
            'Achitat (lei)',
            'Sold (lei)',
            'Status',
            'Ultima plata',
        ];
    }

    public function map($invoice): array
    {
        $balance = max((float) $invoice->amount - (float) $invoice->paid_amount, 0);
        $lastPayment = $invoice->payments->sortByDesc('paid_at')->first();

        return [
            $invoice->invoice_number,
            $invoice->issued_at?->format('d.m.Y') ?? $invoice->created_at->format('d.m.Y'),
            $invoice->due_at?->format('d.m.Y'),
            number_format((float) $invoice->amount, 2, '.', ''),
            number_format((float) $invoice->paid_amount, 2, '.', ''),
            number_format($balance, 2, '.', ''),
            $invoice->status,
            $lastPayment?->paid_at?->format('d.m.Y'),
        ];
    }
}
