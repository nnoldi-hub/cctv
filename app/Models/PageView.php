<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageView extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'session_id',
        'visitor_id',
        'user_id',
        'url',
        'path',
        'page_title',
        'method',
        'ip_address',
        'device_type',
        'browser',
        'platform',
        'user_agent',
        'referer',
        'referer_domain',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_content',
        'is_bot',
        'created_at',
    ];

    protected $casts = [
        'is_bot' => 'boolean',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeHuman($query)
    {
        return $query->where('is_bot', false);
    }

    public function scopePeriod($query, $start, $end)
    {
        return $query->whereBetween('created_at', [$start, $end]);
    }
}
