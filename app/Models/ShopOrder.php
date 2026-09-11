<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShopOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'client_id',
        'name',
        'email',
        'phone',
        'shipping_address',
        'shipping_city',
        'notes',
        'wants_installation',
        'payment_method',
        'subtotal',
        'discount_total',
        'manual_discount',
        'shipping_cost',
        'total',
        'status',
        'invoice_id',
    ];

    protected $casts = [
        'wants_installation' => 'boolean',
        'subtotal' => 'decimal:2',
        'discount_total' => 'decimal:2',
        'manual_discount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ShopOrderItem::class);
    }

    public static function nextOrderNumber(): string
    {
        $count = self::count() + 1;

        return sprintf('SHOP-%s-%04d', now()->format('Y'), $count);
    }
}
