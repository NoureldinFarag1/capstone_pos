<?php $__env->startSection('content'); ?>
<div class="row mb-4">
    <div class="col-md-12">
        <h1>Brands</h1>
        <a href="<?php echo e(route('brands.create')); ?>" class="btn btn-primary">Add New Brand</a>
    </div>
</div>

<div class="container">
    <div class="row">
        <?php $__currentLoopData = $brands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-md-4">
                <div class="card mb-4">
                    <img src="<?php echo e(asset('storage/' . $brand->picture)); ?>" alt="<?php echo e($brand->name); ?>" class="card-img-top">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo e($brand->name); ?></h5>
                        <a href="<?php echo e(route('brands.edit', $brand->id)); ?>" class="btn btn-primary">Edit</a>
                        <form action="<?php echo e(route('brands.destroy', $brand->id)); ?>" method="POST" onsubmit="return confirm('Are you sure?');" style="display:inline;">
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

<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/noureldinfarag/capstone_pos/resources/views/brands/index.blade.php ENDPATH**/ ?>