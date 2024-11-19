<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="<?php echo e(asset('css/custom.css')); ?>">
    <title>Admin Panel - local HUB</title>
    <link rel="stylesheet" href="<?php echo e(asset('css/app.css')); ?>">
    <link rel="icon" href="<?php echo e(asset('images/favicon.ico')); ?>" type="image/x-icon">
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?php echo e(route('dashboard')); ?>">local HUB</a>
                <img src="<?php echo e(asset('images/logo.png')); ?>" alt="Capstone Logo" style="width: 150px; height: auto;">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo e(route('brands.index')); ?>">Brands</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo e(route('categories.index')); ?>">Categories</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo e(route('items.index')); ?>">Items</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo e(route('sales.index')); ?>">Sales</a>
                    </li>
                </ul>
            </div>
            <!-- Low Stock Notification Dropdown -->
            <?php if(isset($lowStockItems) && $lowStockItems->isNotEmpty()): ?>
                <li class="nav-item dropdown me-3"> <!-- Adding margin-right to separate elements -->
                    <a class="nav-link" href="#" id="lowStockDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-bell"></i>
                        <?php if(count($lowStockItems) > 0): ?>
                            <span class="badge badge-danger"><?php echo e(count($lowStockItems)); ?></span>
                        <?php endif; ?>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="lowStockDropdown">
                        <h6 class="dropdown-header">Low Stock Alerts</h6>
                        <?php if(count($lowStockItems) > 0): ?>
                            <?php $__currentLoopData = $lowStockItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <a class="dropdown-item" href="<?php echo e(route('items.show', $item->id)); ?>">
                                    <?php echo e($item->name); ?> (Stock: <?php echo e($item->quantity); ?>)
                                </a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php else: ?>
                            <span class="dropdown-item text-muted">No low-stock items</span>
                        <?php endif; ?>
                    </div>
                </li>
                <?php endif; ?>
        </div>
         <!-- Filter dropdown menu -->
         <div class="dropdown me-3"> <!-- Margin added to move to the left -->
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        Filter Items
                    </button>
                    <form action="<?php echo e(route('items.index')); ?>" method="GET">
                        <ul class="dropdown-menu dropdown-menu-end p-3" aria-labelledby="filterDropdown" style="width: 300px;">
                            <!-- Brand Filter Section -->
                            <li class="dropdown-header">Filter by Brand</li>
                            <?php $__currentLoopData = $brands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="dropdown-item">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="brand" value="<?php echo e($brand->id); ?>" id="brand<?php echo e($brand->id); ?>"
                                        <?php echo e(request('brand') == $brand->id ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="brand<?php echo e($brand->id); ?>">
                                        <?php echo e($brand->name); ?>

                                    </label>
                                </div>
                            </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            <li><hr class="dropdown-divider"></li>

                            <!-- Category Filter Section -->
                            <li class="dropdown-header">Filter by Category</li>
                            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="dropdown-item">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="category" value="<?php echo e($category->id); ?>" id="category<?php echo e($category->id); ?>"
                                        <?php echo e(request('category') == $category->id ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="category<?php echo e($category->id); ?>">
                                        <?php echo e($category->name); ?>

                                    </label>
                                </div>
                            </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>

                        <div class="text-center mt-2">
                            <button type="submit" class="btn btn-primary">Apply Filter</button>
                        </div>
                    </form>
                </div>

       <!-- Logout Button -->
         <form action="<?php echo e(route('logout')); ?>" method="POST" style="display: inline;">
            <?php echo csrf_field(); ?>
            <button type="submit" class="btn btn-danger">Logout</button>
         </form>
    </nav>

    <div class="container">
        <?php echo $__env->yieldContent('content'); ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php /**PATH /Users/noureldinfarag/capstone_pos/resources/views/layouts/dashboard.blade.php ENDPATH**/ ?>