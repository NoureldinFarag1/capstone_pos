<?php

use App\Models\User;
use App\Models\Item;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Sale;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

it('creates a walk-in sale without customer fields', function() {
    // Ensure clean state
    DB::beginTransaction();

    // Create user (role admin for discount bypass)
    $user = User::factory()->create([
        'role' => 'admin',
        'password' => Hash::make('password')
    ]);
    actingAs($user);

    // Create brand & category
    $brand = Brand::create(['name' => 'TestBrand']);
    $category = Category::create(['name' => 'TestCategory']);

    // Create item
    $item = Item::create([
        'name' => 'Test Item',
        'category_id' => $category->id,
        'brand_id' => $brand->id,
        'selling_price' => 100,
        'buying_price' => 50,
        'quantity' => 20,
        'discount_type' => 'none',
        'discount_value' => 0,
        'is_parent' => false,
    ]);

    // Post sale with skip_customer flag
    $response = $this->post(route('sales.store'), [
        'items' => [
            [
                'item_id' => $item->id,
                'quantity' => 2,
                'price' => 100,
                'special_discount' => 0,
                'as_gift' => 0,
            ]
        ],
        'skip_customer' => 1,
        'payment_method' => 'cash',
        'discount_type' => 'none',
        'discount_value' => 0,
        'shipping_fees' => 0,
        'notes' => 'Walk-in test'
    ]);

    // Expect redirect success
    $response->assertRedirect(route('sales.index'));
    $response->assertSessionHas('success');

    $sale = Sale::latest()->first();
    expect($sale)->not()->toBeNull();
    expect($sale->customer_name)->toBeNull();
    expect($sale->customer_phone)->toBeNull();
    expect($sale->saleItems()->count())->toBe(1);

    DB::rollBack(); // Cleanup
});
