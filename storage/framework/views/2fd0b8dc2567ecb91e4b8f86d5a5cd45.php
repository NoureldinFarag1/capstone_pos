<?php $__env->startSection('content'); ?>
<div class="container">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center my-4">
        <h1 class="fw-bold">Items</h1>
        <div>
            <a href="<?php echo e(route('items.create')); ?>" class="btn btn-primary">
                <i class="fas fa-plus-circle"></i> Add New Item
            </a>
            <button id="generateBarcodes" class="btn btn-secondary">
                <i class="fas fa-barcode"></i> Generate Barcodes
            </button>
        </div>
    </div>

    <!-- Filter and Export Section -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <!-- Search Bar -->
        <div class="input-group mb-3 me-2" style="max-width: 300px;">
            <span class="input-group-text bg-gradient-primary">
                <i class="fas fa-search text-gray-600"></i>
            </span>
            <form action="<?php echo e(route('items.index')); ?>" method="GET" class="d-flex flex-grow-1">
                <input type="text"
                       class="form-control"
                       id="itemSearch"
                       name="search"
                       placeholder="Search items..."
                       value="<?php echo e(request('search')); ?>">
                <input type="hidden" name="brand_id" value="<?php echo e(request('brand_id')); ?>">
                <input type="hidden" name="category_id" value="<?php echo e(request('category_id')); ?>">
            </form>
        </div>

        <!-- Export Dropdown -->
        <div class="dropdown me-3">
            <button class="btn btn-success dropdown-toggle" type="button" id="exportDropdown" data-bs-toggle="dropdown"
                aria-expanded="false">
                <i class="fas fa-file-export"></i> Export Items
            </button>
            <ul class="dropdown-menu" aria-labelledby="exportDropdown">
                <li><a class="dropdown-item" href="<?php echo e(route('items.export')); ?>">All Brands</a></li>
                <?php $__currentLoopData = $brands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><a class="dropdown-item"
                            href="<?php echo e(route('items.export', ['brand_id' => $brand->id])); ?>"><?php echo e($brand->name); ?></a></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>

        <!-- Filter Form -->
        <form action="<?php echo e(route('items.index')); ?>" method="GET" class="d-flex">
            <div class="input-group">
                <select name="brand_id" class="form-select">
                    <option value="">All Brands</option>
                    <?php $__currentLoopData = $brands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($brand->id); ?>" <?php echo e(request('brand_id') == $brand->id ? 'selected' : ''); ?>>
                            <?php echo e($brand->name); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <button type="submit" class="btn btn-secondary">
                    <i class="fas fa-filter"></i> Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Items Grid -->
    <div class="row g-4">
        <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if($item->is_parent): ?>
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <a href="<?php echo e(route('items.show', $item->id)); ?>" class="text-decoration-none text-reset">
                                    <h5 class="card-title fw-bold m-0"><?php echo e($item->name); ?> - <?php echo e($item->brand->name); ?></h5>
                                </a>
                                <?php if($item->quantity <= 0): ?>
                                    <div class="stock-badge">
                                        <span class="badge bg-danger">Out of Stock</span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="item-details">
                                <div class="price-info mb-3">
                                    <p class="mb-1 d-flex justify-content-between">
                                        <span class="text-muted">Base Price:</span>
                                        <span class="fw-bold">EGP<?php echo e(number_format($item->selling_price, 2)); ?></span>
                                    </p>
                                    <?php if($item->discount_type === 'percentage'): ?>
                                        <p class="mb-1 d-flex justify-content-between">
                                            <span class="text-muted">Discount:</span>
                                            <span class='text-danger'><?php echo e($item->discount_value); ?>% OFF</span>
                                        </p>
                                    <?php elseif($item->discount_type === 'fixed'): ?>
                                        <p class="mb-1 d-flex justify-content-between">
                                            <span class="text-muted">Discount:</span>
                                            <span class='text-danger'>EGP<?php echo e(number_format($item->discount_value, 2)); ?> OFF</span>
                                    <?php endif; ?>
                                    <p class="mb-1 d-flex justify-content-between">
                                        <span class="text-muted">Final Price:</span>
                                        <span class="fw-bold">EGP<?php echo e(number_format($item->priceAfterSale(), 2)); ?></span>
                                    </p>
                                </div>
                                <p class="mb-0 d-flex justify-content-between align-items-center">
                                    <span class="text-muted">Stock:</span>
                                    <span class="fw-medium"><?php echo e($item->quantity); ?> units</span>
                                </p>
                            </div>

                            <div class="mt-auto pt-3 border-top d-flex justify-content-between">
                                <a href="<?php echo e(route('items.edit', $item->id)); ?>" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('admin')): ?>
                                    <form action="<?php echo e(route('items.destroy', $item->id)); ?>" method="POST" class="delete-item-form">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                            <i class="fas fa-trash-alt"></i> Delete
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        <?php echo e($items->links()); ?>

    </div>
</div>

<?php $__env->startPush('scripts'); ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Update search functionality to submit form on input
            const searchInput = document.getElementById('itemSearch');
            let timeout = null;

            if (searchInput) {
                searchInput.addEventListener('input', function () {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => {
                        this.closest('form').submit();
                    }, 500);
                });
            }

            // Existing delete confirmation
            document.querySelectorAll('.delete-item-form').forEach(form => {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();

                    Swal.fire({
                        title: 'Are you sure?',
                        text: "This will delete the item and all its variants. You won't be able to revert this!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });

            // Generate barcodes
            document.getElementById('generateBarcodes').addEventListener('click', function() {
                fetch('<?php echo e(route('items.generateBarcodes')); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                    }
                }).then(response => response.json())
                  .then(data => {
                      if (data.success) {
                          Swal.fire({
                              title: 'Success!',
                              text: 'Barcodes generated successfully!',
                              icon: 'success',
                              confirmButtonText: 'OK'
                          });
                      } else {
                          Swal.fire({
                              title: 'Error!',
                              text: 'Failed to generate barcodes: ' + data.error,
                              icon: 'error',
                              confirmButtonText: 'OK'
                          });
                      }
                  });
            });
        });
    </script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('styles'); ?>
    <style>
        .color-preview {
            display: inline-block;
            border: 1px solid #dee2e6;
        }
    </style>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/noureldinfarag/capstone_pos/resources/views/items/index.blade.php ENDPATH**/ ?>