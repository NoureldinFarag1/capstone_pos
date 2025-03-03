<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Receipt extends Model
{
    protected $fillable = ['total', 'created_at'];

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'sale_items')
            ->withPivot(['quantity', 'price'])
            ->withTimestamps();
    }
}