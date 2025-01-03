<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - Local HUB</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 40px;
            background-color: #f8f9fa;
        }
        .invoice-container {
            background-color: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 2px solid #eee;
        }
        .company-name {
            font-size: 28px;
            color: #2c3e50;
            margin: 0;
            font-weight: 700;
        }
        .invoice-number {
            color: #7f8c8d;
            margin: 10px 0;
            font-size: 18px;
        }
        .invoice-date {
            color: #666;
            margin-bottom: 30px;
        }
        .table-container {
            margin-top: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background-color: #f8f9fa;
            color: #2c3e50;
            padding: 15px;
            text-align: left;
            border-bottom: 2px solid #ddd;
        }
        td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            color: #444;
        }
        .quantity {
            text-align: center;
        }
        .price {
            text-align: right;
        }
        .subtotal-row td {
            padding-top: 20px;
            border-bottom: none;
        }
        .discount-row td {
            padding-top: 10px;
            border-bottom: none;
            color: #e74c3c;
        }
        .total-row td {
            padding-top: 10px;
            font-weight: 700;
            font-size: 18px;
            color: #2c3e50;
            border-top: 2px solid #eee;
        }
        .text-right {
            text-align: right;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            color: #7f8c8d;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="header">
            <h1 class="company-name">LOCAL HUB</h1>
            <h2 class="invoice-number">Invoice #<?php echo e($sale->id); ?></h2>
            <p class="invoice-date">Date: <?php echo e($sale->created_at->format('F d, Y H:i')); ?></p>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Item</th>
                        <th class="quantity">Quantity</th>
                        <th class="price">Price</th>
                        <th class="price">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $sale->saleItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $saleItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($saleItem->item->name); ?></td>
                        <td class="quantity"><?php echo e($saleItem->quantity); ?></td>
                        <td class="price">$<?php echo e(number_format($saleItem->price, 2)); ?></td>
                        <td class="price">$<?php echo e(number_format($saleItem->quantity * $saleItem->price, 2)); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
                <tfoot>
                    <tr class="subtotal-row">
                        <td colspan="3" class="text-right">Subtotal:</td>
                        <td class="price">$<?php echo e(number_format($sale->total_amount + ($sale->discount), 2)); ?></td>
                    </tr>
                    <tr class="discount-row">
                        <td colspan="3" class="text-right">Discount:</td>
                        <td class="price">-$<?php echo e(number_format($sale->discount ?? 0, 2)); ?></td>
                    </tr>
                    <tr class="total-row">
                        <td colspan="3" class="text-right">Total Amount:</td>
                        <td class="price">$<?php echo e(number_format($sale->total_amount, 2)); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="footer">
            <p>Thank you!</p>
        </div>
    </div>
</body>
</html>
<?php /**PATH /Users/noureldinfarag/capstone_pos/resources/views/invoices/invoice.blade.php ENDPATH**/ ?>