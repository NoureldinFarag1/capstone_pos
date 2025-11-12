<?php

namespace App\Exports;

use App\Models\Item;
use App\Models\ItemUpdateLog;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class InventoryExport implements WithMultipleSheets
{
    protected $variants;
    protected $parents;
    protected $updates;

    public function __construct($variants, $parents, $updates)
    {
        $this->variants = $variants;
        $this->parents = $parents;
        $this->updates = $updates;
    }

    public function sheets(): array
    {
        return [
            'Inventory' => new class($this->variants) implements FromCollection, WithHeadings, ShouldAutoSize {
                private $items;
                public function __construct($items) { $this->items = $items; }
                public function collection()
                {
                    return $this->items->map(function ($item) {
                        return [
                            'ID' => $item->id,
                            'Name' => $item->name,
                            'Brand' => optional($item->brand)->name,
                            'Category' => optional($item->category)->name,
                            'Stock' => $item->quantity,
                            'Regular Price' => $item->selling_price,
                            'Sale Price' => $item->priceAfterSale(),
                            'Total Value' => $item->quantity * $item->priceAfterSale(),
                            'Updated By' => optional($item->updatedBy)->name,
                            'Updated At' => optional($item->updated_at)->toDateTimeString(),
                        ];
                    });
                }
                public function headings(): array
                {
                    return ['ID','Name','Brand','Category','Stock','Regular Price','Sale Price','Total Value','Updated By','Updated At'];
                }
            },
            'Parent Items' => new class($this->parents) implements FromCollection, WithHeadings, ShouldAutoSize {
                private $items;
                public function __construct($items) { $this->items = $items; }
                public function collection()
                {
                    return $this->items->map(function ($item) {
                        return [
                            'ID' => $item->id,
                            'Name' => $item->name,
                            'Brand' => optional($item->brand)->name,
                            'Category' => optional($item->category)->name,
                            'Total Stock' => $item->quantity,
                            'Base Price' => $item->selling_price,
                            'Discount Type' => $item->discount_type,
                            'Discount Value' => $item->discount_value,
                            'Updated By' => optional($item->updatedBy)->name,
                            'Updated At' => optional($item->updated_at)->toDateTimeString(),
                        ];
                    });
                }
                public function headings(): array
                {
                    return ['ID','Name','Brand','Category','Total Stock','Base Price','Discount Type','Discount Value','Updated By','Updated At'];
                }
            },
            'Updates' => new class($this->updates) implements FromCollection, WithHeadings, ShouldAutoSize {
                private $logs;
                public function __construct($logs) { $this->logs = $logs; }
                public function collection()
                {
                    return $this->logs->map(function ($log) {
                        // Flatten changes
                        $flat = [];
                        foreach ($log->changes as $field => $diff) {
                            $flat[] = [
                                'Log ID' => $log->id,
                                'Item ID' => $log->item_id,
                                'Item Name' => optional($log->item)->name,
                                'Field' => $field,
                                'Old Value' => is_scalar($diff['old'] ?? null) ? ($diff['old'] ?? null) : json_encode($diff['old'] ?? null),
                                'New Value' => is_scalar($diff['new'] ?? null) ? ($diff['new'] ?? null) : json_encode($diff['new'] ?? null),
                                'Updated By' => optional($log->user)->name,
                                'Updated At' => optional($log->created_at)->toDateTimeString(),
                            ];
                        }
                        return collect($flat);
                    })->flatten(1);
                }
                public function headings(): array
                {
                    return ['Log ID','Item ID','Item Name','Field','Old Value','New Value','Updated By','Updated At'];
                }
            },
        ];
    }
}
