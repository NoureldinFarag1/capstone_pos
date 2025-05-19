<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'notes',
        'total_visits',
        'total_spent',
        'last_visit',
    ];

    protected $casts = [
        'last_visit' => 'datetime',
        'total_spent' => 'decimal:2',
    ];

    /**
     * Get all sales for this customer
     */
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    /**
     * Calculate average transaction value
     */
    public function getAverageTransactionAttribute()
    {
        return $this->total_visits > 0 ? $this->total_spent / $this->total_visits : 0;
    }
}
