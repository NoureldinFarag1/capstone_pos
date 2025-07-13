<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\Response;

class DemoItemsCSVExport implements FromArray, WithHeadings
{
    public function array(): array
    {
        return [
            [
                'T-Shirt Premium',
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
                'Premium cotton t-shirt with breathable fabric'
            ],
            [
                'T-Shirt Premium',
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
                'Premium cotton t-shirt with breathable fabric'
            ],
            [
                'T-Shirt Premium',
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
                'Premium cotton t-shirt with breathable fabric'
            ],
            [
                'T-Shirt Premium',
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
                'Premium cotton t-shirt with breathable fabric'
            ],
            [
                'T-Shirt Premium',
                'Nike',
                'Clothing',
                'Medium',
                'Blue',
                '#0000FF',
                '22',
                '15.00',
                '30.00',
                '14',
                'percentage',
                '10',
                'Premium cotton t-shirt with breathable fabric'
            ],
            [
                'Jeans Slim Fit',
                'Levi\'s',
                'Clothing',
                '30',
                'Dark Blue',
                '#000080',
                '8',
                '40.00',
                '80.00',
                '14',
                'fixed',
                '5',
                'Premium denim jeans with perfect fit'
            ],
            [
                'Jeans Slim Fit',
                'Levi\'s',
                'Clothing',
                '32',
                'Dark Blue',
                '#000080',
                '12',
                '40.00',
                '80.00',
                '14',
                'fixed',
                '5',
                'Premium denim jeans with perfect fit'
            ],
            [
                'Running Shoes Air',
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
                'Professional running shoes with air cushioning'
            ],
            [
                'Running Shoes Air',
                'Adidas',
                'Footwear',
                '43',
                'Black',
                '#000000',
                '6',
                '60.00',
                '120.00',
                '14',
                'percentage',
                '15',
                'Professional running shoes with air cushioning'
            ],
            [
                'Running Shoes Air',
                'Adidas',
                'Footwear',
                '44',
                'White',
                '#FFFFFF',
                '10',
                '60.00',
                '120.00',
                '14',
                'percentage',
                '15',
                'Professional running shoes with air cushioning'
            ],
            [
                'Coffee Mug Ceramic',
                'HomeStyle',
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
                'High-quality ceramic coffee mug'
            ],
            [
                'Coffee Mug Ceramic',
                'HomeStyle',
                'Home & Kitchen',
                'N/A',
                'Black',
                '#000000',
                '35',
                '3.00',
                '8.00',
                '14',
                'percentage',
                '0',
                'High-quality ceramic coffee mug'
            ],
            [
                'Wireless Headphones',
                'TechSound',
                'Electronics',
                'N/A',
                'Black',
                '#000000',
                '15',
                '45.00',
                '89.99',
                '14',
                'percentage',
                '20',
                'Premium wireless headphones with noise cancellation'
            ],
            [
                'Yoga Mat Premium',
                'FitLife',
                'Sports',
                'N/A',
                'Purple',
                '#800080',
                '20',
                '12.00',
                '25.00',
                '14',
                'fixed',
                '3',
                'Non-slip yoga mat for professional practice'
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

    public static function downloadCSV()
    {
        $export = new self();
        $csvData = implode(',', $export->headings()) . "\n";

        foreach ($export->array() as $row) {
            $csvData .= '"' . implode('","', $row) . '"' . "\n";
        }

        return Response::make($csvData, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="demo_items_import.csv"'
        ]);
    }
}
