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
            font-size: 7px; /* Slightly reduced font size */
            margin: 0;
            padding-bottom: 2px; /* Space between name and barcode */
            max-height: 14px; /* Allow for 2 lines of text */
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2; /* Limit to 2 lines */
            -webkit-box-orient: vertical;
            line-height: 1;
            word-wrap: break-word;
        }
        .barcode {
            width: 1.2in;   /* Adjusted barcode width */
            height: 0.35in;  /* Slightly reduced height */
            object-fit: cover; /* Adjust to cover and avoid empty spaces */
            margin: 0;
        }
        .item-code {
            font-size: 7px; /* Slightly reduced */
            margin: 0;
            padding-top: 1px; /* Space between barcode and item code */
        }
        .pricing {
            font-size: 9px; /* Slightly reduced */
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.05in; /* Space between prices */
            padding-top: 2px; /* Space between item code and pricing */
            margin-top: 1px;
        }
        .original-price {
            font-size: 9px;
            text-decoration: line-through;
            color: black;
        }
        .sale-price {
            font-size: 9px;
            color: black; /* Sale price color */
        }
        .brand-name {
            font-size: 7px; /* Slightly reduced */
            margin: 0;
            padding-top: 1px; /* Space between brand name and item name */
        }
    </style>
</head>
<body>
    <div>
        <img class="barcode" src="<?php echo e($barcodePath); ?>" alt="Barcode">
        <div class="item-code"><?php echo e($item->code); ?></div>
        <div class="brand-name"><?php echo e($item->brand->name); ?></div>
        <div class="item-name"><?php echo e($item->name); ?></div>
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
<?php /**PATH E:\LocalHub POS\capstone_pos\resources\views/pdf/label.blade.php ENDPATH**/ ?>