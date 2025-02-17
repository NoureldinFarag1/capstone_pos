<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class PaymentMethodReportExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping, WithTitle
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
            'Payment Method',
            'Number of Transactions',
            'Total Amount',
            'Percentage of Total',
        ];
    }

    public function map($sale): array
    {
        $totalSales = $this->reportData['sales']->sum('total_amount');
        $percentage = ($sale->total_amount / $totalSales) * 100;

        return [
            $sale->payment_method,
            $sale->count,
            number_format($sale->total_amount, 2),
            number_format($percentage, 2) . '%',
        ];
    }

    public function title(): string
    {
        return 'Payment Methods - ' . $this->reportData['date'];
    }
}
