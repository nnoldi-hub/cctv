<?php

namespace App\Exports;

use App\Models\Subscription;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SubscriptionsExport implements FromQuery, WithHeadings, WithMapping
{
    public function __construct(private array $filters = []) {}

    public function query(): Builder
    {
        return Subscription::query()
            ->with('client:id,name')
            ->when($this->filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->orderBy('next_billing_at');
    }

    public function headings(): array
    {
        return ['Client', 'Plan', 'Pret (lei)', 'Ciclu', 'Status', 'Inceput', 'Urmatoarea facturare'];
    }

    public function map($subscription): array
    {
        return [
            $subscription->client->name,
            $subscription->plan,
            number_format((float) $subscription->price, 2, '.', ''),
            $subscription->billing_cycle,
            $subscription->status,
            $subscription->started_at->format('d.m.Y'),
            $subscription->next_billing_at?->format('d.m.Y'),
        ];
    }
}
