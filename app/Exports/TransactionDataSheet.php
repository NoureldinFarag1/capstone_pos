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

class TransactionDataSheet implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles, ShouldAutoSize, WithEvents
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        return DB::table('sales')
            ->join('users', 'sales.user_id', '=', 'users.id')
            ->select(
                'sales.id',
                'sales.total_amount',
                'sales.discount_type',
                'sales.discount_value',
                'sales.created_at',
                'users.name as issued_by',
                'sales.payment_method',
                'sales.shipping_fees'
            )
            ->whereBetween('sales.created_at', [
                $this->startDate . ' 00:00:00',
                $this->endDate . ' 23:59:59'
            ])
            ->get();
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
            'Sale Items'
        ];
    }

    public function map($transaction): array
    {
        $saleItems = DB::table('sale_items')
            ->join('items', 'sale_items.item_id', '=', 'items.id')
            ->where('sale_items.sale_id', $transaction->id)
            ->select('items.name', 'sale_items.quantity', 'sale_items.price')
            ->get()
            ->map(function ($item) {
                return "{$item->name} ({$item->quantity} @ {$item->price})";
            })
            ->implode(', ');

        return [
            $transaction->id,
            $transaction->total_amount,
            $transaction->discount_type ?? 'None',
            $transaction->discount_value ?? 0,
            \Carbon\Carbon::parse($transaction->created_at)->format('H:i'),
            $transaction->issued_by,
            $transaction->payment_method,
            $transaction->shipping_fees ?? 0,
            $saleItems
        ];
    }

    public function title(): string
    {
        return 'Transaction Data';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 16]], // Style for custom heading row
            2 => ['font' => ['bold' => true, 'size' => 12]], // Style for date row
            3 => ['font' => ['bold' => true]], // Style for column headings
            'A2:I2' => ['fill' => [
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

                // Add report title
                $sheet->setCellValue('A1', 'Transactions Data');
                $sheet->setCellValue('A2', "Date: {$this->startDate} to {$this->endDate}");

                // Merge cells for header
                $sheet->mergeCells('A1:I1');
                $sheet->mergeCells('A2:I2');

                // Center align headers
                $sheet->getStyle('A1:I2')->getAlignment()->setHorizontal('center');

                // Style the header row
                $sheet->getStyle('A2:I2')->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E2E8F0']
                    ],
                ]);

                // Add borders to the entire table
                $sheet->getStyle('A2:I' . $lastRow)->applyFromArray([
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
