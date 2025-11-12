<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ItemsExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $items;

    public function __construct($items)
    {
        $this->items = $items;
    }

    public function collection()
    {
        return $this->items->map(function ($item) {
            return [
                'ID' => $item->id,
                'Name' => $item->name,
                'Brand' => $item->brand->name,
                'Category' => $item->category->name,
                'Stock' => $item->quantity,
                'Regular Price' => $item->selling_price,
                'Sale Price' => $item->priceAfterSale(),
                'Total Value' => $item->quantity * $item->priceAfterSale(),
                'Updated By' => optional($item->updatedBy)->name,
                'Updated At' => optional($item->updated_at)->toDateTimeString(),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Brand',
            'Category',
            'Stock',
            'Regular Price',
            'Sale Price',
            'Total Value',
            'Updated By',
            'Updated At'
        ];
    }
}

