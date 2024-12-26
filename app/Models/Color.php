<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Color extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'hex_code'];

    // Relationship: A color can belong to many items
    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class);
    }
}
