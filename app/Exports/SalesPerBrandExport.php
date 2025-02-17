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
        return Brand::all()->map(function ($brand) {
            $sales = SaleItem::whereHas('item', function ($query) use ($brand) {
                $query->where('brand_id', $brand->id);
            });

            $totalSales = $sales->sum('price');
            $totalQuantity = $sales->sum('quantity');
            $salesCount = $sales->count();
            $averagePrice = $totalQuantity > 0 ? $totalSales / $totalQuantity : 0;
            $lastSaleDate = $sales->latest('created_at')->value('created_at');

            return [
                'Brand' => $brand->name,
                'Total Sales' => $totalSales,
                'Total Quantity' => $totalQuantity,
                'Sales Count' => $salesCount,
                'Average Price' => number_format($averagePrice, 2),
                'Last Sale Date' => $lastSaleDate ? $lastSaleDate->format('Y-m-d') : 'N/A',
            ];
        });
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
