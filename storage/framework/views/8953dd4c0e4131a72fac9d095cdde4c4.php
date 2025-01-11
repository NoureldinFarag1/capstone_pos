<table>
    <thead>
        <tr>
            <th>Item Name</th>
            <th>Quantity</th>
            <th>Selling Price</th>
            <th>Discount Type</th>
            <th>Discount Value</th>
            <th>Price After Sale</th>
        </tr>
    </thead>
    <tbody>
        <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($item->name); ?></td>
                <td><?php echo e($item->quantity); ?></td>
                <td><?php echo e($item->selling_price); ?></td>
                <td><?php echo e($item->discount_type); ?></td>
                <td><?php echo e($item->discount_value); ?></td>
                <td><?php echo e($item->priceAfterSale()); ?></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
</table>
<?php /**PATH /Users/noureldinfarag/capstone_pos/resources/views/exports/items.blade.php ENDPATH**/ ?>