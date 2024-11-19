<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <link rel="stylesheet" href="<?php echo e(asset('css/invoice.css')); ?>">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        p {
            margin: 10px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        tfoot tr {
            font-weight: bold;
        }
        .total {
            text-align: right;
        }
    </style>
</head>
<body>
    <img src="<?php echo e(asset('images/logo.png')); ?>" alt="Shop Logo" style="max-width: 150px;">
    <h2>local HUB</h2>
    <h2>Invoice #<?php echo e($sale->id); ?></h2>
    <p>Date: <?php echo e($sale->created_at->format('Y-m-d H:i')); ?></p>
    
    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th>Quantity</th>
                <th>Price</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $sale->saleItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $saleItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($saleItem->item->name); ?></td>
                    <td><?php echo e($saleItem->quantity); ?></td>
                    <td>$<?php echo e(number_format($saleItem->price, 2)); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2" class="total">Total:</td>
                <td>$<?php echo e(number_format($sale->total_amount, 2)); ?></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
<?php /**PATH /Users/noureldinfarag/capstone_pos/resources/views/invoices/invoice.blade.php ENDPATH**/ ?>