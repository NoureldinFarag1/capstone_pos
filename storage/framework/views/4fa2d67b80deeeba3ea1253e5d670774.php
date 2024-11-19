<?php $__env->startSection('content'); ?>
<h2>Edit Item</h2>

<form action="<?php echo e(route('items.update', $item->id)); ?>" method="POST" enctype="multipart/form-data">
    <?php echo csrf_field(); ?>
    <?php echo method_field('PUT'); ?>

    <!-- Item Name -->
    <div class="form-group">
        <label for="name">Item Name</label>
        <input type="text" name="name" class="form-control" value="<?php echo e($item->name); ?>" required>
    </div>

    <!-- Category -->
    <div class="form-group">
        <label for="category_id">Category</label>
        <select name="category_id" class="form-control">
            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($category->id); ?>" <?php echo e($item->category_id == $category->id ? 'selected' : ''); ?>>
                    <?php echo e($category->name); ?>

                </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>

    <!-- Brand -->
    <div class="form-group">
        <label for="brand_id">Brand</label>
        <select name="brand_id" class="form-control">
            <?php $__currentLoopData = $brands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($brand->id); ?>" <?php echo e($item->brand_id == $brand->id ? 'selected' : ''); ?>>
                    <?php echo e($brand->name); ?>

                </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>

    <!-- Price -->
    <div class="form-group">
        <label for="price">Price</label>
        <input type="number" name="price" class="form-control" value="<?php echo e($item->price); ?>" required>
    </div>

    <!-- Quantity -->
    <div class="form-group">
        <label for="quantity">Quantity</label>
        <input type="number" name="quantity" class="form-control" value="<?php echo e($item->quantity); ?>" required>
    </div>

    <!-- Optional: Picture Upload -->
    <div class="form-group">
        <label for="picture">Item Picture (optional)</label>
        <input type="file" name="picture" class="form-control">
    </div>

    <button type="submit" class="btn btn-primary">Update Item</button>
</form>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/noureldinfarag/capstone_pos/resources/views/items/edit.blade.php ENDPATH**/ ?>