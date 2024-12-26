<?php $__env->startSection('content'); ?>
<div class="container">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center my-4">
        <h1 class="fw-bold">Items</h1>
        <a href="<?php echo e(route('items.create')); ?>" class="btn btn-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="Add new item to the stock">
            <i class="fas fa-plus-circle"></i> Add New Item
          </a>
    </div>

    <!-- Filter and Export Section -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <!-- Export Dropdown -->
        <div class="dropdown me-3">
            <button class="btn btn-success dropdown-toggle" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-file-export"></i> Export Items
            </button>
            <ul class="dropdown-menu" aria-labelledby="exportDropdown">
                <li><a class="dropdown-item" href="<?php echo e(route('items.export')); ?>">All Brands</a></li>
                <?php $__currentLoopData = $brands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><a class="dropdown-item" href="<?php echo e(route('items.export', ['brand_id' => $brand->id])); ?>"><?php echo e($brand->name); ?></a></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>

        <!-- Filter Form -->
        <form action="<?php echo e(route('items.index')); ?>" method="GET" class="d-flex">
            <select name="brand_id" class="form-select form-select me-2">
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
        </form>
    </div>
    <!-- Modern Pagination Links -->
    <div class="d-flex justify-content-center mt-4">
        <nav aria-label="Page navigation">
            <ul class="pagination">
                <!-- Previous Page Link -->
                <?php if($items->onFirstPage()): ?>
                    <li class="page-item disabled">
                        <span class="page-link">Previous</span>
                    </li>
                <?php else: ?>
                    <li class="page-item">
                        <a class="page-link" href="<?php echo e($items->previousPageUrl()); ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                <?php endif; ?>

                <!-- Page Number Links -->
                <?php $__currentLoopData = $items->getUrlRange(1, $items->lastPage()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li class="page-item <?php echo e($page == $items->currentPage() ? 'active' : ''); ?>">
                        <a class="page-link" href="<?php echo e($url); ?>"><?php echo e($page); ?></a>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <!-- Next Page Link -->
                <?php if($items->hasMorePages()): ?>
                    <li class="page-item">
                        <a class="page-link" href="<?php echo e($items->nextPageUrl()); ?>" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                <?php else: ?>
                    <li class="page-item disabled">
                        <span class="page-link">Next</span>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
    <!-- Items List -->
    <div class="row g-4">
        <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-lg-4 col-md-6 col-sm-12">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body d-flex flex-column">
                        <a href="<?php echo e(route('items.show', $item->id)); ?>" class="text-decoration-none text-dark">
                            <h5 class="card-title fw-bold mb-3"><?php echo e($item->name); ?></h5>
                        </a>
                        <div class="mb-2">
                            <p class="mb-1">Price: <span class="fw-bold">$<?php echo e(number_format($item->selling_price, 2)); ?></span></p>
                            <?php if($item->discount_type === 'percentage'): ?>
                                <p class="mb-1 text-muted">Sale: <span class="fw-bold"><?php echo e($item->discount_value); ?>%</span></p>
                            <?php else: ?>
                                <p class="mb-1 text-muted">Sale: <span class="fw-bold">$<?php echo e($item->discount_value); ?></span></p>
                            <?php endif; ?>
                            <p class="mb-1">Total Amount: <span class="fw-bold">$<?php echo e(number_format($item->PriceAfterSale(), 2)); ?></span></p>
                            <p class="mb-1">Quantity: <span class="fw-bold"><?php echo e($item->quantity); ?></span></p>
                        </div>
                        <p class="mt-2">
                            Size:
                            <?php $__currentLoopData = $item->sizes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $size): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <span class="badge bg-secondary"><?php echo e($size->name); ?></span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </p>
                        <p class="mt-2">
                            Color:
                            <?php $__currentLoopData = $item->colors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $color): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <span class="badge bg-secondary"><?php echo e($color->name); ?></span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </p>
                        <div class="mt-auto d-flex justify-content-between">
                            <a href="<?php echo e(route('items.edit', $item->id)); ?>" class="btn btn-outline-primary btn-sm" aria-label="Edit item">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('admin')): ?>
                            <form action="<?php echo e(route('items.destroy', $item->id)); ?>" method="POST" onsubmit="return confirm('Are you sure you want to delete this item?');" aria-label="Delete item">
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
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <!-- Modern Pagination Links -->
    <div class="d-flex justify-content-center mt-4">
        <nav aria-label="Page navigation">
            <ul class="pagination">
                <!-- Previous Page Link -->
                <?php if($items->onFirstPage()): ?>
                    <li class="page-item disabled">
                        <span class="page-link">Previous</span>
                    </li>
                <?php else: ?>
                    <li class="page-item">
                        <a class="page-link" href="<?php echo e($items->previousPageUrl()); ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                <?php endif; ?>

                <!-- Page Number Links -->
                <?php $__currentLoopData = $items->getUrlRange(1, $items->lastPage()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li class="page-item <?php echo e($page == $items->currentPage() ? 'active' : ''); ?>">
                        <a class="page-link" href="<?php echo e($url); ?>"><?php echo e($page); ?></a>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <!-- Next Page Link -->
                <?php if($items->hasMorePages()): ?>
                    <li class="page-item">
                        <a class="page-link" href="<?php echo e($items->nextPageUrl()); ?>" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                <?php else: ?>
                    <li class="page-item disabled">
                        <span class="page-link">Next</span>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/noureldinfarag/capstone_pos/resources/views/items/index.blade.php ENDPATH**/ ?>