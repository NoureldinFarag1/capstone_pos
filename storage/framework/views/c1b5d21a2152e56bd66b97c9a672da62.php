<?php $__env->startSection('content'); ?>
    <h1>Edit Size</h1>
    <form action="<?php echo e(route('sizes.update', $size->id)); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>
        <div class="form-group">
            <label for="name">Size Name</label>
            <input type="text" name="name" id="name" class="form-control" value="<?php echo e($size->name); ?>" required>
        </div>
        <div class="form-group">
            <label for="type">Size Type</label>
            <select name="type" id="type" class="form-control" required>
                <option value="clothes" <?php echo e($size->type == 'clothes' ? 'selected' : ''); ?>>Clothes</option>
                <option value="shoes" <?php echo e($size->type == 'shoes' ? 'selected' : ''); ?>>Shoes</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, update it!'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/noureldinfarag/capstone_pos/resources/views/sizes/edit.blade.php ENDPATH**/ ?>