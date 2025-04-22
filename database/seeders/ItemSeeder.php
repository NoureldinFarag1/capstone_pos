<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get available columns
        $columns = Schema::getColumnListing('items');

        $brands = Brand::all();
        $categories = Category::all();

        if ($brands->isEmpty() || $categories->isEmpty()) {
            $this->command->info('Skipping ItemSeeder: No brands or categories found.');
            return;
        }

        // Define some common products for each category
        $categoryProducts = [
            'Electronics' => [
                'Smartphone', 'Laptop', 'Tablet', 'Smartwatch', 'Headphones',
                'Bluetooth Speaker', 'Power Bank', 'Wireless Charger', 'Monitor', 'Keyboard'
            ],
            'Clothing' => [
                'T-Shirt', 'Jeans', 'Dress', 'Sweater', 'Jacket',
                'Shorts', 'Skirt', 'Hoodie', 'Polo Shirt', 'Socks'
            ],
            'Home & Kitchen' => [
                'Coffee Maker', 'Blender', 'Toaster', 'Microwave', 'Cookware Set',
                'Knife Set', 'Dinnerware Set', 'Mixing Bowl', 'Food Storage Container', 'Cutting Board'
            ],
            'Beauty & Personal Care' => [
                'Shampoo', 'Conditioner', 'Body Wash', 'Face Cream', 'Sunscreen',
                'Makeup Set', 'Perfume', 'Hair Dryer', 'Electric Shaver', 'Facial Cleanser'
            ],
            'Sports & Outdoors' => [
                'Running Shoes', 'Yoga Mat', 'Dumbbell Set', 'Tennis Racket', 'Basketball',
                'Bicycle', 'Camping Tent', 'Hiking Backpack', 'Fitness Tracker', 'Golf Clubs'
            ],
            'Books' => [
                'Novel', 'Cookbook', 'Self-Help Book', 'Biography', 'History Book',
                'Science Fiction', 'Children\'s Book', 'Business Book', 'Art Book', 'Educational Textbook'
            ],
            'Toys & Games' => [
                'Action Figure', 'Board Game', 'Puzzle', 'Remote Control Car', 'Doll',
                'Building Blocks', 'Stuffed Animal', 'Card Game', 'Educational Toy', 'Video Game'
            ],
            'Grocery' => [
                'Coffee', 'Tea', 'Cereal', 'Pasta', 'Chocolate',
                'Snack Bar', 'Juice', 'Spices', 'Olive Oil', 'Energy Drink'
            ],
            'Health & Wellness' => [
                'Vitamin C', 'Multivitamin', 'Protein Powder', 'Essential Oils', 'First Aid Kit',
                'Massage Device', 'Blood Pressure Monitor', 'Digital Thermometer', 'Fitness Supplement', 'Sleep Aid'
            ],
            'Accessories' => [
                'Necklace', 'Bracelet', 'Earrings', 'Watch', 'Sunglasses',
                'Handbag', 'Wallet', 'Scarf', 'Belt', 'Hat'
            ],
        ];

        // Create items
        foreach ($categories as $category) {
            // Get products for this category
            $products = $categoryProducts[$category->name] ??
                        ['Product 1', 'Product 2', 'Product 3', 'Product 4', 'Product 5'];

            foreach ($brands as $brand) {
                // Not all brands make all types of products - add some variation
                if (rand(0, 100) > 70) {
                    continue;
                }

                // Create 1-2 products for this brand in this category
                $numProducts = rand(1, 2);

                for ($i = 0; $i < $numProducts; $i++) {
                    $productName = $products[array_rand($products)];

                    // Create some variations in names to avoid duplicates
                    $modifiers = ['', 'Pro', 'Plus', 'Ultra', 'Mini', 'Max', 'Lite', 'Premium', 'Elite', 'Classic'];
                    $productName .= ' ' . $modifiers[array_rand($modifiers)];

                    // Ensure the name is unique by adding a random string if needed
                    if (Item::where('name', $productName)->where('brand_id', $brand->id)->exists()) {
                        $productName .= ' ' . Str::random(3);
                    }

                    // Random cost and selling price
                    $cost = rand(50, 5000);
                    $sellingPrice = $cost * (rand(120, 200) / 100); // 20-100% markup

                    // Random discount (20% chance of having a discount)
                    $discountType = 'none';
                    $discountValue = 0;
                    if (rand(0, 100) < 20) {
                        $discountType = rand(0, 1) ? 'percentage' : 'fixed';
                        $discountValue = $discountType === 'percentage' ? rand(5, 30) : rand(10, 50);
                    }

                    // Random quantity (some items low in stock)
                    $quantity = rand(0, 100);
                    if ($quantity <= 5) {
                        $quantity = rand(0, 5); // Ensure some items are low in stock
                    }

                    // Create a random SKU
                    $sku = strtoupper(substr($brand->name, 0, 3)) . '-' .
                           strtoupper(substr($category->name, 0, 3)) . '-' .
                           rand(1000, 9999);

                    // Base item data
                    $itemData = [
                        'name' => $productName,
                        'brand_id' => $brand->id,
                        'category_id' => $category->id,
                        'description' => "A quality $productName from {$brand->name}.",
                        'cost_price' => $cost,
                        'selling_price' => $sellingPrice,
                        'sku' => $sku,
                        'barcode' => rand(100000000000, 999999999999) . '.png',
                        'quantity' => $quantity,
                        'min_stock_level' => rand(5, 20),
                        'discount_type' => $discountType,
                        'discount_value' => $discountValue,
                        'is_parent' => false, // Default to regular item
                    ];

                    // Filter data based on available columns
                    $filteredData = array_filter(
                        $itemData,
                        function ($key) use ($columns) {
                            return in_array($key, $columns);
                        },
                        ARRAY_FILTER_USE_KEY
                    );

                    // Create the item
                    Item::create($filteredData);
                }
            }
        }

        // Skip parent/variant creation if is_parent column doesn't exist
        if (!in_array('is_parent', $columns) || !in_array('parent_id', $columns)) {
            return;
        }

        // Create some parent items with variants (10% of existing items)
        $items = Item::where('is_parent', false)->whereNull('parent_id')->get();
        $itemsToConvertCount = min(10, ceil($items->count() * 0.1));

        for ($i = 0; $i < $itemsToConvertCount; $i++) {
            // Pick a random item
            $originalItem = $items->random();

            // Make it a parent
            $originalItem->update([
                'is_parent' => true,
                'quantity' => 0 // Parent items don't maintain quantity
            ]);

            // Create 2-3 variants
            $variantCount = rand(2, 3);

            // Possible variant types
            $variantTypes = ['Size', 'Color', 'Material', 'Style', 'Type'];
            $variantType = $variantTypes[array_rand($variantTypes)];

            $variantOptions = [];
            if ($variantType === 'Size') {
                $variantOptions = ['Small', 'Medium', 'Large', 'X-Large', 'XX-Large'];
            } elseif ($variantType === 'Color') {
                $variantOptions = ['Red', 'Blue', 'Green', 'Black', 'White', 'Silver', 'Gold'];
            } elseif ($variantType === 'Material') {
                $variantOptions = ['Cotton', 'Polyester', 'Leather', 'Plastic', 'Metal', 'Wood'];
            } elseif ($variantType === 'Style') {
                $variantOptions = ['Classic', 'Modern', 'Vintage', 'Sporty', 'Casual', 'Formal'];
            } else {
                $variantOptions = ['Type A', 'Type B', 'Type C', 'Type D', 'Type E'];
            }

            for ($j = 0; $j < $variantCount; $j++) {
                // Get variant option
                $option = $variantOptions[$j % count($variantOptions)];

                // Create a variant with similar properties to parent but with a variant name
                $variantData = [
                    'name' => $originalItem->name . ' - ' . $option,
                    'brand_id' => $originalItem->brand_id,
                    'category_id' => $originalItem->category_id,
                    'description' => $originalItem->description,
                    'cost_price' => $originalItem->cost_price * (rand(90, 110) / 100), // Slight price variation
                    'selling_price' => $originalItem->selling_price * (rand(90, 110) / 100),
                    'sku' => $originalItem->sku . '-' . substr($option, 0, 1),
                    'barcode' => rand(100000000000, 999999999999) . '.png',
                    'quantity' => rand(0, 50),
                    'min_stock_level' => $originalItem->min_stock_level,
                    'discount_type' => $originalItem->discount_type,
                    'discount_value' => $originalItem->discount_value,
                    'is_parent' => false,
                    'parent_id' => $originalItem->id, // Set parent relationship
                ];

                // Filter data based on available columns
                $filteredVariantData = array_filter(
                    $variantData,
                    function ($key) use ($columns) {
                        return in_array($key, $columns);
                    },
                    ARRAY_FILTER_USE_KEY
                );

                // Create the variant
                Item::create($filteredVariantData);
            }
        }
    }
}
