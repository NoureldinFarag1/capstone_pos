<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Item Labels Sheet</title>
    <style>
        @page {
            size: A4;
            margin: 5mm;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        .labels-container {
            display: flex;
            flex-wrap: wrap;
            gap: 2mm;
            justify-content: flex-start;
        }

        .label {
            width: 36.5mm;
            height: 25mm;
            border: 1px solid #ccc;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            page-break-inside: avoid;
            position: relative;
            overflow: hidden;
        }

        .item-name {
            font-weight: bold;
            font-size: 9px;
            margin: 0;
            padding-bottom: 2px;
            max-height: 14px;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            line-height: 1;
            word-wrap: break-word;
        }

        .barcode {
            width: 30mm;
            height: 8.8mm;
            object-fit: cover;
            margin: 0;
        }

        .item-code {
            font-size: 8px;
            margin: 0;
            padding-top: 1px;
        }

        .pricing {
            font-size: 9px;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 1.3mm;
            padding-top: 2px;
            margin-top: 1px;
        }

        .original-price {
            font-size: 8px;
            text-decoration: line-through;
            color: black;
        }

        .sale-price {
            font-size: 11px;
            color: black;
        }

        .brand-name {
            font-size: 7px;
            margin: 0;
            padding-top: 1px;
        }

        /* Page break control */
        .page-break {
            page-break-before: always;
        }
    </style>
</head>

<body>
    <div class="labels-container">
        @foreach ($labels as $index => $labelData)
            @if ($index > 0 && $index % 63 == 0)
                <!-- Force page break after approximately 63 labels (9 columns × 7 rows) -->
                </div>
                <div class="page-break"></div>
                <div class="labels-container">
            @endif

            <div class="label">
                <img class="barcode" src="{{ $labelData['barcodePath'] }}" alt="Barcode">
                <div class="item-code">{{ $labelData['item']->code }}</div>
                <div class="brand-name">{{ $labelData['item']->brand->name }}</div>
                <div class="item-name">{{ $labelData['item']->name }}</div>
                <div class="pricing">
                    @if ($labelData['item']->discount_value > 0)
                        <span class="original-price">EGP {{ number_format($labelData['item']->selling_price, 2) }}</span>
                        <span class="sale-price">EGP {{ number_format($labelData['item']->priceAfterSale(), 2) }}</span>
                    @else
                        <span>EGP {{ number_format($labelData['item']->selling_price, 2) }}</span>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</body>

</html>
