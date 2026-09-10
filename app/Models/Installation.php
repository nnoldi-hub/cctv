<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Installation extends Model
{
    use HasFactory;

    public const CHECKLIST_TEMPLATE = [
        'Verificare si pregatire cabluri',
        'Montaj si fixare camere',
        'Configurare NVR/DVR',
        'Testare imagine pe toate camerele',
        'Configurare acces remote (telefon/aplicatie)',
        'Curatenie zona de lucru',
        'Instruire client privind utilizarea sistemului',
    ];

    protected $fillable = [
        'client_id',
        'offer_id',
        'technician_id',
        'type',
        'address',
        'latitude',
        'longitude',
        'scheduled_at',
        'labor_hours',
        'status',
        'notes',
        'checklist',
        'materials',
        'material_items',
        'service_items',
        'stock_consumed_at',
        'photos',
        'customer_name',
        'customer_notes',
        'completed_at',
        'report_number',
        'handover_at',
        'technician_signature',
        'customer_signature',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'labor_hours' => 'decimal:2',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'checklist' => 'array',
        'materials' => 'array',
        'material_items' => 'array',
        'service_items' => 'array',
        'photos' => 'array',
        'completed_at' => 'datetime',
        'handover_at' => 'datetime',
        'stock_consumed_at' => 'datetime',
    ];

    protected $appends = ['cost_report'];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public static function defaultChecklist(): array
    {
        return collect(self::CHECKLIST_TEMPLATE)
            ->map(fn (string $label) => ['label' => $label, 'done' => false])
            ->all();
    }

    public function getCostReportAttribute(): array
    {
        $materialItems = collect($this->material_items ?? []);
        $serviceItems = collect($this->service_items ?? []);

        $equipment = Equipment::query()
            ->whereIn('id', $materialItems->pluck('equipment_id')->filter()->unique())
            ->get()
            ->keyBy('id');
        $services = Service::query()
            ->whereIn('id', $serviceItems->pluck('service_id')->filter()->unique())
            ->get()
            ->keyBy('id');

        $materialCost = $materialItems->sum(
            fn (array $item): float => (float) ($item['quantity'] ?? 0)
                * (float) ($equipment->get($item['equipment_id'])?->cost_price ?? 0)
        );
        $laborCost = $serviceItems->sum(
            fn (array $item): float => (float) ($item['quantity'] ?? 0)
                * (float) ($services->get($item['service_id'])?->cost_price ?? 0)
        );
        $offerValue = (float) ($this->offer?->total_amount ?? 0);
        $totalCost = $materialCost + $laborCost;

        return [
            'offer_value' => round($offerValue, 2),
            'material_cost' => round($materialCost, 2),
            'labor_cost' => round($laborCost, 2),
            'total_cost' => round($totalCost, 2),
            'estimated_profit' => round($offerValue - $totalCost, 2),
            'final_profit' => $this->status === 'completed' ? round($offerValue - $totalCost, 2) : null,
        ];
    }
}
