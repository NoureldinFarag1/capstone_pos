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

        $salesData = $query->get();

        // Prepend custom heading row and an empty row for separation
        $salesData->prepend((object) [
            'brand' => '',
            'item' => '',
            'quantity_sold' => '',
            'stock_quantity' => '',
            'sale_price' => '',
            'line_total' => ''
        ]);

        $salesData->prepend((object) [
            'brand' => "SALES REPORT FROM {$this->startDate} TO {$this->endDate}",
            'item' => '',
            'quantity_sold' => '',
            'stock_quantity' => '',
            'sale_price' => '',
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
            1 => ['font' => ['bold' => true, 'size' => 16]], // Style for custom heading row
            2 => ['font' => ['bold' => true, 'size' => 12]], // Style for date row
            3 => ['font' => ['bold' => true]], // Style for column headings
            'A2:F2' => ['fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E2E8F0']
            ]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();
                $totalRow = $lastRow + 1;

                // Add report title
                $sheet->setCellValue('A1', 'Sales Report');
                $sheet->setCellValue('A2', "Date: {$this->startDate} to {$this->endDate}");

                // Merge cells for header
                $sheet->mergeCells('A1:F1');
                $sheet->mergeCells('A2:F2');

                // Center align headers
                $sheet->getStyle('A1:F2')->getAlignment()->setHorizontal('center');

                // Add totals row
                $sheet->setCellValue("A{$totalRow}", 'TOTAL');
                $sheet->setCellValue("C{$totalRow}", "=SUM(C4:C{$lastRow})");
                $sheet->setCellValue("F{$totalRow}", "=SUM(F4:F{$lastRow})");

                // Style totals row
                $sheet->getStyle("A{$totalRow}:F{$totalRow}")->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F3F4F6']
                    ],
                ]);

                // Format numbers in totals row
                $sheet->getStyle("C{$totalRow}")->getNumberFormat()->setFormatCode('#,##0');
                $sheet->getStyle("F{$totalRow}")->getNumberFormat()->setFormatCode('#,##0.00');

                // Add borders to the entire table
                $sheet->getStyle('A3:F' . $totalRow)->applyFromArray([
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
