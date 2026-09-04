<?php

namespace App\Exports;

use App\Models\Offer;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class OffersExport implements FromQuery, WithHeadings, WithMapping
{
    public function __construct(private array $filters = []) {}

    public function query(): Builder
    {
        return Offer::query()
            ->with('client:id,name')
            ->when($this->filters['search'] ?? null, function ($query, $search) {
                $query->whereHas('client', fn ($q) => $q->where('name', 'like', "%{$search}%"));
            })
            ->when($this->filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->latest();
    }

    public function headings(): array
    {
        return ['ID', 'Client', 'Titlu', 'Status', 'Valoare (lei)', 'Valabila pana la', 'Creata la'];
    }

    public function map($offer): array
    {
        return [
            $offer->id,
            $offer->client->name,
            $offer->title,
            $offer->status,
            number_format((float) $offer->total_amount, 2, '.', ''),
            $offer->valid_until?->format('d.m.Y'),
            $offer->created_at->format('d.m.Y H:i'),
        ];
    }
}
