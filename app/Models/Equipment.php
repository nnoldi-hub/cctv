<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Equipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'sku',
        'unit',
        'unit_price',
        'cost_price',
        'stock_quantity',
        'minimum_stock',
        'description',
        'is_active',
        'client_id',
        'supplier_id',
        'markup_percent',
        'location',
        'warranty_until',
        'installed_at',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'markup_percent' => 'decimal:2',
        'is_active' => 'boolean',
        'minimum_stock' => 'integer',
        'warranty_until' => 'date',
        'installed_at' => 'date',
    ];

    public function offerItems(): HasMany
    {
        return $this->hasMany(OfferItem::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function getProfitAmountAttribute(): float
    {
        return (float) $this->unit_price - (float) $this->cost_price;
    }
}
