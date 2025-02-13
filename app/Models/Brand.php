<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $fillable = ['name', 'picture'];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'brand_category');
    }

    public function items()
    {
        return $this->hasMany(Item::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class, 'brand_id');
    }
}

