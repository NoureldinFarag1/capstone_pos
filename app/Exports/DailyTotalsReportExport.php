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
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;

class DailyTotalsReportExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles, ShouldAutoSize, WithEvents, WithCustomStartCell
{
    protected string $startDate;
    protected string $endDate;

    public function __construct(string $startDate, string $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        return DB::table('sales')
            ->selectRaw('DATE(sale_date) as date')
            ->selectRaw('COUNT(*) as transactions')
            ->selectRaw('SUM(subtotal) as subtotal_total')
            ->selectRaw('SUM(discount) as discount_total')
            ->selectRaw('SUM(shipping_fees) as shipping_total')
            ->selectRaw('SUM(total_amount) as net_total')
            ->selectRaw("SUM(CASE WHEN discount_type = 'fixed' THEN 1 ELSE 0 END) as fixed_count")
            ->selectRaw("SUM(CASE WHEN discount_type = 'fixed' THEN discount ELSE 0 END) as fixed_discount_total")
            ->selectRaw("SUM(CASE WHEN discount_type = 'percentage' THEN 1 ELSE 0 END) as percentage_count")
            ->selectRaw("SUM(CASE WHEN discount_type = 'percentage' THEN discount ELSE 0 END) as percentage_discount_total")
            ->selectRaw("SUM(CASE WHEN (discount_type IS NULL OR discount_type = 'none' OR discount = 0) THEN 1 ELSE 0 END) as no_discount_count")
            // Payment method totals only (no counts)
            ->selectRaw("SUM(CASE WHEN payment_method = 'cash' THEN total_amount ELSE 0 END) as cash_total")
            ->selectRaw("SUM(CASE WHEN payment_method = 'credit_card' THEN total_amount ELSE 0 END) as credit_card_total")
            ->selectRaw("SUM(CASE WHEN payment_method = 'mobile_pay' THEN total_amount ELSE 0 END) as mobile_pay_total")
            ->selectRaw("SUM(CASE WHEN payment_method = 'cod' THEN total_amount ELSE 0 END) as cod_total")
            ->whereBetween('sale_date', [$this->startDate, $this->endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Date',
            'Transactions Count',
            'Subtotal Amount (EGP)',
            'Discount Amount (EGP)',
            'Shipping Amount (EGP)',
            'Net Amount (EGP)',
            'Fixed Discount Count',
            'Fixed Discount Amount (EGP)',
            'Percentage Discount Count',
            'Percentage Discount Amount (EGP)',
            'No Discount Count',
            // Payment method totals only
            'Cash Amount (EGP)',
            'Credit Card Amount (EGP)',
            'Mobile Pay Amount (EGP)',
            'COD Amount (EGP)',
        ];
    }

    public function map($row): array
    {
        return [
            (string) $row->date,
            (int) $row->transactions,
            (float) ($row->subtotal_total ?? 0),
            (float) ($row->discount_total ?? 0),
            (float) ($row->shipping_total ?? 0),
            (float) ($row->net_total ?? 0),
            (int) $row->fixed_count,
            (float) ($row->fixed_discount_total ?? 0),
            (int) $row->percentage_count,
            (float) ($row->percentage_discount_total ?? 0),
            (int) $row->no_discount_count,
            // Payment method totals only
            (float) ($row->cash_total ?? 0),
            (float) ($row->credit_card_total ?? 0),
            (float) ($row->mobile_pay_total ?? 0),
            (float) ($row->cod_total ?? 0),
        ];
    }

    public function title(): string
    {
        return 'Daily Totals';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 16]],
            2 => ['font' => ['bold' => true, 'size' => 12]],
            3 => ['font' => ['bold' => true]],
            'A3:O3' => [
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

                // Header title and range
                $sheet->setCellValue('A1', 'Daily Totals Report');
                $sheet->setCellValue('A2', "Date: {$this->startDate} to {$this->endDate}");
                $sheet->mergeCells('A1:O1');
                $sheet->mergeCells('A2:O2');
                $sheet->getStyle('A1:O2')->getAlignment()->setHorizontal('center');

                // Totals row
                $sheet->setCellValue("A{$totalRow}", 'TOTAL');
                $sheet->setCellValue("B{$totalRow}", "=SUM(B4:B{$lastRow})");
                $sheet->setCellValue("C{$totalRow}", "=SUM(C4:C{$lastRow})");
                $sheet->setCellValue("D{$totalRow}", "=SUM(D4:D{$lastRow})");
                $sheet->setCellValue("E{$totalRow}", "=SUM(E4:E{$lastRow})");
                $sheet->setCellValue("F{$totalRow}", "=SUM(F4:F{$lastRow})");
                $sheet->setCellValue("G{$totalRow}", "=SUM(G4:G{$lastRow})");
                $sheet->setCellValue("H{$totalRow}", "=SUM(H4:H{$lastRow})");
                $sheet->setCellValue("I{$totalRow}", "=SUM(I4:I{$lastRow})");
                $sheet->setCellValue("J{$totalRow}", "=SUM(J4:J{$lastRow})");
                $sheet->setCellValue("K{$totalRow}", "=SUM(K4:K{$lastRow})");
                // Payment method totals only
                $sheet->setCellValue("L{$totalRow}", "=SUM(L4:L{$lastRow})");
                $sheet->setCellValue("M{$totalRow}", "=SUM(M4:M{$lastRow})");
                $sheet->setCellValue("N{$totalRow}", "=SUM(N4:N{$lastRow})");
                $sheet->setCellValue("O{$totalRow}", "=SUM(O4:O{$lastRow})");

                // Style totals row
                $sheet->getStyle("A{$totalRow}:O{$totalRow}")->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F3F4F6']
                    ],
                ]);

                // Number formatting
                $sheet->getStyle("B4:B{$totalRow}")->getNumberFormat()->setFormatCode('#,##0');
                $sheet->getStyle("C4:F{$totalRow}")->getNumberFormat()->setFormatCode('#,##0.00');
                $sheet->getStyle("H4:H{$totalRow}")->getNumberFormat()->setFormatCode('#,##0.00');
                $sheet->getStyle("J4:J{$totalRow}")->getNumberFormat()->setFormatCode('#,##0.00');
                // Payment method totals format
                $sheet->getStyle("L4:O{$totalRow}")->getNumberFormat()->setFormatCode('#,##0.00');

                // Borders
                $sheet->getStyle('A3:O' . $totalRow)->applyFromArray([
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

    public function startCell(): string
    {
        return 'A3'; // Headings will appear in row 4, after title and date
    }
}
