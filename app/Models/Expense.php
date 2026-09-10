<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'installation_id',
        'description',
        'category',
        'amount',
        'expense_date',
        'document_number',
        'document_path',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
    ];

    protected $appends = ['document_url'];

    public function getDocumentUrlAttribute(): ?string
    {
        return $this->document_path ? Storage::url($this->document_path) : null;
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function installation(): BelongsTo
    {
        return $this->belongsTo(Installation::class);
    }
}
