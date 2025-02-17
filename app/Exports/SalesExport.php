<?php

namespace App\Exports;

use App\Models\Brand;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalesExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles, ShouldAutoSize
{
    protected $startDate;
    protected $endDate;
    protected $brandId;

    public function __construct($startDate, $endDate, $brandId = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->brandId = $brandId;
    }

    public function collection()
    {
        $query = DB::table('sale_items')
            ->join('items', 'sale_items.item_id', '=', 'items.id')
            ->join('brands', 'items.brand_id', '=', 'brands.id')
            ->whereBetween('sale_items.created_at', [
                $this->startDate . ' 00:00:00',
                $this->endDate . ' 23:59:59'
            ])
            ->select(
                'brands.name as brand',
                'items.name as item',
                'sale_items.quantity as quantity_sold',
                'items.quantity as stock_quantity',
                'sale_items.price as sale_price',
                DB::raw('(sale_items.quantity * sale_items.price) as line_total')
            );

        if ($this->brandId) {
            $query->where('items.brand_id', $this->brandId);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Brand',
            'Item',
            'Quantity Sold',
            'Current Stock',
            'Price',
            'Total'
        ];
    }

    public function map($row): array
    {
        return [
            $row->brand,
            $row->item,
            $row->quantity_sold,
            $row->stock_quantity,
            $row->sale_price,
            $row->line_total
        ];
    }

    public function title(): string
    {
        return 'Sales Report';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            'A1:F1' => ['fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E2E8F0']
            ]],
        ];
    }
}
