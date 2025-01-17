<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="card">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h2 class="mb-0">Create New Item</h2>
            <a href="<?php echo e(route('items.index')); ?>" class="btn btn-light">← Back to Items</a>
        </div>
        <div class="card-body">
            <form action="<?php echo e(route('items.store')); ?>" method="POST" enctype="multipart/form-data" id="createItemForm">
                <?php echo csrf_field(); ?>

                <!-- Basic Information Section -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Basic Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Item Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" required>
                                <div class="form-text">Enter a descriptive name for the item</div>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                                <select name="category_id" class="form-select" required>
                                    <option value="" selected disabled>Select Category</option>
                                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($category->id); ?>"><?php echo e($category->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="brand_id" class="form-label">Brand <span class="text-danger">*</span></label>
                                <select name="brand_id" class="form-select" required>
                                    <option value="" selected disabled>Select Brand</option>
                                    <?php $__currentLoopData = $brands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($brand->id); ?>"><?php echo e($brand->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="picture" class="form-label">Item Picture</label>
                            <div class="input-group">
                                <input type="file" name="picture" class="form-control" accept="image/*" id="pictureInput">
                                <label class="input-group-text" for="pictureInput">Browse</label>
                            </div>
                            <div id="imagePreview" class="mt-2 d-none">
                                <img src="" alt="Preview" class="img-thumbnail" style="max-height: 200px">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pricing Section -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Pricing Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="buying_price" class="form-label">Buying Price <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">EGP</span>
                                    <input type="number" name="buying_price" class="form-control" min="0" step="0.01" required>
                                </div>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="selling_price" class="form-label">Selling Price <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">EGP</span>
                                    <input type="number" name="selling_price" class="form-control" min="0" step="0.01" required>
                                </div>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="tax" class="form-label">Tax Rate <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" name="tax" class="form-control" min="0" max="100" required>
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="quantity" class="form-label">Initial Stock <span class="text-danger">*</span></label>
                                <input type="number" name="quantity" class="form-control" min="1" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="discount_type" class="form-label">Discount Type</label>
                                <select id="discount_type" name="discount_type" class="form-select">
                                    <option value="percentage">Percentage (%)</option>
                                    <option value="fixed">Fixed Amount (EGP)</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="discount_value" class="form-label">Discount Value</label>
                                <input type="number" id="discount_value" name="discount_value" class="form-control" min="0" required>
                                <div id="discountHelp" class="form-text"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Variants Section -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Item Variants</h5>
                    </div>
                    <div class="card-body">
                        <!-- Sizes -->
                        <div class="mb-4">
                            <label class="form-label">Available Sizes <span class="text-danger">*</span></label>
                            <div class="d-flex flex-wrap gap-2">
                                <?php $__currentLoopData = $sizes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $size): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="form-check">
                                        <input type="checkbox" name="sizes[]" value="<?php echo e($size->id); ?>"
                                               class="form-check-input" id="size<?php echo e($size->id); ?>">
                                        <label class="form-check-label px-3 py-2 border rounded-3"
                                               for="size<?php echo e($size->id); ?>">
                                            <?php echo e($size->name); ?>

                                        </label>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>

                        <!-- Colors -->
                        <div class="mb-3">
                            <label class="form-label">Available Colors</label>
                            <div class="d-flex flex-wrap gap-2">
                                <?php $__currentLoopData = $colors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $color): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="form-check">
                                        <input type="checkbox" name="colors[]" value="<?php echo e($color->id); ?>"
                                               class="form-check-input" id="color<?php echo e($color->id); ?>">
                                        <label class="form-check-label d-flex align-items-center gap-2"
                                               for="color<?php echo e($color->id); ?>">
                                            <span class="color-preview rounded-circle border"
                                                  style="width: 20px; height: 20px; background-color: <?php echo e($color->hex_code); ?>;"></span>
                                            <?php echo e($color->name); ?>

                                        </label>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>

                        <!-- Variant Quantities Preview -->
                        <div id="variantQuantities" class="mt-4 d-none">
                            <h6>Set Quantities for Each Variant</h6>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Variant</th>
                                            <th>Size</th>
                                            <th>Color</th>
                                            <th>Quantity</th>
                                        </tr>
                                    </thead>
                                    <tbody id="variantQuantitiesBody">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="<?php echo e(route('items.index')); ?>" class="btn btn-secondary px-4">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4">Create Item</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const pictureInput = document.getElementById('pictureInput');
    const imagePreview = document.getElementById('imagePreview');

    pictureInput.addEventListener('change', function(e) {
        if (e.target.files && e.target.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.querySelector('img').src = e.target.result;
                imagePreview.classList.remove('d-none');
            };
            reader.readAsDataURL(e.target.files[0]);
        }
    });

    const discountType = document.getElementById('discount_type');
    const discountValue = document.getElementById('discount_value');
    const discountHelp = document.getElementById('discountHelp');

    function updateDiscountHelp() {
        const type = discountType.value;
        const value = discountValue.value;
        if (type === 'percentage') {
            discountHelp.textContent = `${value}% will be deducted from the selling price`;
        } else {
            discountHelp.textContent = `EGP ${value} will be deducted from the selling price`;
        }
    }

    discountType.addEventListener('change', updateDiscountHelp);
    discountValue.addEventListener('input', updateDiscountHelp);

    const form = document.getElementById('createItemForm');
    form.addEventListener('submit', function(e) {
        const sizes = document.querySelectorAll('input[name="sizes[]"]:checked');
        if (sizes.length === 0) {
            e.preventDefault();
            alert('Please select at least one size');
        }
    });

    // Handle variant quantity preview
    function updateVariantQuantities() {
        const selectedSizes = Array.from(document.querySelectorAll('input[name="sizes[]"]:checked')).map(input => ({
            id: input.value,
            name: input.nextElementSibling.textContent.trim()
        }));

        const selectedColors = Array.from(document.querySelectorAll('input[name="colors[]"]:checked')).map(input => ({
            id: input.value,
            name: input.nextElementSibling.textContent.trim(),
            hex: input.nextElementSibling.querySelector('.color-preview').style.backgroundColor
        }));

        const variantQuantitiesDiv = document.getElementById('variantQuantities');
        const tbody = document.getElementById('variantQuantitiesBody');
        tbody.innerHTML = '';

        if (selectedSizes.length && selectedColors.length) {
            variantQuantitiesDiv.classList.remove('d-none');

            selectedColors.forEach(color => {
                selectedSizes.forEach(size => {
                    const tr = document.createElement('tr');
                    const variantName = document.querySelector('input[name="name"]').value;

                    tr.innerHTML = `
                        <td>${variantName}</td>
                        <td>${size.name}</td>
                        <td>
                            <span class="d-flex align-items-center gap-2">
                                <span class="color-preview rounded-circle" style="width: 15px; height: 15px; background-color: ${color.hex}"></span>
                                ${color.name}
                            </span>
                        </td>
                        <td>
                            <input type="number"
                                   name="variant_quantities[${size.id}][${color.id}]"
                                   class="form-control form-control-sm variant-quantity"
                                   style="width: 100px"
                                   min="0"
                                   value="0">
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
            });
        } else {
            variantQuantitiesDiv.classList.add('d-none');
        }
    }

    // Add event listeners for size and color checkboxes
    document.querySelectorAll('input[name="sizes[]"], input[name="colors[]"]').forEach(input => {
        input.addEventListener('change', updateVariantQuantities);
    });

    // Remove the original quantity input field since we're using variant quantities
    document.querySelector('input[name="quantity"]').closest('.col-md-3').remove();
});
</script>
<?php $__env->stopPush(); ?>

<style>
.form-check-label {
    cursor: pointer;
}

.form-check-input:checked + .form-check-label {
    background-color: #e9ecef;
    border-color: #0d6efd;
}

.color-preview {
    display: inline-block;
    border: 1px solid #dee2e6;
}
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/noureldinfarag/capstone_pos/resources/views/items/create.blade.php ENDPATH**/ ?>