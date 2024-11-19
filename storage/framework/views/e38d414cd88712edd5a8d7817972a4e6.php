<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row">
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-body">
                    <img src="<?php echo e(asset('storage/' . $item->picture)); ?>" alt="<?php echo e($item->name); ?>" class="card-img-top img-fluid item-image">
                    <h5 class="card-title"><?php echo e($item->name); ?></h5>
                    <?php if($item->barcode): ?>
                        <h3>Barcode:</h3>
                        <img src="<?php echo e(asset('storage/' .$item->barcode)); ?>">
                    <?php else: ?>
                        <p>No barcode available.</p>
                    <?php endif; ?>  
                    <p class="card-text"><strong>Brand:</strong> <?php echo e($item->brand->name); ?></p>
                    <p class="card-text"><strong>Category:</strong> <?php echo e($item->category->name); ?></p>
                    <p class="card-text"><strong>Price:</strong> $<?php echo e($item->selling_price); ?></p>
                    <p class="card-text"><strong>Total Sale:</strong> <?php echo e($item->applied_sale); ?>%</p>
                    <p class="card-text"><strong>Quantity:</strong> <?php echo e($item->quantity); ?></p>                  
                </div>
            </div>
        </div>
    </div>
    <a href="<?php echo e(route('items.index')); ?>" class="btn btn-secondary">Back to Items</a>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/noureldinfarag/capstone_pos/resources/views/items/show.blade.php ENDPATH**/ ?>