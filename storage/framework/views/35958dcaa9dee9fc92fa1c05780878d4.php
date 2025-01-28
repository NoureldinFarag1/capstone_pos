<?php $__env->startSection('content'); ?>
    <div class="container">
        <h2>Edit Category</h2>

        <form action="<?php echo e(route('categories.update', $category->id)); ?>" method="POST" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div class="form-group">
                <label for="name">Category Name:</label>
                <input type="text" name="name" id="name" class="form-control" value="<?php echo e($category->name); ?>" required>
            </div>

            <div class="form-group">
                <label for="brand_id">Brand:</label>
                <select name="brand_id" id="brand_id" class="form-control" required>
                    <?php $__currentLoopData = $brands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($brand->id); ?>" <?php echo e($brand->id == $category->brand_id ? 'selected' : ''); ?>>
                            <?php echo e($brand->name); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div class="form-group">
                <label for="picture" class="mt-3">Category Picture:</label>
                <input type="file" name="picture" id="picture" class="form-control-file">
                <?php if($category->picture): ?>
                    <img src="<?php echo e(asset('storage/'.$category->picture)); ?>" alt="<?php echo e($category->name); ?>" class="img-thumbnail" width="500">
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-primary mt-2">Update Category</button>
        </form>
    </div>
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

<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH E:\LocalHub POS\capstone_pos\resources\views/categories/edit.blade.php ENDPATH**/ ?>