<?php

namespace App\Imports;

use App\Models\Item;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ItemsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Item([
            'name' => $row['name'],
            'brand_id' => $row['brand_id'], // Adjust based on your database
            'category_id' => $row['category_id'],
            'code' => $row['code'],
            'quantity' => $row['quantity'],
            'buying_price' => $row['buying_price'],
            'selling_price' => $row['selling_price'],
            'sale_price' => $row['applied_sale'],
        ]);
    }
}
