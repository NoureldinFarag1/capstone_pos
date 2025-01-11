<?php $__env->startSection('content'); ?>
<br>
<div class="container">
    <h2 class="mb-4 text-center">Edit Item</h2>

    <form action="<?php echo e(route('items.update', $item->id)); ?>" method="POST" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        <div class="row">
            <!-- Left Column -->
            <div class="col-md-6">
                <div class="form-group mb-4">
                    <label for="name" class="form-label">Item Name</label>
                    <input type="text" name="name" id="name" class="form-control" value="<?php echo e($item->name); ?>" required>
                </div>

                <div class="form-group mb-4">
                    <label for="category_id" class="form-label">Category</label>
                    <select name="category_id" id="category_id" class="form-select">
                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($category->id); ?>" <?php echo e($item->category_id == $category->id ? 'selected' : ''); ?>>
                                <?php echo e($category->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div class="form-group mb-4">
                    <label for="brand_id" class="form-label">Brand</label>
                    <select name="brand_id" id="brand_id" class="form-select">
                        <?php $__currentLoopData = $brands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($brand->id); ?>" <?php echo e($item->brand_id == $brand->id ? 'selected' : ''); ?>>
                                <?php echo e($brand->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>

            <!-- Right Column -->
            <div class="col-md-6">
                <div class="form-group mb-4">
                    <label for="selling_price" class="form-label">Price</label>
                    <input type="number" name="selling_price" id="selling_price" class="form-control" value="<?php echo e($item->selling_price); ?>" required>
                </div>

                <div class="form-group mb-4">
                    <label for="quantity" class="form-label">Quantity</label>
                    <input type="number" name="quantity" id="quantity" class="form-control" value="<?php echo e($item->quantity); ?>" required>
                </div>

                <!-- Discount Type and Value -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="discount_type" class="form-label">Discount Type</label>
                        <select id="discount_type" name="discount_type" class="form-select">
                            <option value="percentage" <?php echo e(old('discount_type', $item->discount_type) === 'percentage' ? 'selected' : ''); ?>>Percentage</option>
                            <option value="fixed" <?php echo e(old('discount_type', $item->discount_type) === 'fixed' ? 'selected' : ''); ?>>Fixed Amount</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="discount_value" class="form-label">Discount Value</label>
                        <input type="number" id="discount_value" name="discount_value" class="form-control" value="<?php echo e(old('discount_value', $item->discount_value)); ?>" min="0" required>
                    </div>
                </div>
            </div>
        </div>

        <!-- Picture Upload -->
        <div class="form-group mb-4">
            <label for="picture" class="form-label">Item Picture (optional)</label>
            <input type="file" name="picture" id="picture" class="form-control">
            <?php if($item->picture): ?>
                <div class="mt-3">
                    <img src="<?php echo e(asset('storage/' . $item->picture)); ?>" alt="<?php echo e($item->name); ?>" class="img-thumbnail" style="max-width: 200px;">
                </div>
            <?php endif; ?>
        </div>

        <!-- Sizes -->
        <div class="form-group mb-4">
            <label class="form-label">Sizes</label>
            <div class="row">
                <?php $__currentLoopData = $sizes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $size): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-md-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="sizes[]" id="size-<?php echo e($size->id); ?>" value="<?php echo e($size->id); ?>"
                                <?php echo e($item->sizes->contains($size->id) ? 'checked' : ''); ?>>
                            <label class="form-check-label" for="size-<?php echo e($size->id); ?>">
                                <?php echo e($size->name); ?>

                            </label>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        <!-- Buttons -->
        <div class="d-flex justify-content-between">
            <a href="<?php echo e(route('items.index')); ?>" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back
            </a>
            <button type="submit" class="btn btn-success">
                <i class="bi bi-save"></i> Update Item
            </button>
        </div>
    </form>

    <!-- Error Messages -->
    <?php if($errors->any()): ?>
        <div class="alert alert-danger mt-4">
            <ul>
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/noureldinfarag/capstone_pos/resources/views/items/edit.blade.php ENDPATH**/ ?>