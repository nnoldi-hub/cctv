<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SitePackage extends Model
{
    protected $fillable = [
        'key',
        'name',
        'price_from',
        'cameras',
        'resolution',
        'storage_days',
        'features',
        'highlight',
        'active',
        'sort_order',
    ];

    protected $casts = [
        'price_from' => 'decimal:2',
        'features' => 'array',
        'highlight' => 'boolean',
        'active' => 'boolean',
    ];
}
