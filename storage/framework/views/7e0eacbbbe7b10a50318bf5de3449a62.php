<?php $__env->startSection('content'); ?>
<div class="row mb-4 ms-5">
    <div class="col-md-12 ms-2">
        <h1>Categories</h1>
        <a href="<?php echo e(route('categories.create')); ?>" class="btn btn-primary">Add New Category</a>
    </div>
</div>

<div class="container">
    <div class="row">
        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo e($category->name); ?></h5>
                        <p class="card-text">Brand: <?php echo e($category->brand->name); ?></p>
                        <a href="<?php echo e(route('categories.edit', $category->id)); ?>" class="btn btn-primary">Edit</a>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('admin')): ?>
                        <form action="<?php echo e(route('categories.destroy', $category->id)); ?>" method="POST" onsubmit="return confirm('Are you sure?');" style="display:inline;">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/noureldinfarag/capstone_pos/resources/views/categories/index.blade.php ENDPATH**/ ?>