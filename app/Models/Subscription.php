<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'plan',
        'price',
        'billing_cycle',
        'status',
        'started_at',
        'next_billing_at',
        'notes',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'started_at' => 'date',
        'next_billing_at' => 'date',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
