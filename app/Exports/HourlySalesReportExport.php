<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class HourlySalesReportExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping, WithTitle
{
    protected $reportData;

    public function __construct(array $reportData)
    {
        $this->reportData = $reportData;
    }

    public function collection()
    {
        return collect($this->reportData['sales']);
    }

    public function headings(): array
    {
        return [
            'Hour',
            'Number of Sales',
            'Total Amount',
            'Average Transaction Value',
        ];
    }

    public function map($sale): array
    {
        $timeSlot = sprintf('%02d:00 - %02d:00', $sale->hour, ($sale->hour + 1) % 24);
        $avgTransaction = $sale->count > 0 ? $sale->total_amount / $sale->count : 0;

        return [
            $timeSlot,
            $sale->count,
            number_format($sale->total_amount, 2),
            number_format($avgTransaction, 2),
        ];
    }

    public function title(): string
    {
        return 'Hourly Sales - ' . $this->reportData['date'];
    }
}
