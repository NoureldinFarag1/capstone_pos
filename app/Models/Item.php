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
        'applied_sale',];

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
        if ($this->applied_sale) {
            $discountAmount = $this->selling_price * ($this->applied_sale / 100);
        }
        
        // Calculate selling price after discount
        return $this->selling_price - $discountAmount;
    }
    
    public function sellingPriceWithTax()
    {
        // Calculate discount amount if applied sale exists
        $discountAmount = 0;
        if ($this->applied_sale) {
            $discountAmount = $this->selling_price * ($this->applied_sale / 100);
        }
        
        // Calculate selling price after discount
        $sellingPriceAfterDiscount = $this->selling_price - $discountAmount;

        // Calculate selling price including tax
        return $sellingPriceAfterDiscount + ($sellingPriceAfterDiscount * ($this->tax / 100));
    }

    public function netProfit()
    {
        return $this->sellingPriceWithTax() - $this->buying_price;
    }
}
