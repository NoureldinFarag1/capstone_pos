<?php $__env->startSection('content'); ?>
<div class="container mt-4">
    <h1 class="mb-4">Sales</h1>

    <?php if(session('success')): ?>
        <div class="alert alert-success">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between mb-3">
        <a href="<?php echo e(route('sales.create')); ?>" class="btn btn-primary">Create New Sale</a>
        <a href="<?php echo e(route('sales.export')); ?>" class="btn btn-success">Export Sales</a>
    </div>

    <table class="table table-bordered table-striped">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Total Amount</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $sales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($sale->id); ?></td>
                    <td>$<?php echo e(number_format($sale->total_amount, 2)); ?></td>
                    <td><?php echo e($sale->created_at->format('Y-m-d H:i')); ?></td>
                    <td>
                        <a href="<?php echo e(route('sales.show', $sale->id)); ?>" class="btn btn-info btn-sm">Show</a>
                        <form action="<?php echo e(route('sales.destroy', $sale->id)); ?>" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this sale?');">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/noureldinfarag/capstone_pos/resources/views/sales/index.blade.php ENDPATH**/ ?>