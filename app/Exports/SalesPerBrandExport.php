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
        $brands = Brand::all();
        $data = new Collection();

        foreach ($brands as $brand) {
            $totalSales = SaleItem::whereHas('item', function ($query) use ($brand) {
                $query->where('brand_id', $brand->id);
            })->sum('price');

            $totalQuantity = SaleItem::whereHas('item', function ($query) use ($brand) {
                $query->where('brand_id', $brand->id);
            })->sum('quantity');

            $salesCount = SaleItem::whereHas('item', function ($query) use ($brand) {
                $query->where('brand_id', $brand->id);
            })->count();

            $averagePrice = $totalQuantity > 0 ? $totalSales / $totalQuantity : 0;

            $lastSaleDate = SaleItem::whereHas('item', function ($query) use ($brand) {
                $query->where('brand_id', $brand->id);
            })->latest('created_at')->value('created_at');

            $data->push([
                'Brand' => $brand->name,
                'Total Sales' => $totalSales,
                'Total Quantity' => $totalQuantity,
                'Sales Count' => $salesCount,
                'Average Price' => number_format($averagePrice, 2),
                'Last Sale Date' => $lastSaleDate ? $lastSaleDate->format('Y-m-d') : 'N/A',
            ]);
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            'Brand',
            'Total Sales',
            'Total Quantity',
            'Sales Count',
            'Average Price',
            'Last Sale Date',
        ];
    }
}
