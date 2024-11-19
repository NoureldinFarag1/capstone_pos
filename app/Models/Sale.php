<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = ['total_amount'];

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }
}
