<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'contact_name', 'email', 'phone', 'tax_id', 'notes'];

    public function equipment(): HasMany
    {
        return $this->hasMany(Equipment::class);
    }
}
