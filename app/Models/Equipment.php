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
        'stock_quantity',
        'description',
        'client_id',
        'location',
        'warranty_until',
        'installed_at',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
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
}
