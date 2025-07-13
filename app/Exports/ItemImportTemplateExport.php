<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ItemImportTemplateExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'Template' => new TemplateSheet(),
            'Instructions' => new InstructionsSheet(),
            'Examples' => new ExamplesSheet(),
        ];
    }
}

class TemplateSheet implements FromArray, WithHeadings, WithStyles, ShouldAutoSize
{
    public function array(): array
    {
        return [
            // Empty rows for user input - they can add as many as needed
        ];
    }

    public function headings(): array
    {
        return [
            'item_name',
            'brand',
            'category',
            'size',
            'color',
            'color_code',
            'quantity',
            'buying_price',
            'selling_price',
            'tax',
            'discount_type',
            'discount_value',
            'description'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4'],
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ],
        ];
    }
}

class InstructionsSheet implements FromArray, WithHeadings, WithStyles, ShouldAutoSize
{
    public function array(): array
    {
        return [
            ['Field Name', 'Required', 'Description', 'Example'],
            ['item_name', 'Yes', 'Name of the item/product', 'T-Shirt Classic'],
            ['brand', 'Yes', 'Brand name (will be created if not exists)', 'Nike'],
            ['category', 'Yes', 'Category name (will be created if not exists)', 'Clothing'],
            ['size', 'No', 'Size variant (leave empty or N/A for no size)', 'Medium'],
            ['color', 'No', 'Color variant (leave empty or N/A for no color)', 'Red'],
            ['color_code', 'No', 'Hex color code for the color', '#FF0000'],
            ['quantity', 'Yes', 'Stock quantity for this variant', '50'],
            ['buying_price', 'Yes', 'Cost price per unit', '15.00'],
            ['selling_price', 'Yes', 'Selling price per unit', '25.00'],
            ['tax', 'No', 'Tax percentage (default: 0)', '14'],
            ['discount_type', 'No', 'Type of discount: percentage or fixed', 'percentage'],
            ['discount_value', 'No', 'Discount value', '10'],
            ['description', 'No', 'Product description', 'Comfortable cotton t-shirt'],
            [],
            ['IMPORTANT NOTES:'],
            ['• One row per variant (size/color combination)'],
            ['• Items with same name, brand, and category will be grouped as variants'],
            ['• If size or color is not specified, use "N/A" or leave empty'],
            ['• Color codes should be in hex format (#RRGGBB)'],
            ['• Discount type can be "percentage" or "fixed"'],
            ['• Tax is in percentage (e.g., 14 for 14%)'],
            ['• All prices should be numeric values'],
            ['• Brands and categories will be created automatically if they don\'t exist'],
        ];
    }

    public function headings(): array
    {
        return [
            'BULK IMPORT INSTRUCTIONS'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 16,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '2E7D32'],
                ],
            ],
            2 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E8F5E8'],
                ],
            ],
            17 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'D32F2F'],
                ],
            ],
        ];
    }
}

class ExamplesSheet implements FromArray, WithHeadings, WithStyles, ShouldAutoSize
{
    public function array(): array
    {
        return [
            [
                'T-Shirt Classic',
                'Nike',
                'Clothing',
                'Small',
                'Red',
                '#FF0000',
                '25',
                '15.00',
                '30.00',
                '14',
                'percentage',
                '10',
                'Comfortable cotton t-shirt'
            ],
            [
                'T-Shirt Classic',
                'Nike',
                'Clothing',
                'Medium',
                'Red',
                '#FF0000',
                '30',
                '15.00',
                '30.00',
                '14',
                'percentage',
                '10',
                'Comfortable cotton t-shirt'
            ],
            [
                'T-Shirt Classic',
                'Nike',
                'Clothing',
                'Large',
                'Red',
                '#FF0000',
                '20',
                '15.00',
                '30.00',
                '14',
                'percentage',
                '10',
                'Comfortable cotton t-shirt'
            ],
            [
                'T-Shirt Classic',
                'Nike',
                'Clothing',
                'Small',
                'Blue',
                '#0000FF',
                '15',
                '15.00',
                '30.00',
                '14',
                'percentage',
                '10',
                'Comfortable cotton t-shirt'
            ],
            [
                'Jeans Slim Fit',
                'Levi\'s',
                'Clothing',
                '32',
                'Dark Blue',
                '#000080',
                '10',
                '40.00',
                '80.00',
                '14',
                'fixed',
                '5',
                'Premium denim jeans'
            ],
            [
                'Running Shoes',
                'Adidas',
                'Footwear',
                '42',
                'Black',
                '#000000',
                '8',
                '60.00',
                '120.00',
                '14',
                'percentage',
                '15',
                'Professional running shoes'
            ],
            [
                'Coffee Mug',
                'Generic',
                'Home & Kitchen',
                'N/A',
                'White',
                '#FFFFFF',
                '50',
                '3.00',
                '8.00',
                '14',
                'percentage',
                '0',
                'Ceramic coffee mug'
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'item_name',
            'brand',
            'category',
            'size',
            'color',
            'color_code',
            'quantity',
            'buying_price',
            'selling_price',
            'tax',
            'discount_type',
            'discount_value',
            'description'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4'],
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ],
        ];
    }
}
