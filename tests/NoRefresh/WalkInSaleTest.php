<?php

namespace Tests\NoRefresh;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Sale;
use Illuminate\Support\Facades\Hash;

class WalkInSaleTest extends TestCase
{
    /** @test */
    public function it_creates_a_walk_in_sale_without_customer_fields()
    {
        // Authenticate
        $user = User::factory()->create([
            'role' => 'admin',
            'password' => Hash::make('password'),
        ]);
        $this->actingAs($user);

        // Create dependencies
        $brand = Brand::first() ?? Brand::create(['name' => 'TestBrand']);
        $category = Category::first() ?? Category::create(['name' => 'TestCategory']);

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
            'notes' => 'Walk-in test',
        ]);

        $response->assertRedirect(route('sales.index'));
        $response->assertSessionHas('success');

        $sale = Sale::latest()->first();
        $this->assertNotNull($sale);
        $this->assertNull($sale->customer_name);
        $this->assertNull($sale->customer_phone);
        $this->assertEquals(1, $sale->saleItems()->count());
    }
}
