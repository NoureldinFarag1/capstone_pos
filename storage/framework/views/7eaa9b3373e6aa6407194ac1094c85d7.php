<?php $__env->startSection('content'); ?>
<form action="<?php echo e(route('brands.store')); ?>" method="POST" enctype="multipart/form-data">
    <?php echo csrf_field(); ?>
    <div class="form-group">
        <label for="name">Brand Name</label>
        <input type="text" name="name" class="form-control" required autofocus>
    </div>
    <div class="form-group">
        <label for="picture">Brand Picture</label>
        <input type="file" name="picture" class="form-control" accept="image/*" required>
    </div>
    <button type="submit" class="btn btn-primary mt-2">Add Brand</button>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/noureldinfarag/capstone_pos/resources/views/brands/create.blade.php ENDPATH**/ ?>