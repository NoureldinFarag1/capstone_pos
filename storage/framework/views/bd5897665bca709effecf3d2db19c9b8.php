<?php $__env->startSection('content'); ?>
    <h1>Create New Size</h1>
    <form action="<?php echo e(route('sizes.store')); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <div class="form-group">
            <label for="name">Size Name</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="type">Size Type</label>
            <select name="type" id="type" class="form-control" required>
                <option value="clothes">Clothes</option>
                <option value="shoes">Shoes</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success">Create</button>
    </form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/noureldinfarag/capstone_pos/resources/views/sizes/create.blade.php ENDPATH**/ ?>