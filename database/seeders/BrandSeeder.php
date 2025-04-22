<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the column names from the brands table
        $columns = Schema::getColumnListing('brands');

        // Create popular brands
        $brands = [
            ['name' => 'Apple', 'description' => 'Premium electronics and devices'],
            ['name' => 'Samsung', 'description' => 'Electronic devices and appliances'],
            ['name' => 'Nike', 'description' => 'Athletic footwear and apparel'],
            ['name' => 'Adidas', 'description' => 'Sports clothing and equipment'],
            ['name' => 'Sony', 'description' => 'Electronics and entertainment'],
            ['name' => 'H&M', 'description' => 'Fast fashion clothing retailer'],
            ['name' => 'IKEA', 'description' => 'Furniture and home accessories'],
            ['name' => 'Nestlé', 'description' => 'Food and beverage products'],
            ['name' => 'L\'Oréal', 'description' => 'Cosmetics and beauty products'],
            ['name' => 'Philips', 'description' => 'Electronics and appliances'],
            ['name' => 'Zara', 'description' => 'Clothing and accessories'],
            ['name' => 'Puma', 'description' => 'Athletic and casual footwear'],
            ['name' => 'Canon', 'description' => 'Cameras and imaging products'],
            ['name' => 'LG', 'description' => 'Electronics and home appliances'],
            ['name' => 'Dell', 'description' => 'Computers and technology products'],
        ];

        foreach ($brands as $brandData) {
            // Check if the name already exists
            if (Brand::where('name', $brandData['name'])->exists()) {
                continue; // Skip if brand already exists
            }

            // Filter the data based on available columns
            $filteredData = array_filter(
                $brandData,
                function ($key) use ($columns) {
                    return in_array($key, $columns);
                },
                ARRAY_FILTER_USE_KEY
            );

            // Create brand with valid columns only
            Brand::create($filteredData);
        }
    }
}
