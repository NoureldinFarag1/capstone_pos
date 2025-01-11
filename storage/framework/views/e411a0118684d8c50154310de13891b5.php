<?php $__env->startSection('content'); ?>
<div class="container">
    <h2>Edit Brand</h2>

    <form action="<?php echo e(route('brands.update', $brand->id)); ?>" method="POST" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        <div class="mb-3">
            <label for="name">Brand Name:</label>
            <input type="text" name="name" id="name" class="form-control" value="<?php echo e($brand->name); ?>" required>
        </div>

        <div class="mb-3">
            <label for="picture">Brand Picture:</label>
            <input type="file" name="picture" id="picture" class="form-control">
            <?php if($brand->picture): ?>
                <img src="<?php echo e(asset('storage/'.$brand->picture)); ?>" alt="<?php echo e($brand->name); ?>" class="img-thumbnail mt-2" style="max-width: 200px;">
            <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-primary">Update Brand</button>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/noureldinfarag/capstone_pos/resources/views/brands/edit.blade.php ENDPATH**/ ?>