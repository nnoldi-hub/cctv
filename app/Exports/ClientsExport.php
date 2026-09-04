<?php

namespace App\Exports;

use App\Models\Client;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ClientsExport implements FromQuery, WithHeadings, WithMapping
{
    public function __construct(private array $filters = []) {}

    public function query(): Builder
    {
        return Client::query()
            ->with('assignedTo:id,name')
            ->when($this->filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($this->filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($this->filters['source'] ?? null, fn ($query, $source) => $query->where('source', $source))
            ->latest();
    }

    public function headings(): array
    {
        return ['Nume', 'Firma', 'Email', 'Telefon', 'Oras', 'Sursa', 'Status', 'Asignat', 'Creat la'];
    }

    public function map($client): array
    {
        return [
            $client->name,
            $client->company_name,
            $client->email,
            $client->phone,
            $client->city,
            $client->source,
            $client->status,
            $client->assignedTo?->name,
            $client->created_at->format('d.m.Y H:i'),
        ];
    }
}
