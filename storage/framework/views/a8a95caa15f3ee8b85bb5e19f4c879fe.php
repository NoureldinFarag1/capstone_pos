<?php $__env->startSection('content'); ?>
<div class="container">
    <h1>Colors</h1>
    <a href="<?php echo e(route('colors.create')); ?>" class="btn btn-primary">Add Color</a>
    <?php if(session('success')): ?>
        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $colors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $color): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($color->id); ?></td>
                    <td><?php echo e($color->name); ?></td>
                    <td>
                        <a href="<?php echo e(route('colors.edit', $color)); ?>" class="btn btn-warning">Edit</a>
                        <form action="<?php echo e(route('colors.destroy', $color)); ?>" method="POST" style="display:inline;">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/noureldinfarag/capstone_pos/resources/views/colors/index.blade.php ENDPATH**/ ?>