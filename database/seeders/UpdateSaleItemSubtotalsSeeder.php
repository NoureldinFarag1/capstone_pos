<?php

namespace Database\Seeders;

use App\Models\SaleItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateSaleItemSubtotalsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $saleItems = SaleItem::all();
        $count = 0;

        foreach ($saleItems as $saleItem) {
            // Calculate subtotal as price * quantity
            $subtotal = $saleItem->price * $saleItem->quantity;

            // Update the record
            $saleItem->subtotal = $subtotal;
            $saleItem->save();

            $count++;
        }

        Log::info("Updated subtotals for {$count} sale items");
        $this->command->info("Updated subtotals for {$count} sale items");
    }
}
