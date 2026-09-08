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
        'photos',
        'customer_name',
        'customer_notes',
        'completed_at',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'labor_hours' => 'decimal:2',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'checklist' => 'array',
        'materials' => 'array',
        'photos' => 'array',
        'completed_at' => 'datetime',
    ];

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
}
