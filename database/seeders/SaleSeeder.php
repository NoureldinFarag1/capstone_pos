<?php

namespace Database\Seeders;

use App\Models\Sale;
use App\Models\User;
use App\Models\Item;
use App\Models\SaleItem;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get columns from relevant tables
        $saleColumns = Schema::getColumnListing('sales');
        $saleItemColumns = Schema::getColumnListing('sale_items');
        $refundTableExists = Schema::hasTable('refunds');

        // Get users with roles or any users if roles table doesn't exist
        try {
            $cashiers = User::role('cashier')->get();
            $manager = User::role('manager')->first();
            $admin = User::role('admin')->first();
        } catch (\Exception $e) {
            // Fallback if roles don't exist
            $cashiers = User::take(3)->get();
            $manager = User::skip(3)->first();
            $admin = User::skip(4)->first();
        }

        // If no cashiers, use any user
        if ($cashiers->isEmpty()) {
            $cashiers = User::all();
        }

        // Get items that are not parent items (i.e., actual products that can be sold)
        try {
            $items = Item::where('is_parent', false)->get();
        } catch (\Exception $e) {
            // Fallback if is_parent column doesn't exist
            $items = Item::all();
        }

        // Skip if no items or users
        if ($items->isEmpty() || $cashiers->isEmpty()) {
            return;
        }

        // Payment methods
        $paymentMethods = ['cash', 'credit_card', 'mobile_pay', 'cod'];

        // Customer names and phones
        $customerNames = [
            'John Doe', 'Jane Smith', 'Michael Johnson', 'Emily Davis', 'Robert Wilson',
            'Lisa Brown', 'William Jones', 'Sarah Miller', 'David Anderson', 'Jennifer Taylor',
            'James Thomas', 'Mary Jackson', 'Richard White', 'Patricia Harris', 'Charles Martin',
            'Jessica Thompson', 'Christopher Garcia', 'Laura Martinez', 'Daniel Robinson', 'Karen Clark'
        ];

        // Generate sales data for the last 60 days
        $startDate = Carbon::now()->subDays(60);

        // Generate more sales for recent days to show business growth
        $salesDistribution = [];
        $totalWeight = 0;

        // Create a weighted distribution favoring recent days
        for ($i = 0; $i <= 60; $i++) {
            $weight = 1 + ($i / 10); // More recent days get higher weight
            $salesDistribution[60 - $i] = $weight;
            $totalWeight += $weight;
        }

        // Calculate the number of sales based on items (1 sale per 3 items, minimum 30 sales, maximum 300)
        $totalItems = $items->count();
        $totalSales = min(300, max(30, intval($totalItems / 3)));

        // Normalize weights to sum to $totalSales
        foreach ($salesDistribution as $day => $weight) {
            $salesDistribution[$day] = round(($weight / $totalWeight) * $totalSales);
        }

        // Transaction counter for each day (for display_id)
        $dailyTransactions = [];

        // Create sales based on the distribution
        foreach ($salesDistribution as $daysAgo => $numSales) {
            $saleDate = Carbon::now()->subDays($daysAgo)->format('Y-m-d');

            // Initialize daily transaction counter if not set
            if (!isset($dailyTransactions[$saleDate])) {
                $dailyTransactions[$saleDate] = 1;
            }

            for ($i = 0; $i < $numSales; $i++) {
                // Create a sale with random attributes
                $this->createRandomSale([
                    'date' => $saleDate,
                    'displayId' => $dailyTransactions[$saleDate]++,
                    'cashiers' => $cashiers,
                    'manager' => $manager,
                    'admin' => $admin,
                    'items' => $items,
                    'paymentMethods' => $paymentMethods,
                    'customerNames' => $customerNames,
                    'saleColumns' => $saleColumns,
                    'saleItemColumns' => $saleItemColumns,
                    'refundTableExists' => $refundTableExists,
                ]);
            }
        }
    }

    /**
     * Create a single random sale with all necessary attributes
     */
    private function createRandomSale($params)
    {
        $date = $params['date'];
        $displayId = $params['displayId'];
        $cashiers = $params['cashiers'];
        $manager = $params['manager'];
        $admin = $params['admin'];
        $items = $params['items'];
        $paymentMethods = $params['paymentMethods'];
        $customerNames = $params['customerNames'];
        $saleColumns = $params['saleColumns'];
        $saleItemColumns = $params['saleItemColumns'];
        $refundTableExists = $params['refundTableExists'];

        // Randomly select a user - more likely to be a cashier
        $randomValue = rand(1, 100);
        if ($randomValue <= 80) {
            // 80% of sales by cashiers
            $user = $cashiers->random();
        } elseif ($randomValue <= 95) {
            // 15% of sales by manager
            $user = $manager ?? $cashiers->random();
        } else {
            // 5% of sales by admin
            $user = $admin ?? $cashiers->random();
        }

        // Randomize time within the day
        $saleHour = rand(9, 21); // Store hours from 9 AM to 9 PM
        $saleMinute = rand(0, 59);
        $saleSecond = rand(0, 59);

        $saleDateTime = Carbon::parse($date)
            ->setHour($saleHour)
            ->setMinute($saleMinute)
            ->setSecond($saleSecond);

        // Select 1-3 items for the sale
        $saleItemsCount = rand(1, 3);
        $selectedItems = $items->random(min($saleItemsCount, $items->count()));

        // Calculate initial subtotal and prepare sale items data
        $subtotal = 0;
        $saleItemsData = [];

        foreach ($selectedItems as $item) {
            // Random quantity between 1 and 3
            $quantity = rand(1, 3);

            // Get item price after any item-specific discount
            $price = $item->selling_price;
            if (isset($item->discount_type) && $item->discount_type === 'percentage') {
                $price = $price * (1 - ($item->discount_value / 100));
            } elseif (isset($item->discount_type) && $item->discount_type === 'fixed') {
                $price = max($price - $item->discount_value, 0);
            }

            // Special discount (5% chance)
            $specialDiscount = 0;
            if (rand(1, 100) <= 5) {
                $specialDiscount = rand(5, 15);
            }

            // Apply special discount
            $priceAfterSpecialDiscount = $price * (1 - ($specialDiscount / 100));

            // Determine if this item is a gift (2% chance)
            $asGift = rand(1, 100) <= 2;
            if ($asGift) {
                $lineTotal = 0;
            } else {
                $lineTotal = $priceAfterSpecialDiscount * $quantity;
            }

            // Add to subtotal
            $subtotal += $lineTotal;

            // Base sale item data
            $saleItemBase = [
                'item_id' => $item->id,
                'quantity' => $quantity,
                'price' => $asGift ? 0 : $price,
                'special_discount' => $specialDiscount,
                'subtotal' => $lineTotal,
                'as_gift' => $asGift,
            ];

            // Filter based on available columns
            $filteredSaleItem = array_filter(
                $saleItemBase,
                function ($key) use ($saleItemColumns) {
                    return in_array($key, $saleItemColumns);
                },
                ARRAY_FILTER_USE_KEY
            );

            // Store sale item data
            $saleItemsData[] = $filteredSaleItem;
        }

        // Apply sale-level discount (30% chance)
        $discountType = 'none';
        $discountValue = 0;
        $discountAmount = 0;

        if (rand(1, 100) <= 30) {
            $discountType = rand(0, 1) ? 'percentage' : 'fixed';

            if ($discountType === 'percentage') {
                $discountValue = rand(5, 20);
                $discountAmount = $subtotal * ($discountValue / 100);
            } else {
                $discountValue = rand(10, 100);
                $discountAmount = min($discountValue, $subtotal);
            }
        }

        // Add shipping fees (10% chance for delivery orders)
        $shippingFees = 0;
        $address = null;
        if (rand(1, 100) <= 10 && in_array('shipping_fees', $saleColumns)) {
            $shippingFees = rand(20, 100);
            $address = 'Sample Address, City, State, 12345';
        }

        // Calculate final total
        $totalAmount = $subtotal - $discountAmount + $shippingFees;

        // Ensure total is not negative
        $totalAmount = max($totalAmount, 0);

        // Select random payment method
        $paymentMethod = $paymentMethods[array_rand($paymentMethods)];

        // Generate random payment reference for non-cash payments
        $paymentReference = null;
        if ($paymentMethod != 'cash') {
            $paymentReference = strtoupper(substr($paymentMethod, 0, 3)) . '-' . rand(100000, 999999);
        }

        // Select a customer name and phone
        $customerName = $customerNames[array_rand($customerNames)];
        $customerPhone = '01' . rand(0, 2) . rand(10000000, 99999999); // Egyptian-like mobile format

        // Create a random note (10% chance)
        $notes = null;
        if (rand(1, 100) <= 10 && in_array('notes', $saleColumns)) {
            $noteOptions = [
                'Customer requested gift wrapping.',
                'Delivery to be made after 6 PM.',
                'Fragile items, handle with care.',
                'Customer will pick up tomorrow.',
                'Regular customer, apply loyalty discount.',
                'Business purchase, include tax invoice.',
            ];
            $notes = $noteOptions[array_rand($noteOptions)];
        }

        // Add a small chance for refunds (5% chance)
        $refundStatus = 'no_refund'; // Using 'no_refund' as default instead of null
        if (rand(1, 100) <= 5 && in_array('refund_status', $saleColumns)) {
            $refundStatus = rand(0, 1) ? 'partial_refund' : 'full_refund';
        }

        // Base sale data
        $saleData = [
            'user_id' => $user->id,
            'total_amount' => $totalAmount,
            'subtotal' => $subtotal,
            'customer_name' => $customerName,
            'customer_phone' => $customerPhone,
            'payment_method' => $paymentMethod,
            'payment_reference' => $paymentReference,
            'discount_type' => $discountType,
            'discount_value' => $discountValue,
            'discount' => $discountAmount,
            'shipping_fees' => $shippingFees,
            'address' => $address,
            'notes' => $notes,
            'display_id' => $displayId,
            'sale_date' => $date,
            'refund_status' => $refundStatus,
            'created_at' => $saleDateTime,
            'updated_at' => $saleDateTime,
        ];

        // Filter based on available columns
        $filteredSaleData = array_filter(
            $saleData,
            function ($key) use ($saleColumns) {
                return in_array($key, $saleColumns);
            },
            ARRAY_FILTER_USE_KEY
        );

        // Create the sale record
        $sale = Sale::create($filteredSaleData);

        // Create sale items
        foreach ($saleItemsData as $saleItemData) {
            $saleItem = new SaleItem($saleItemData);
            $saleItem->created_at = $saleDateTime;
            $saleItem->updated_at = $saleDateTime;
            $sale->saleItems()->save($saleItem);
        }

        // If it's a refund and refund table exists, create refund records
        if (($refundStatus === 'partial_refund' || $refundStatus === 'full_refund') && $refundTableExists) {
            $refundDate = Carbon::parse($saleDateTime)->addDays(rand(1, 5));

            // Get refund table columns
            $refundColumns = Schema::getColumnListing('refunds');

            // For full refunds, refund all items
            if ($refundStatus === 'full_refund') {
                foreach ($sale->saleItems as $saleItem) {
                    // Skip gift items
                    if (isset($saleItem->as_gift) && $saleItem->as_gift) {
                        continue;
                    }

                    $refundData = [
                        'sale_id' => $sale->id,
                        'item_id' => $saleItem->item_id,
                        'quantity' => $saleItem->quantity,
                        'quantity_refunded' => $saleItem->quantity, // Add this field
                        'refund_amount' => $saleItem->subtotal,
                        'reason' => $this->getRandomRefundReason(),
                        'created_at' => $refundDate,
                        'updated_at' => $refundDate,
                    ];

                    // Filter based on available columns
                    $filteredRefundData = array_filter(
                        $refundData,
                        function ($key) use ($refundColumns) {
                            return in_array($key, $refundColumns);
                        },
                        ARRAY_FILTER_USE_KEY
                    );

                    DB::table('refunds')->insert($filteredRefundData);
                }
            }
            // For partial refunds, refund a random selection of items
            else {
                $itemsToRefund = $sale->saleItems->filter(function ($saleItem) {
                    return !(isset($saleItem->as_gift) && $saleItem->as_gift) && rand(0, 1) == 1;
                });

                if ($itemsToRefund->isEmpty() && !$sale->saleItems->isEmpty()) {
                    // Ensure at least one item is refunded for partial refunds
                    $itemsToRefund = $sale->saleItems->filter(function ($saleItem) {
                        return !(isset($saleItem->as_gift) && $saleItem->as_gift);
                    })->take(1);
                }

                foreach ($itemsToRefund as $saleItem) {
                    // Refund either all or partial quantity
                    $refundQuantity = rand(0, 1) == 1 ? $saleItem->quantity : rand(1, $saleItem->quantity);
                    $refundAmount = ($saleItem->subtotal / $saleItem->quantity) * $refundQuantity;

                    $refundData = [
                        'sale_id' => $sale->id,
                        'item_id' => $saleItem->item_id,
                        'quantity' => $refundQuantity,
                        'quantity_refunded' => $refundQuantity, // Add this field
                        'refund_amount' => $refundAmount,
                        'reason' => $this->getRandomRefundReason(),
                        'created_at' => $refundDate,
                        'updated_at' => $refundDate,
                    ];

                    // Filter based on available columns
                    $filteredRefundData = array_filter(
                        $refundData,
                        function ($key) use ($refundColumns) {
                            return in_array($key, $refundColumns);
                        },
                        ARRAY_FILTER_USE_KEY
                    );

                    DB::table('refunds')->insert($filteredRefundData);
                }
            }
        }
    }

    /**
     * Get a random refund reason
     */
    private function getRandomRefundReason()
    {
        $reasons = [
            'Item damaged upon receipt',
            'Wrong size/color ordered',
            'Product not as described',
            'Customer changed mind',
            'Better price found elsewhere',
            'Defective product',
            'Shipping took too long',
            'Duplicate order',
            'Accidental purchase',
            'Item no longer needed',
        ];

        return $reasons[array_rand($reasons)];
    }
}
