<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Style\Border;

class DailySalesExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping, WithCustomStartCell, WithStyles, WithEvents
{
    protected $reportData;
    protected $date;

    public function __construct(array $reportData, $date)
    {
        $this->reportData = $reportData;
        $this->date = $date;
    }

    public function startCell(): string
    {
        return 'A3';
    }

    public function collection()
    {
        return collect($this->reportData['sales']);
    }

    public function headings(): array
    {
        return [
            'Transaction ID',
            'Total Amount (EGP)',
            'Discount Type',
            'Discount Amount',
            'Date',
            'Issued By',
            'Payment Method',
            'Shipping Fees (EGP)',
        ];
    }

    public function map($sale): array
    {
        return [
            $sale->id, // This will be formatted as number
            (float)$sale->total_amount, // Convert to float to ensure Excel treats it as number
            $sale->discount_type ?? 'None',
            $sale->discount_value ? (float)$sale->discount_value : 0, // Convert to float
            $sale->created_at->format('H:i'),
            $sale->user->name,
            $sale->payment_method,
            (float)($sale->shipping_fees ?? 0), // Convert to float
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 16]],
            2 => ['font' => ['bold' => true, 'size' => 12]],
            3 => ['font' => ['bold' => true]],
            'A3:H3' => [
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E2E8F0']
                ],
            ],
            // Format numbers properly
            'A' => ['numberFormat' => ['formatCode' => '#,##0']],
            'B' => ['numberFormat' => ['formatCode' => '#,##0.00']],
            'D' => ['numberFormat' => ['formatCode' => '#,##0.00']],
            'H' => ['numberFormat' => ['formatCode' => '#,##0.00']],
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
                $sheet->setCellValue('A1', 'Daily Sales Report');
                $sheet->setCellValue('A2', 'Date: ' . $this->date);

                // Merge cells for header
                $sheet->mergeCells('A1:H1');
                $sheet->mergeCells('A2:H2');

                // Center align headers
                $sheet->getStyle('A1:H2')->getAlignment()->setHorizontal('center');

                // Add totals row
                $sheet->setCellValue("A{$totalRow}", 'TOTAL');
                $sheet->setCellValue("B{$totalRow}", "=SUM(B4:B{$lastRow})");
                $sheet->setCellValue("H{$totalRow}", "=SUM(H4:H{$lastRow})");

                // Style totals row
                $sheet->getStyle("A{$totalRow}:H{$totalRow}")->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F3F4F6']
                    ],
                ]);

                // Format numbers in totals row
                $sheet->getStyle("B{$totalRow}")->getNumberFormat()->setFormatCode('#,##0.00');
                $sheet->getStyle("H{$totalRow}")->getNumberFormat()->setFormatCode('#,##0.00');

                // Add borders to the entire table
                $sheet->getStyle('A3:H' . $totalRow)->applyFromArray([
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
