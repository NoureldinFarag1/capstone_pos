<?php

namespace App\Exports;

use App\Models\Brand;
use App\Models\SaleItem;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SalesPerBrandExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        // Get all brands
        $brands = Brand::all();
        $data = new Collection();

        foreach ($brands as $brand) {
            // Get total sales and quantities for the brand through SaleItems
            $totalSales = SaleItem::whereHas('item', function ($query) use ($brand) {
                $query->where('brand_id', $brand->id);
            })->sum('price');

            $totalQuantity = SaleItem::whereHas('item', function ($query) use ($brand) {
                $query->where('brand_id', $brand->id);
            })->sum('quantity');

            // Add brand data to collection
            $data->push([
                'Brand' => $brand->name,
                'Total Sales' => $totalSales,
                'Total Quantity' => $totalQuantity,
            ]);
        }

        return $data;
    }

    // Define headings for the Excel file
    public function headings(): array
    {
        return [
            'Brand',
            'Total Sales',
            'Total Quantity',
        ];
    }
}
