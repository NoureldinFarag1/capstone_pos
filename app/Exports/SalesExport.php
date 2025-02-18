<?php

namespace App\Exports;

use App\Models\Brand;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class SalesExport implements WithMultipleSheets
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

    public function sheets(): array
    {
        $sheets = [];

        // If a brand is selected, export only the SalesReportSheet
        if ($this->brandId) {
            $sheets[] = new SalesReportSheet($this->startDate, $this->endDate, $this->brandId);
        } else {
            // Otherwise, export both sheets
            $sheets[] = new SalesReportSheet($this->startDate, $this->endDate, $this->brandId);
            $sheets[] = new TransactionDataSheet($this->startDate, $this->endDate);
        }

        return $sheets;
    }
}
