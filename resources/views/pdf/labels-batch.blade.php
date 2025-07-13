<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Item Labels Batch</title>
    <style>
        @page {
            size: 36.5mm 25mm;
            margin: 0;
            padding: 0;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            text-align: center;
            width: 100%;
            height: 100%;
        }

        .label {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            page-break-after: always;
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

        /* Last label in the document shouldn't have a page break */
        .label:last-child {
            page-break-after: avoid;
        }
    </style>
</head>

<body>
    @foreach ($labels as $labelData)
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
</body>

</html>
