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
        <div class="item-name"><?php echo e($item->name); ?></div>
        <img class="barcode" src="<?php echo e($barcodePath); ?>" alt="Barcode">
        <div class="item-code"><?php echo e($item->code); ?></div>
        <div class="pricing">
            <?php if($item->discount_value > 0): ?>
                <span class="original-price">EGP <?php echo e(number_format($item->selling_price, 2)); ?></span>
                <span class="sale-price">EGP <?php echo e(number_format($item->priceAfterSale(), 2)); ?></span>
            <?php else: ?>
                <span>$<?php echo e(number_format($item->selling_price, 2)); ?></span>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
<?php /**PATH /Users/noureldinfarag/capstone_pos/resources/views/pdf/label.blade.php ENDPATH**/ ?>