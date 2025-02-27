<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3 border-bottom">
                    <h2 class="mb-0">Sale Details</h2>
                </div>

                <div class="card-body p-4">
                    <?php if(session('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo e(session('success')); ?>

                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <div class="table-responsive mb-4">
                        <h3 class="card-title text-secondary mb-3">Items Sold</h3>
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="px-4">Item Name</th>
                                    <th class="text-center">Quantity</th>
                                    <th class="text-end">Price</th>
                                    <th class="text-end px-4">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $sale->saleItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $saleItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td class="px-4"><?php echo e($saleItem->item->brand->name ?? 'No Brand'); ?> - <?php echo e($saleItem->item->name); ?></td>
                                        <td class="text-center"><?php echo e($saleItem->quantity); ?></td>
                                        <td class="text-end">EGP <?php echo e(number_format($saleItem->price, 2)); ?></td>
                                        <td class="text-end px-4">EGP <?php echo e(number_format($saleItem->price * $saleItem->quantity, 2)); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="row justify-content-end mb-4">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body p-3">
                                    <?php if($sale->shipping_fees): ?>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Shipping Fees:</span>
                                        <span>+ EGP<?php echo e(number_format($sale->shipping_fees, 2)); ?></span>
                                    </div>
                                    <?php endif; ?>
                                    <?php if($sale->address): ?>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Address:</span>
                                        <span><?php echo e($sale->address); ?></span>
                                    </div>
                                    <?php endif; ?>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-danger">Discount:</span>
                                        <span class="text-danger">- EGP<?php echo e(number_format($sale->discount, 2)); ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <h4 class="mb-0">Total Amount:</h4>
                                        <h4 class="mb-0">EGP <?php echo e(number_format($sale->total_amount, 2)); ?></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <a href="<?php echo e(route('sales.index')); ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Sales
                        </a>
                        <div class="btn-group">
                            <form action="<?php echo e(route('sales.thermalReceipt', $sale->id)); ?>" method="POST" class="me-2">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-print me-2"></i>Print Receipt
                                </button>
                            </form>
                            <form action="<?php echo e(route('sales.invoice', $sale->id)); ?>" method="GET">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-file-invoice me-2"></i>Print Invoice
                                </button>
                            </form>
                            <a href="<?php echo e(route('sales.showExchangeForm', $sale->id)); ?>" class="btn btn-warning ms-2">
                                <i class="fas fa-exchange-alt me-2"></i>Exchange Item
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/noureldinfarag/capstone_pos/resources/views/sales/show.blade.php ENDPATH**/ ?>