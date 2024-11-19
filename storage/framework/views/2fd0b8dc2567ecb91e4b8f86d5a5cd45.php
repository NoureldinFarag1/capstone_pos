        <?php $__env->startSection('content'); ?>
            <div class="container">
                <h1>Items</h1>
                <a href="<?php echo e(route('items.create')); ?>" class="btn btn-primary     mb-3">Add New Item</a>
                <div class="container">
    <div class="row">
        <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-md-4">
                <div class="card mb-4">
                <a href="<?php echo e(route('items.show', $item->id)); ?>" class="card mb-1 text-decoration-none text-dark">
                    <img src="<?php echo e(asset('storage/' . $item->picture)); ?>" alt="<?php echo e($item->name); ?>" class="card-img-top">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo e($item->name); ?></h5>
                        <p class="card-text">Price: $<?php echo e($item->selling_price); ?></p>
                        <p class="card-text">Sale: <?php echo e($item->applied_sale); ?>%</p>
                        <p class="card-text">Total Amount: $<?php echo e($item->PriceAfterSale()); ?></p>
                        <p class="card-text">Quantity: <?php echo e($item->quantity); ?></p>
                        <a href="<?php echo e(route('items.edit', $item->id)); ?>" class="btn btn-primary">Edit</a>
                        <form action="<?php echo e(route('items.destroy', $item->id)); ?>" method="POST" onsubmit="return confirm('Are you sure?');" style="display:inline;">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/noureldinfarag/capstone_pos/resources/views/items/index.blade.php ENDPATH**/ ?>