<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'picture'];

    public function brands()
    {
        return $this->belongsToMany(Brand::class, 'brand_category');
    }

    public function items()
    {
        return $this->hasMany(Item::class);
    }
}

