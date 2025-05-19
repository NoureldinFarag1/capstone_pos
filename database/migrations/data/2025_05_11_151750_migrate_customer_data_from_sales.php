<?php

use App\Models\Customer;
use App\Models\Sale;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     * This migration transfers existing customer data from sales to the customers table
     */
    public function up(): void
    {
        // Group sales by customer phone to get unique customers
        $customers = DB::table('sales')
            ->select(
                'customer_name',
                'customer_phone',
                DB::raw('COUNT(*) as visit_count'),
                DB::raw('SUM(total_amount) as total_spent'),
                DB::raw('MAX(created_at) as last_visit')
            )
            ->whereNotNull('customer_phone')
            ->groupBy('customer_name', 'customer_phone')
            ->get();

        Log::info('Starting customer data migration: ' . $customers->count() . ' unique customers found');

        // Create a new customer record for each unique customer
        foreach ($customers as $customer) {
            // Skip if phone number is empty or if customer with this phone already exists
            if (empty($customer->customer_phone) || Customer::where('phone', $customer->customer_phone)->exists()) {
                continue;
            }

            // Set a default name for customers with null name
            $customerName = $customer->customer_name;
            if (empty($customerName)) {
                $customerName = 'Customer (' . $customer->customer_phone . ')';
            }

            // Create the customer record
            $newCustomer = Customer::create([
                'name' => $customerName,
                'phone' => $customer->customer_phone,
                'total_visits' => $customer->visit_count,
                'total_spent' => $customer->total_spent,
                'last_visit' => $customer->last_visit,
            ]);

            Log::info("Migrated customer: {$newCustomer->name}, Phone: {$newCustomer->phone}");

            // Update all sales for this customer to include the customer_id
            DB::table('sales')
                ->where('customer_phone', $customer->customer_phone)
                ->update(['customer_id' => $newCustomer->id]);
        }

        Log::info('Customer data migration completed');
    }

    /**
     * Reverse the migrations.
     * This is a destructive operation that cannot be easily reversed
     */
    public function down(): void
    {
        // Clear customer_id from sales
        DB::table('sales')->update(['customer_id' => null]);

        // Truncate customers table
        DB::table('customers')->truncate();

        Log::info('Customer data migration reversed');
    }
};
