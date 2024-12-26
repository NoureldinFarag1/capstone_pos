<?php $__env->startSection('content'); ?>
<br>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">

                    <h2>Sale Details</h2>
                </div>

                <div class="card-body">
                    <?php if(session('success')): ?>
                        <div class="alert alert-success">
                            <?php echo e(session('success')); ?>

                        </div>
                    <?php endif; ?>

                    <h3 class="card-title">Items Sold</h3>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Item Name</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $sale->saleItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $saleItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($saleItem->item->name); ?></td>
                                    <td><?php echo e($saleItem->quantity); ?></td>
                                    <td>$<?php echo e(number_format($saleItem->price, 2)); ?></td>
                                    <td>$<?php echo e(number_format($saleItem->price * $saleItem->quantity, 2)); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>

                    <h5 class="card-title text-danger">Total Discount: $<?php echo e(number_format($sale->discount, 2)); ?></h5>
                    <h3 class="card-title">Total Amount: $<?php echo e(number_format($sale->total_amount, 2)); ?></h3>

                    <div class="d-flex justify-content-between">
                        <a href="<?php echo e(route('sales.index')); ?>" class="btn btn-warning">Back to Sales</a>
                        <div>
                            <form action="<?php echo e(route('sales.thermalReceipt', $sale->id)); ?>" method="POST" style="display:inline;">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="btn btn-primary">Print Receipt</button>
                            </form>
                            <form action="<?php echo e(route('sales.invoice', $sale->id)); ?>" method="GET" style="display:inline;">
                                <button type="submit" class="btn btn-primary">Print Invoice</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/noureldinfarag/capstone_pos/resources/views/sales/show.blade.php ENDPATH**/ ?>