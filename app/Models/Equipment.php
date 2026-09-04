<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
    ];

    public function offerItems(): HasMany
    {
        return $this->hasMany(OfferItem::class);
    }
}
