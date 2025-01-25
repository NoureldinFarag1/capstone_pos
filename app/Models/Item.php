<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{

    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'category_id',
        'brand_id',
        'picture',
        'quantity',
        'buying_price',
        'selling_price',
        'tax',
        'discount_type',
        'discount_value',
        'parent_id',
        'is_parent',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function brand()
    {
        return $this->belongsTo(Brand::class); // Define the relationship with Brand
    }
    public static function lowStockItems()
    {
        return self::where('quantity', '<=', 10)->get();
    }

    public function priceAfterSale()
    {
        // Calculate discount amount if applied sale exists
        $discountAmount = 0;
        if ($this->discount_value && $this->discount_type === 'percentage') {
            $discountAmount = $this->selling_price * ($this->discount_value / 100);
        }

        elseif ($this->discount_value && $this->discount_type === 'fixed') {
            $discountAmount = min($this->discount_value, $this->selling_price);  // Ensure the discount is not greater than the selling price
        }

        // Calculate selling price after discount
        return $this->selling_price - $discountAmount;
    }

    public function sales()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function salesItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function sellingPriceWithTax()
    {
        // Calculate discount amount if applied sale exists
        $discountAmount = 0;
        if ($this->discount_value) {
            $discountAmount = $this->selling_price * ($this->discount_value / 100);
        }

        // Calculate selling price after discount
        $sellingPriceAfterDiscount = $this->selling_price - $discountAmount;

        // Calculate selling price including tax
        return $sellingPriceAfterDiscount + ($sellingPriceAfterDiscount * ($this->tax / 100));
    }
    public function getFormattedDiscountAttribute()
    {
        if ($this->discount_type === 'percentage') {
            return number_format($this->discount_value * ($this->selling_price / 100), 2);
        }
        return number_format($this->discount_value, 2);
    }

    public function netProfit()
    {
        return $this->sellingPriceWithTax() - $this->buying_price;
    }

    public function sizes()
    {
        return $this->belongsToMany(Size::class);
    }

    public function colors()
    {
        return $this->belongsToMany(Color::class);
    }

    public function parent()
    {
        return $this->belongsTo(Item::class, 'parent_id');
    }

    public function variants()
    {
        return $this->hasMany(Item::class, 'parent_id');
    }

}
