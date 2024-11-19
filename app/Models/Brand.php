<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $fillable = ['name', 'picture'];

    public function categories()
    {
        return $this->hasMany(Category::class);
    }
    public function items()
    {
        return $this->hasMany(Item::class); // Define the relationship with items
    }
    public function sales()
    {
        return $this->hasMany(Sale::class, 'brand_id');
    }
}

