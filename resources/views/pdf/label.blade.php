<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Item Label</title>
    <style>
        @page {
            size: 1.4409in 0.9843in; /* Set to the exact label size (36.5mm x 25mm) */
            margin: 0;
        }
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100%; /* Ensure body fills the page */
            width: 100%;  /* Ensure body fills the page */
        }
        .item-name {
            font-weight: bold;
            font-size: 8px; /* Increased font size */
            margin: 0;
            padding-bottom: 2px; /* Space between name and barcode */
        }
        .barcode {
            width: 1.2in;   /* Adjusted barcode width */
            height: 0.4in;  /* Adjusted barcode height */
            object-fit: cover; /* Adjust to cover and avoid empty spaces */
            margin: 0;
        }
        .item-code {
            font-size: 8px; /* Increased font size for item code */
            margin: 0;
            padding-top: 2px; /* Space between barcode and item code */
        }
        .pricing {
            font-size: 10px; /* Increased font size for pricing */
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.05in; /* Space between prices */
            padding-top: 2px; /* Space between item code and pricing */
        }
        .original-price {
            font-size: 10px;
            text-decoration: line-through;
            color: black;
        }
        .sale-price {
            font-size: 10px;
            color: black; /* Sale price color */
        }
    </style>
</head>
<body>
    <div>
        <img class="barcode" src="{{ $barcodePath }}" alt="Barcode">
        <div class="item-code">{{ $item->code }}</div>
        <div class="item-name">{{ $item->name }}</div>
        <div class="pricing">
            @if ($item->discount_value > 0)
                <span class="original-price">EGP {{ number_format($item->selling_price, 2) }}</span>
                <span class="sale-price">EGP {{ number_format($item->priceAfterSale(), 2) }}</span>
            @else
                <span>${{ number_format($item->selling_price, 2) }}</span>
            @endif
        </div>
    </div>
</body>
</html>
