<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Carbon\Carbon;

class RefundsReportExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping, WithTitle
{
    protected $reportData;

    public function __construct(array $reportData)
    {
        $this->reportData = $reportData;
    }

    public function collection()
    {
        return collect($this->reportData['refunds']);
    }

    public function headings(): array
    {
        return [
            'Transaction ID',
            'Original Sale Date',
            'First Refund Date',
            'Last Refund Date',
            'Refund Status',
            'Original Amount',
            'Total Refunded Amount',
            'Refunded Items Detail',
            'Payment Method',
            'Processed By',
            'Remaining Balance',
            'Refund Reason'
        ];
    }

    public function map($sale): array
    {
        // Get refunded items details from the refunds relationship
        $refundedItems = $sale->refunds->map(function($refund) {
            return sprintf(
                '%s (x%d) - EGP %s - %s (Refunded on: %s)',
                $refund->item->name,
                $refund->quantity_refunded,
                number_format($refund->refund_amount, 2),
                $refund->reason ?: 'No reason provided',
                $refund->created_at->format('Y-m-d H:i')
            );
        })->implode("\n");

        $totalRefunded = $sale->refunds->sum('refund_amount');
        $remainingBalance = $sale->total_amount - $totalRefunded;

        // Get first and last refund dates
        $firstRefund = $sale->refunds->min('created_at');
        $lastRefund = $sale->refunds->max('created_at');

        return [
            $sale->id,
            $sale->created_at->format('Y-m-d H:i'),
            $firstRefund ? Carbon::parse($firstRefund)->format('Y-m-d H:i') : 'N/A',
            $lastRefund ? Carbon::parse($lastRefund)->format('Y-m-d H:i') : 'N/A',
            ucfirst(str_replace('_', ' ', $sale->refund_status)),
            number_format($sale->total_amount, 2),
            number_format($totalRefunded, 2),
            $refundedItems ?: 'No items refunded',
            $sale->payment_method,
            $sale->user->name,
            number_format($remainingBalance, 2),
            $sale->refunds->pluck('reason')->filter()->implode(', ') ?: 'No reason provided'
        ];
    }

    public function title(): string
    {
        return 'Refunds Report - ' . $this->reportData['date'];
    }
}
