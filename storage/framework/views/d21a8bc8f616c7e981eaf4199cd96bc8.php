<?php $__env->startSection('content'); ?>
<div class="container">
    <h1>Add Color</h1>
    <form action="<?php echo e(route('colors.store')); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <div class="form-group">
            <label for="name">Color Name</label>
            <input type="text" name="name" id="name" class="form-control" required autofocus>
        </div>
        <button type="submit" class="btn btn-success mt-3">Add Color</button>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/noureldinfarag/capstone_pos/resources/views/colors/create.blade.php ENDPATH**/ ?>