<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stat extends Model
{
    protected $fillable = ['key', 'value', 'description', 'icon', 'is_dynamic'];

    protected $casts = ['is_dynamic' => 'boolean'];
}
