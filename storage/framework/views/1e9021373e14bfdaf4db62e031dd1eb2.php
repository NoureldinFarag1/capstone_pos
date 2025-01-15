<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Product Sizes</h1>
        <a href="<?php echo e(route('sizes.create')); ?>" class="btn btn-primary">
            <i class="fas fa-plus mr-2"></i>Create New Size
        </a>
    </div>

    <!-- No Sizes Available Message -->
    <?php if($sizes->isEmpty()): ?>
        <div class="alert alert-info">
            No sizes have been created yet.
            <a href="<?php echo e(route('sizes.create')); ?>" class="alert-link">Create your first size</a>.
        </div>
    <?php else: ?>
        <!-- Product Sizes Table -->
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>Name</th>
                                <th>Type</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $sizes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $size): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($size->name); ?></td>
                                    <td><?php echo e(ucfirst($size->type)); ?></td>
                                    <td class="text-right">
                                        <div class="btn-group" role="group">
                                            <!-- Edit Button -->
                                            <a href="<?php echo e(route('sizes.edit', $size->id)); ?>" class="btn btn-warning btn-sm ml-2" title="Edit Size">
                                                <i class="fas fa-edit mr-1"></i>Edit
                                            </a>
                                            <!-- Delete Button -->
                                            <form action="<?php echo e(route('sizes.destroy', $size->id)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this size?');">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="btn btn-danger btn-sm ml-2" title="Delete Size">
                                                    <i class="fas fa-trash mr-1"></i>Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/noureldinfarag/capstone_pos/resources/views/sizes/index.blade.php ENDPATH**/ ?>