<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;

class SalesReportSheet implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles, ShouldAutoSize, WithEvents
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
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id') // Added join to fix sales.shipping_fees column error
            ->join('brands', 'items.brand_id', '=', 'brands.id')
            ->whereBetween('sale_items.created_at', [
                $this->startDate . ' 00:00:00',
                $this->endDate . ' 23:59:59'
            ])
            ->select(
                'brands.name as brand',
                'items.name as item',
                DB::raw('SUM(sale_items.quantity) as quantity_sold'),
                'items.quantity as stock_quantity',
                'sale_items.price as sale_price',
                DB::raw('SUM(sale_items.quantity * sale_items.price) as line_total'),
                DB::raw('SUM(sales.shipping_fees) as shipping_total') // Add shipping total
            )
            ->groupBy('brands.name', 'items.name', 'items.quantity', 'sale_items.price');

        if ($this->brandId) {
            $query->where('items.brand_id', $this->brandId);
        }

        $salesData = $query->get();

        // Prepend custom heading row and an empty row for separation
        $salesData->prepend((object) [
            'brand' => 'Brand Name',
            'item' => 'Item Name',
            'quantity_sold' => 'Quantity Sold',
            'stock_quantity' => 'Stock Quantity',
            'sale_price' => 'Sale Price',
            'shipping_total' => 'Shipping', // Add shipping heading
            'line_total' => 'Total'
        ]);

        $salesData->prepend((object) [
            'brand' => "SALES REPORT FROM {$this->startDate} TO {$this->endDate}",
            'item' => '',
            'quantity_sold' => '',
            'stock_quantity' => '',
            'sale_price' => '',
            'shipping_total' => '', // Add empty shipping row
            'line_total' => ''
        ]);

        return $salesData;
    }

    public function headings(): array
    {
        return [
            'Brand',
            'Item',
            'Quantity Sold',
            'Current Stock',
            'Price',
            'Shipping', // Add shipping heading
            'Total'
        ];
    }

    public function map($row): array
    {
        return [
            $row->brand,
            $row->item,
            $row->quantity_sold,
            ($row->stock_quantity === 0 ? 0 : $row->stock_quantity), // Ensuring 0 is shown as 0
            $row->sale_price,
            $row->shipping_total, // Add shipping total
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
            1 => ['font' => ['bold' => true, 'size' => 16]], // Style for custom heading row
            2 => ['font' => ['bold' => true, 'size' => 12]], // Style for date row
            3 => ['font' => ['bold' => true]], // Style for column headings
            'A2:G2' => [ // Adjust range to include new column
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E2E8F0']
                ]
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();
                $totalRow = $lastRow + 1;

                // Add report title
                $sheet->setCellValue('A1', 'Sales Report');
                $sheet->setCellValue('A2', "Date: {$this->startDate} to {$this->endDate}");

                // Merge cells for header
                $sheet->mergeCells('A1:G1'); // Adjust range to include new column
                $sheet->mergeCells('A2:G2'); // Adjust range to include new column

                // Center align headers
                $sheet->getStyle('A1:G2')->getAlignment()->setHorizontal('center'); // Adjust range to include new column

                // Add totals row
                $sheet->setCellValue("A{$totalRow}", 'TOTAL');
                $sheet->setCellValue("C{$totalRow}", "=SUM(C4:C{$lastRow})");
                $sheet->setCellValue("F{$totalRow}", "=SUM(F4:F{$lastRow})");
                $sheet->setCellValue("G{$totalRow}", "=SUM(G4:G{$lastRow})"); // Add shipping total

                // Style totals row
                $sheet->getStyle("A{$totalRow}:G{$totalRow}")->applyFromArray([ // Adjust range to include new column
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F3F4F6']
                    ],
                ]);

                // Format numbers in totals row
                $sheet->getStyle("C{$totalRow}")->getNumberFormat()->setFormatCode('#,##0');
                $sheet->getStyle("F{$totalRow}")->getNumberFormat()->setFormatCode('#,##0.00');
                $sheet->getStyle("G{$totalRow}")->getNumberFormat()->setFormatCode('#,##0.00'); // Format shipping total

                // Add borders to the entire table
                $sheet->getStyle('A3:G' . $totalRow)->applyFromArray([ // Adjust range to include new column
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ]);
            },
        ];
    }
}
