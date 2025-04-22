<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the column names from the categories table
        $columns = Schema::getColumnListing('categories');

        // Define category data
        $categories = [
            ['name' => 'Electronics', 'description' => 'Electronic devices and accessories'],
            ['name' => 'Clothing', 'description' => 'Fashion items and apparel'],
            ['name' => 'Home & Kitchen', 'description' => 'Home decoration and kitchen utilities'],
            ['name' => 'Beauty & Personal Care', 'description' => 'Beauty products and personal care items'],
            ['name' => 'Sports & Outdoors', 'description' => 'Sports equipment and outdoor gear'],
            ['name' => 'Books', 'description' => 'Books, novels and educational material'],
            ['name' => 'Toys & Games', 'description' => 'Children toys and board games'],
            ['name' => 'Grocery', 'description' => 'Food and beverage items'],
            ['name' => 'Health & Wellness', 'description' => 'Health supplements and wellness products'],
            ['name' => 'Accessories', 'description' => 'Fashion accessories and jewelry'],
        ];

        foreach ($categories as $categoryData) {
            // Check if the name already exists
            if (Category::where('name', $categoryData['name'])->exists()) {
                continue; // Skip if category already exists
            }

            // Filter the data based on available columns
            $filteredData = array_filter(
                $categoryData,
                function ($key) use ($columns) {
                    return in_array($key, $columns);
                },
                ARRAY_FILTER_USE_KEY
            );

            // Create category with valid columns only
            Category::create($filteredData);
        }
    }
}
