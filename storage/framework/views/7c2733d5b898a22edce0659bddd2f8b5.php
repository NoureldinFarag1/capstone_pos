<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="card mt-4 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h2 class="mb-0">Create New Item</h2>
        </div>
        <div class="card-body">
            <form action="<?php echo e(route('items.store')); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>

                <div class="row">
                    <!-- Item Name -->
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Item Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="Enter item name" required>
                    </div>

                    <!-- Category -->
                    <div class="col-md-6 mb-3">
                        <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                        <select name="category_id" class="form-select" required>
                            <option value="" selected disabled>Choose a category</option>
                            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($category->id); ?>"><?php echo e($category->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <!-- Brand -->
                    <div class="col-md-6 mb-3">
                        <label for="brand_id" class="form-label">Brand <span class="text-danger">*</span></label>
                        <select name="brand_id" class="form-select" required>
                            <option value="" selected disabled>Choose a brand</option>
                            <?php $__currentLoopData = $brands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($brand->id); ?>"><?php echo e($brand->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <!-- Buying Price -->
                    <div class="col-md-6 mb-3">
                        <label for="buying_price" class="form-label">Buying Price <span class="text-danger">*</span></label>
                        <input type="number" name="buying_price" class="form-control" placeholder="Enter buying price" min="0" required>
                    </div>
                </div>

                <div class="row">
                    <!-- Selling Price -->
                    <div class="col-md-6 mb-3">
                        <label for="selling_price" class="form-label">Selling Price <span class="text-danger">*</span></label>
                        <input type="number" name="selling_price" class="form-control" placeholder="Enter selling price" min="0" required>
                    </div>

                    <!-- Tax -->
                    <div class="col-md-6 mb-3">
                        <label for="tax" class="form-label">Tax (%) <span class="text-danger">*</span></label>
                        <input type="number" name="tax" class="form-control" placeholder="Enter tax percentage" min="0" required>
                    </div>
                </div>

                <div class="row">
                    <!-- Discount Type and Value -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="discount_type" class="form-label">Discount Type</label>
                            <select id="discount_type" name="discount_type" class="form-select">
                                <option value="percentage">Percentage</option>
                                <option value="fixed">Fixed Amount</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="discount_value" class="form-label">Discount Value</label>
                            <input type="number" id="discount_value" name="discount_value" class="form-control" min="0" required>
                        </div>
                    </div>

                    <!-- Quantity -->
                    <div class="col-md-6 mb-3">
                        <label for="quantity" class="form-label">Quantity <span class="text-danger">*</span></label>
                        <input type="number" name="quantity" class="form-control" placeholder="Enter item quantity" min="1" required>
                    </div>
                </div>

                <!-- Picture -->
                <div class="mb-3">
                    <label for="picture" class="form-label">Item Picture</label>
                    <input type="file" name="picture" class="form-control" accept="image/*">
                </div>

                <!-- Sizes -->
                <div class="mb-3">
                    <label for="sizes" class="form-label">Select Sizes</label>
                    <div class="d-flex flex-wrap">
                        <?php $__currentLoopData = $sizes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $size): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="form-check m-2">
                                <input type="checkbox" name="sizes[]" value="<?php echo e($size->id); ?>" class="form-check-input" id="size<?php echo e($size->id); ?>">
                                <label class="form-check-label" for="size<?php echo e($size->id); ?>">
                                    <?php echo e($size->name); ?> (<?php echo e($size->type); ?>)
                                </label>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>

                <div class="w-full max-w-md">
                    <label for="colors" class="block text-sm font-medium text-gray-700 mb-2">
                        Select Colors
                    </label>
                    <select
                        name="colors[]"
                        id="colors"
                        multiple
                        class="tom-select-colors w-full"
                    >
                        <?php $__currentLoopData = $colors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $color): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option
                                value="<?php echo e($color->id); ?>"
                                data-color="<?php echo e($color->hex_code); ?>"
                            >
                                <?php echo e($color->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <!-- Submit Button -->
                <div class="text-end">
                    <button type="submit" class="btn btn-success px-4">Add Item</button>
                    <a href="<?php echo e(route('items.index')); ?>" class="btn btn-secondary px-4">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        new TomSelect('#colors', {
            plugins: ['remove_button', 'checkbox_options'],
            render: {
                option: function(data, escape) {
                    return `
                        <div class="flex items-center">
                            <div
                                class="w-6 h-6 mr-2 rounded-full"
                                style="background-color: ${escape(data.color)};"
                            ></div>
                            <span>${escape(data.text)}</span>
                        </div>
                    `;
                },
                item: function(data, escape) {
                    return `
                        <div class="flex items-center">
                            <div
                                class="w-5 h-5 mr-2 rounded-full"
                                style="background-color: ${escape(data.color)};"
                            ></div>
                            <span>${escape(data.text)}</span>
                        </div>
                    `;
                }
            }
        });
    });
    </script>

<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/noureldinfarag/capstone_pos/resources/views/items/create.blade.php ENDPATH**/ ?>